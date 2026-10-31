<?php
namespace Tests\Unitarias;

use PHPUnit\Framework\TestCase;

/**
 * Contrato del sistema de comentarios de torneos.
 *
 * Estas pruebas son estáticas: leen el esquema SQL, el modelo, el enrutador y
 * la vista pública, y no se conectan a la base de datos ni levantan el
 * servidor. Verifican el cableado de la funcionalidad y fallan cuando alguien
 * cambia una pieza sin actualizar el contrato. El comportamiento real (listar,
 * publicar, paginar, ocultar) se cubre en tests/integracion.
 */
class ComentariosTorneoTest extends TestCase {

    private const RUTA_ESQUEMA = __DIR__ . '/../../base_de_datos/migraciones/001_ascend.sql';
    private const RUTA_SEED_QA = __DIR__ . '/../../base_de_datos/migraciones/003_ascend_seed_relacional_qa.sql';
    private const RUTA_SEED_AMPLIADA = __DIR__ . '/../../base_de_datos/migraciones/003_ascend_seed_relacional_qa.sql';
    private const RUTA_MODELO = __DIR__ . '/../../codigo_fuente/modelos/Torneo.php';
    private const RUTA_CONTROLADOR = __DIR__ . '/../../codigo_fuente/controladores/TorneoControlador.php';
    private const RUTA_VISTA = __DIR__ . '/../../codigo_fuente/vistas/publico/detalle-torneo.php';
    private const RUTA_ENTRADA = __DIR__ . '/../../codigo_fuente/publico/index.php';
    private const RUTA_POLITICA = __DIR__ . '/../../codigo_fuente/vistas/publico/nosotros.php';

    private function leer(string $ruta): string {
        $this->assertFileExists($ruta, 'Falta el archivo que respalda el contrato: ' . $ruta);
        return (string) file_get_contents($ruta);
    }

    /** Devuelve el cuerpo del CREATE TABLE torneo_comentarios. */
    private function cuerpoDeLaTabla(): string {
        $sql = $this->leer(self::RUTA_ESQUEMA);
        $patron = '/CREATE TABLE torneo_comentarios \((.*?)\) ENGINE = InnoDB;/s';
        $this->assertSame(
            1,
            preg_match($patron, $sql, $coincidencias),
            'El esquema base ya no declara CREATE TABLE torneo_comentarios.'
        );
        return $coincidencias[1];
    }

    /** Devuelve las líneas de columna (nombre => definición) de la tabla. */
    private function columnas(): array {
        $cuerpo = $this->cuerpoDeLaTabla();
        preg_match_all('/^\s{4}([a-z_]+)\s+([A-Z]+(?:\([0-9]+\))?)(.*)$/m', $cuerpo, $filas, PREG_SET_ORDER);

        $columnas = [];
        foreach ($filas as $fila) {
            $columnas[$fila[1]] = $fila[2] . $fila[3];
        }
        return $columnas;
    }

    private function seccionComentarios(): string {
        $vista = $this->leer(self::RUTA_VISTA);
        $inicio = strpos($vista, '<section class="td-comentarios"');
        $fin = strpos($vista, '</section>', $inicio);
        $this->assertNotFalse($inicio, 'Desapareció la sección de comentarios.');
        $this->assertNotFalse($fin, 'La sección de comentarios no cierra.');
        return substr($vista, $inicio, $fin - $inicio);
    }

    private function seccionFormulario(): string {
        $vista = $this->leer(self::RUTA_VISTA);
        $inicio = strpos($vista, '<section class="td-formulario-comentarios"');
        $fin = strpos($vista, '</section>', $inicio);
        $this->assertNotFalse($inicio, 'Desapareció el formulario de comentarios.');
        $this->assertNotFalse($fin, 'El formulario de comentarios no cierra.');
        return substr($vista, $inicio, $fin - $inicio);
    }

    public function test_el_esquema_declara_la_tabla_de_comentarios(): void {
        $cuerpo = $this->cuerpoDeLaTabla();
        $this->assertStringContainsString('AUTO_INCREMENT PRIMARY KEY', $cuerpo);
        $this->assertStringContainsString('ENGINE = InnoDB', $this->leer(self::RUTA_ESQUEMA));
    }

    public function test_la_tabla_conserva_las_ocho_columnas_previstas_en_orden(): void {
        $this->assertSame(
            ['id', 'torneo_id', 'usuario_id', 'nombre_autor', 'email_autor', 'comentario', 'aprobado', 'fecha_hora'],
            array_keys($this->columnas()),
            'Cambió el conjunto o el orden de columnas de torneo_comentarios.'
        );
    }

    public function test_solo_el_autor_vinculado_admite_nulo(): void {
        $columnas = $this->columnas();

        $this->assertStringContainsString('INT NULL', $columnas['usuario_id'], 'usuario_id debe aceptar NULL para conservar el comentario si se borra la cuenta.');
        $this->assertStringNotContainsString('NOT NULL', $columnas['usuario_id']);

        foreach (['torneo_id', 'nombre_autor', 'email_autor', 'comentario'] as $obligatoria) {
            $this->assertStringContainsString('NOT NULL', $columnas[$obligatoria], $obligatoria . ' debe ser obligatoria.');
        }
    }

    public function test_aprobado_filtra_la_visibilidad_publica(): void {
        $columnas = $this->columnas();
        $this->assertStringContainsString('DEFAULT TRUE', strtoupper($columnas['aprobado']));

        $modelo = $this->leer(self::RUTA_MODELO);
        $this->assertStringContainsString('c.aprobado = 1', $modelo, 'El listado público debe filtrar por aprobado.');
    }

    public function test_fecha_hora_se_registra_automaticamente(): void {
        $columnas = $this->columnas();
        $this->assertStringContainsString('TIMESTAMP', $columnas['fecha_hora']);
        $this->assertStringContainsString('DEFAULT CURRENT_TIMESTAMP', $columnas['fecha_hora']);
    }

    public function test_las_claves_foraneas_estan_definidas_con_su_politica_de_borrado(): void {
        $cuerpo = $this->cuerpoDeLaTabla();

        $this->assertMatchesRegularExpression(
            '/CONSTRAINT fk_comentario_torneo FOREIGN KEY \(torneo_id\) REFERENCES torneos \(id\) ON DELETE CASCADE ON UPDATE CASCADE/',
            $cuerpo
        );
        $this->assertMatchesRegularExpression(
            '/CONSTRAINT fk_comentario_usuario FOREIGN KEY \(usuario_id\) REFERENCES usuarios \(id\) ON DELETE SET NULL ON UPDATE CASCADE/',
            $cuerpo
        );
    }

    public function test_el_borrado_administrativo_del_torneo_incluye_los_comentarios(): void {
        $modelo = $this->leer(self::RUTA_MODELO);

        $inicio = strpos($modelo, 'public function eliminarPorAdmin');
        $fin = strpos($modelo, 'public function actualizarPorAdmin');
        $posicion = strpos($modelo, "'torneo_comentarios'");

        $this->assertNotFalse($inicio, 'No se encontró eliminarPorAdmin().');
        $this->assertNotFalse($fin, 'No se encontró el método que sigue a eliminarPorAdmin().');
        $this->assertNotFalse($posicion, 'La lista de tablas dependientes perdió torneo_comentarios.');
        $this->assertGreaterThan($inicio, $posicion);
        $this->assertLessThan($fin, $posicion, 'torneo_comentarios debe borrarse dentro de eliminarPorAdmin().');
    }

    public function test_el_acceso_a_la_tabla_vive_en_el_modelo(): void {
        $modelo = $this->leer(self::RUTA_MODELO);
        $controlador = $this->leer(self::RUTA_CONTROLADOR);
        $vista = $this->leer(self::RUTA_VISTA);

        $this->assertStringContainsString('FROM torneo_comentarios', $modelo);
        $this->assertStringContainsString('INSERT INTO torneo_comentarios', $modelo);
        $this->assertStringContainsString('ORDER BY c.fecha_hora DESC, c.id DESC', $modelo);
        $this->assertStringNotContainsString('torneo_comentarios', $controlador, 'El controlador no debe escribir SQL.');
        $this->assertStringNotContainsString('torneo_comentarios', $vista, 'La vista no debe consultar la tabla.');
    }

    public function test_el_modelo_expone_las_cuatro_operaciones_del_listado(): void {
        $modelo = $this->leer(self::RUTA_MODELO);

        foreach (['obtenerComentariosPublicos', 'contarComentariosPublicos', 'crearComentario', 'contarComentariosRecientes'] as $metodo) {
            $this->assertMatchesRegularExpression(
                '/public function ' . $metodo . '\(/',
                $modelo,
                'Falta el método ' . $metodo . '().'
            );
        }
    }

    public function test_el_enrutador_expone_la_accion_de_comentarios(): void {
        $entrada = $this->leer(self::RUTA_ENTRADA);

        $this->assertSame(
            1,
            preg_match('/\$rutasPermitidas = \[(.*?)\n\];/s', $entrada, $coincidencias),
            'No se pudo leer la lista blanca de rutas.'
        );

        $this->assertStringContainsString("'comentar'", $coincidencias[1]);
        $this->assertStringContainsString('public function comentar', $this->leer(self::RUTA_CONTROLADOR));
    }

    public function test_el_controlador_exige_sesion_csrf_y_bloquea_ver_como(): void {
        $controlador = $this->leer(self::RUTA_CONTROLADOR);

        $this->assertStringContainsString('Sesion::estaLogueado()', $controlador);
        $this->assertStringContainsString('Sesion::requerirTokensCSRF()', $controlador);
        $this->assertStringContainsString("admin_antes_suplantar", $controlador);
        $this->assertStringContainsString('contarComentariosRecientes', $controlador, 'Falta el límite anti-inundación.');
    }

    public function test_el_listado_publico_se_alimenta_de_la_base_y_escapa_el_texto(): void {
        $seccion = $this->seccionComentarios();

        $this->assertStringContainsString('foreach ($comentariosVisibles as $comentario)', $seccion);
        $this->assertStringContainsString("htmlspecialchars((string) \$comentario['comentario']", $seccion);
        $this->assertStringContainsString('nl2br(', $seccion);
        $this->assertStringContainsString('sin-comentarios', $seccion, 'Falta el estado vacío.');
        $this->assertStringContainsString('pagina=', $seccion, 'Falta la paginación.');
        $this->assertStringNotContainsString('Comentario hecho por el usuario sobre el torneo.', $seccion);
        $this->assertStringNotContainsString('enConstruccion', $seccion);
        $this->assertStringNotContainsString('<button', $seccion, 'No debe quedar el botón Responder de la maqueta.');
    }

    public function test_el_formulario_publica_con_csrf_y_solo_con_sesion(): void {
        $seccion = $this->seccionFormulario();

        $this->assertStringContainsString('<form method="POST"', $seccion);
        $this->assertStringContainsString('c=torneo&a=comentar', $seccion);
        $this->assertStringContainsString('name="csrf_token"', $seccion);
        $this->assertStringContainsString('name="torneo_id"', $seccion);
        $this->assertStringContainsString('name="comentario"', $seccion);
        $this->assertStringContainsString('maxlength="800"', $seccion);
        $this->assertStringContainsString('admin_antes_suplantar', $seccion);
        $this->assertStringContainsString('mostrarLogin', $seccion, 'Sin sesión debe ofrecer iniciar sesión.');
        $this->assertStringNotContainsString('alert(', $seccion);
        $this->assertStringNotContainsString('type="email"', $seccion, 'El correo se toma de la sesión, no se pide de nuevo.');
    }

    public function test_la_politica_publica_ya_no_promete_moderacion_previa(): void {
        $politica = $this->leer(self::RUTA_POLITICA);

        $this->assertStringNotContainsString('quedan sujetos a moderación antes de', $politica);
        $this->assertStringContainsString('pueden ser', $politica);
        $this->assertStringContainsString('moderados u ocultados', $politica);
    }

    public function test_la_semilla_aporta_veintinueve_comentarios(): void {
        $qa = $this->leer(self::RUTA_SEED_QA);
        $filasQa = substr_count($qa, 'INSERT IGNORE INTO torneo_comentarios');

        $ampliada = $this->leer(self::RUTA_SEED_AMPLIADA);
        $inicio = strpos($ampliada, 'INSERT INTO torneo_comentarios (id, torneo_id');
        $this->assertNotFalse($inicio, 'La semilla ampliada dejó de sembrar comentarios.');
        // El cierre se busca en la cláusula siguiente y no en el primer punto y
        // coma: los textos sembrados pueden contener ";" dentro del comentario.
        $fin = strpos($ampliada, 'ON DUPLICATE KEY UPDATE', $inicio);
        $this->assertNotFalse($fin, 'La inserción de comentarios perdió su cierre.');
        $bloque = substr($ampliada, $inicio, $fin - $inicio);
        $filasAmpliada = preg_match_all('/^\s*\(\d+,/m', $bloque);

        $this->assertSame(29, $filasQa + $filasAmpliada, 'El README declara 29 comentarios sembrados.');
    }
}
