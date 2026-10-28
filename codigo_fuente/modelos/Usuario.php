<?php
/**
 * ============================================================================
 * CLASE MODELO: Usuario.php
 * ============================================================================
 * Propósito: Gestiona el acceso a datos y operaciones CRUD para la tabla 'usuarios'
 *            y la especialización de perfiles (perfiles_jugadores, perfiles_organizadores).
 * Ubicación: codigo_fuente/modelos/Usuario.php
 * ============================================================================
 */

namespace App\Modelos;

use PDO; //importamos pdo para no tener que poner PDO en toda la clase
require_once __DIR__ . '/Conexion.php';

//La clase Usuario maneja todas las interacciones con la base de datos relacionadas con usuarios.

class Usuario {
    //Propiedades: nos aseguramos de que la conexion sea privada para que solo se pueda acceder desde aqui
    private PDO $bd;
    
    //Constructor: inicializamos la conexion a la base de datos al crear un objeto de esta clase
    public function __construct() {
        //Optenemos la instancia única de la base de datos
        $this->bd = Conexion::getInstance()->getBD();
    }

    //Metodo para buscar a un usuario por su email
    public function buscarPorEmail(string $email) {
        //Buscamos por email sin filtrar por estado: el login necesita distinguir
        //una cuenta suspendida de unas credenciales inexistentes.
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";

        //Preparamos la consulta para evitar inyección SQL
        $stmt = $this->bd->prepare($sql);

        //Ejecutamos la consulta pasandole el email
        $stmt->execute([':email' => $email]);

        //Obtenemos el resultado como array asociativo
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        //Devolvemos el resultado si lo encuentra o null si no
        return $usuario;        
    }

    //Metodo para contar el total de usuarios registrador
    public function contarTotalJugadoresRegistrados() {
        //La instruccion SQL COUNT(*) cuenta las filas de la tabla
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE rol_id = 3";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();

        //fetch devuelve la fila, asi sacamos la columna total
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'] ?? 0;
    }

    //Metodo para contar el total de organizadores registrados
    public function contarTotalOrganizadoresRegistrados(){
      $sql = "SELECT COUNT(*) as total FROM usuarios WHERE rol_id = 2";
      $stmt = $this->bd->prepare($sql);
      $stmt->execute();
      $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
      return $resultado['total'] ?? 0;    
    }   

    //Metodo para obtener los ultimos jugadores registrados
    public function obtenerUltimosJugadoresRegistrados(int $limite){
        $sql = "SELECT id, nombre_completo, email, fecha_registro
        FROM usuarios
        WHERE rol_id = 3
        ORDER BY fecha_registro DESC
        LIMIT :limite";
        
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para obtener los ultimos organizadores registrados
    public function obtenerUltimosOrganizadoresRegistrados(int $limite) {
        $sql = "SELECT id, nombre_completo, email, fecha_registro
        FROM usuarios
        WHERE rol_id = 2
        ORDER BY fecha_registro DESC
        LIMIT :limit";

        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':limit', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para obtener todos los usuarios registrados junto con el rol
    public function obtenerTodosUsuariosConRoles(?int $rolFiltro = null){
        $sql = "SELECT 
        u.id,
        u.nombre_completo,
        u.email,
        u.telefono,
        u.esta_activo,
        u.fecha_registro,
        u.rol_id,
        r.nombre_rol,
        COALESCE(p.nombre_organizacion, 'Organizador Independiente') AS nombre_organizacion,
        COALESCE(p.verificado_oficial, 0) AS verificado_oficial
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        LEFT JOIN perfiles_organizadores p ON p.usuario_id = u.id";

        if ($rolFiltro){
            $sql .= " WHERE u.rol_id = :rolFiltro";
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $this->bd->prepare($sql);

        if ($rolFiltro){
            $stmt->execute([':rolFiltro' => $rolFiltro]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para responder (Aprobar, Rechazar o Revocar) a la solicitud de un organizador
    public function responderSolicitudOrganizador(int $usuarioId, string $decision): bool {
        $estado = ($decision === 'aprobar') ? 1 : 0;

        $sqlUpsert = "INSERT INTO perfiles_organizadores (usuario_id, nombre_organizacion, verificado_oficial)
                      VALUES (:id, 'Organizador Independiente', :estado)
                      ON DUPLICATE KEY UPDATE verificado_oficial = VALUES(verificado_oficial)";
        $stmtUpsert = $this->bd->prepare($sqlUpsert);
        return $stmtUpsert->execute([
            ':id' => $usuarioId,
            ':estado' => $estado
        ]);
    }

    //Metodo para alternar el estado de verificación oficial de un organizador
    public function cambiarVerificacionOrganizador(int $usuarioId): bool {
        // Verificar o crear perfil de organizador en la tabla hija
        $sqlActual = "SELECT verificado_oficial FROM perfiles_organizadores WHERE usuario_id = :id LIMIT 1";
        $stmtActual = $this->bd->prepare($sqlActual);
        $stmtActual->execute([':id' => $usuarioId]);
        $perfil = $stmtActual->fetch(PDO::FETCH_ASSOC);

        $nuevoEstado = ($perfil && $perfil['verificado_oficial']) ? 0 : 1;

        $sqlUpsert = "INSERT INTO perfiles_organizadores (usuario_id, nombre_organizacion, verificado_oficial)
                      VALUES (:id, 'Organizador Independiente', :nuevoEstado)
                      ON DUPLICATE KEY UPDATE verificado_oficial = VALUES(verificado_oficial)";
        $stmtUpsert = $this->bd->prepare($sqlUpsert);
        return $stmtUpsert->execute([
            ':id' => $usuarioId,
            ':nuevoEstado' => $nuevoEstado
        ]);
    }

    //Metodo para cambiar el estado (Activo/Bloqueado) de un usuario
    public function cambiarEstadoUsuario(int $usuarioId){
        $sql = "SELECT esta_activo FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        $estadoActual = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($estadoActual === false) {
            return false;
        }

        if ($estadoActual){
            $nuevoEstado = $estadoActual['esta_activo'] ? 0 : 1; 

            //Escribimos la consulta SQL para actualizar el estado
            $sqlUpdate = "UPDATE usuarios SET esta_activo = :estado WHERE id =:id";
            $stmtUpdate = $this->bd->prepare($sqlUpdate);

            //Ejecutamos la actualizacion pasando el nuevo estado
            return $stmtUpdate->execute([
                ':estado' => $nuevoEstado,
                ':id' => $usuarioId
            ]);
        }
        
        //Devolvemos el nuevo estado del usuario
        return false;
    }

    //Metodo para obtener datos de un usuario por su ID para poder editarlo
    public function obtenerUsuarioPorId(int $usuarioId){
        $sql = "SELECT id, nombre_completo, email, rol_id, telefono, esta_activo 
        FROM usuarios 
        WHERE id = :id 
        LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        //Si no encuentra el usuario devuelve false, si lo encuentra devuelve el usuario
        return $usuario ?: false;
    }


    //Metodo para obtener todos los roles del sistema, con esto llenamos el <select> del formulario
    public function obtenerTodosLosRoles(){
        $sql = "SELECT id, nombre_rol FROM roles ORDER BY id ASC";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos administrativos del usuario.
     * El email no se modifica: la verificación de correo pertenece al usuario
     * y un cambio desde el panel la invalidaría.
     *
     * El cambio de rol se sincroniza con la tabla hija correspondiente dentro
     * de la misma transacción para que el panel del rol destino no quede vacío.
     */
    public function actualizarUsuario(int $usuarioId, array $datos) {
        $rolId = (int) $datos['rol_id'];

        try {
            $this->bd->beginTransaction();

            $sql = "UPDATE usuarios
                SET nombre_completo = :nombre_completo,
                    rol_id = :rol
                WHERE id = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                'nombre_completo' => $datos['nombre_completo'],
                'rol' => $rolId,
                'id' => $usuarioId
            ]);

            //No pisamos un perfil existente: solo creamos la fila si falta.
            if ($rolId === 2) {
                $stmt = $this->bd->prepare("SELECT 1 FROM perfiles_organizadores WHERE usuario_id = :id LIMIT 1");
                $stmt->execute([':id' => $usuarioId]);

                if (!$stmt->fetchColumn()) {
                    $stmt = $this->bd->prepare(
                        "INSERT INTO perfiles_organizadores (usuario_id, nombre_organizacion, verificado_oficial)
                         SELECT id, 'Organizador Independiente', 0 FROM usuarios WHERE id = :id"
                    );
                    $stmt->execute([':id' => $usuarioId]);
                }
            } elseif ($rolId === 3) {
                $stmt = $this->bd->prepare("SELECT 1 FROM perfiles_jugadores WHERE usuario_id = :id LIMIT 1");
                $stmt->execute([':id' => $usuarioId]);

                if (!$stmt->fetchColumn()) {
                    $stmt = $this->bd->prepare(
                        "INSERT INTO perfiles_jugadores (usuario_id, apodo_gamertag)
                         SELECT id, LEFT(SUBSTRING_INDEX(email, '@', 1), 50) FROM usuarios WHERE id = :id"
                    );
                    $stmt->execute([':id' => $usuarioId]);
                }
            }

            $this->bd->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Dependencias visibles de un usuario antes de cambiarle el rol.
     * Es informativo: el cambio no transfiere automáticamente esas responsabilidades.
     */
    public function obtenerImpactoRol(int $usuarioId): array {
        $sql = "SELECT
            (SELECT COUNT(*) FROM torneos WHERE organizador_id = :torneos) AS torneos,
            (SELECT COUNT(*) FROM equipos WHERE creado_por = :equipos) AS equipos,
            (SELECT COUNT(*) FROM equipo_capitanes WHERE usuario_id = :capitanias) AS capitanias";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':torneos' => $usuarioId,
            ':equipos' => $usuarioId,
            ':capitanias' => $usuarioId,
        ]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'torneos' => (int) ($fila['torneos'] ?? 0),
            'equipos' => (int) ($fila['equipos'] ?? 0),
            'capitanias' => (int) ($fila['capitanias'] ?? 0),
        ];
    }

    //Metodo para actualizar perfil (nombre y teléfono unicamente)
    public function actualizarPerfil(int $usuarioId, string $nombre, string $telefono) {
        $sql = "UPDATE usuarios
            SET nombre_completo = :nombre,
                telefono = :telefono
            WHERE id = :id";

        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':id' => $usuarioId
        ]);
    }

    /**
     * Baja lógica del usuario.
     * No se borra la fila: se conservan perfiles, equipos, capitanías, torneos,
     * historial y auditoría. La sesión se invalida en la siguiente petición
     * porque verificarUsuarioActivo() exige esta_activo = 1.
     */
    public function eliminarUsuario(int $usuarioId): array {
        try {
            $stmt = $this->bd->prepare("SELECT esta_activo FROM usuarios WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $usuarioId]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila === false) {
                return ['exito' => false, 'mensaje' => 'El usuario no existe.'];
            }

            if ((int) $fila['esta_activo'] === 0) {
                return ['exito' => true, 'mensaje' => 'El usuario ya estaba dado de baja.'];
            }

            $stmt = $this->bd->prepare("UPDATE usuarios SET esta_activo = 0 WHERE id = :id");
            $stmt->execute([':id' => $usuarioId]);

            return [
                'exito' => true,
                'mensaje' => 'Usuario dado de baja correctamente. Su historial se conserva.'
            ];
        } catch (\PDOException $e) {
            error_log('Error al dar de baja al usuario ID ' . $usuarioId . ': ' . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'No se pudo dar de baja al usuario. Intentá nuevamente.'];
        }
    }

    //Metodo para registrar un nuevo usuario en el sistema con generacion de token de verificacion por email
    public function registrarUsuario(array $datos): array {
        $email = trim(strtolower($datos['email'] ?? ''));
        // Defensa en profundidad: el alta no depende de que el controlador
        // haya validado el formato ni de la validación nativa del navegador.
        if ($email === '' || mb_strlen($email, 'UTF-8') > 255
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return ['exito' => false, 'mensaje' => 'Ingresá un correo electrónico válido.'];
        }
        if ($this->buscarPorEmail($email)) {
            return ['exito' => false, 'mensaje' => 'El correo electrónico ya se encuentra registrado.'];
        }

        // La aceptación es obligatoria y la decide el servidor, nunca el cliente.
        // Se valida antes de abrir la transacción para no dejar rastros parciales.
        $aceptaciones = $this->normalizarAceptaciones($datos['aceptaciones'] ?? null);
        if ($aceptaciones === null) {
            return [
                'exito' => false,
                'mensaje' => 'Debés aceptar los Términos y Condiciones y la Política de Privacidad para crear tu cuenta.'
            ];
        }
        $ipAceptacion = $this->normalizarIp($datos['aceptacion_ip'] ?? null);
        $userAgentAceptacion = $this->normalizarUserAgent($datos['aceptacion_user_agent'] ?? null);

        try {
            $this->bd->beginTransaction();

            $hash = password_hash($datos['contrasena'], PASSWORD_BCRYPT);
            $rolId = (int) ($datos['rol_id'] ?? 3); // 3 = Jugador por defecto

            $sqlUsuario = "INSERT INTO usuarios (nombre_completo, email, contrasena_hash, telefono, rol_id, esta_activo, email_verificado)
                           VALUES (:nombre, :email, :hash, :telefono, :rol_id, 1, 0)";
            $stmtUsuario = $this->bd->prepare($sqlUsuario);
            $stmtUsuario->execute([
                ':nombre' => trim($datos['nombre_completo']),
                ':email' => $email,
                ':hash' => $hash,
                ':telefono' => trim($datos['telefono'] ?? ''),
                ':rol_id' => $rolId
            ]);

            $usuarioId = (int) $this->bd->lastInsertId();

            // Especialización en tabla hija según rol
            if ($rolId === 2) {
                // Organizador: verificado_oficial = 0 (requiere aprobación del Administrador)
                $sqlPerfil = "INSERT INTO perfiles_organizadores (usuario_id, nombre_organizacion, verificado_oficial)
                              VALUES (:usuario_id, :organizacion, 0)";
                $stmtPerfil = $this->bd->prepare($sqlPerfil);
                $stmtPerfil->execute([
                    ':usuario_id' => $usuarioId,
                    ':organizacion' => trim($datos['nombre_organizacion'] ?? 'Organizador Independiente')
                ]);
            } else {
                // Jugador
                $sqlPerfil = "INSERT INTO perfiles_jugadores (usuario_id, apodo_gamertag)
                              VALUES (:usuario_id, :apodo)";
                $stmtPerfil = $this->bd->prepare($sqlPerfil);
                $stmtPerfil->execute([
                    ':usuario_id' => $usuarioId,
                    ':apodo' => trim($datos['apodo'] ?? explode('@', $email)[0])
                ]);
            }

            // Generación de token aleatorio de verificación por email (24 horas de validez)
            $token = bin2hex(random_bytes(32));
            $sqlToken = "INSERT INTO tokens_verificacion_email (usuario_id, token, expira_en, usado)
                         VALUES (:usuario_id, :token, DATE_ADD(NOW(), INTERVAL 24 HOUR), 0)";
            $stmtToken = $this->bd->prepare($sqlToken);
            $stmtToken->execute([
                ':usuario_id' => $usuarioId,
                ':token' => $token
            ]);

            // Constancia de aceptación: dos filas, una por documento, dentro de
            // la misma transacción. Si falla, el rollback deja la cuenta sin crear.
            $sqlAceptacion = "INSERT INTO aceptaciones_legales
                                  (usuario_id, tipo_documento, version, ip_origen, user_agent)
                              VALUES (:usuario_id, :tipo_documento, :version, :ip_origen, :user_agent)";
            $stmtAceptacion = $this->bd->prepare($sqlAceptacion);
            foreach ($aceptaciones as $tipoDocumento => $versionDocumento) {
                $stmtAceptacion->execute([
                    ':usuario_id' => $usuarioId,
                    ':tipo_documento' => $tipoDocumento,
                    ':version' => $versionDocumento,
                    ':ip_origen' => $ipAceptacion,
                    ':user_agent' => $userAgentAceptacion,
                ]);
            }

            $this->bd->commit();

            return [
                'exito' => true,
                'usuario_id' => $usuarioId,
                'email' => $email,
                'rol_id' => $rolId,
                'token' => $token,
                'mensaje' => 'Registro completado con éxito.'
            ];
        } catch (\PDOException $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            if ($e->getCode() === '23000') {
                return ['exito' => false, 'mensaje' => 'El correo electrónico ya se encuentra registrado.'];
            }
            return ['exito' => false, 'mensaje' => 'No se pudo completar el registro. Intentá nuevamente.'];
        } catch (\Throwable $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['exito' => false, 'mensaje' => 'No se pudo completar el registro. Intentá nuevamente.'];
        }
    }

    /**
     * Valida y normaliza las aceptaciones legales recibidas del controlador.
     * Devuelve null si falta un documento o la versión no es válida.
     */
    private function normalizarAceptaciones($aceptaciones) {
        if (!is_array($aceptaciones)) {
            return null;
        }

        $normalizadas = [];
        foreach (['terminos', 'privacidad'] as $tipoDocumento) {
            $version = trim((string) ($aceptaciones[$tipoDocumento] ?? ''));
            if ($version === '' || mb_strlen($version, 'UTF-8') > 20) {
                return null;
            }
            $normalizadas[$tipoDocumento] = $version;
        }

        return $normalizadas;
    }

    private function normalizarIp($ip) {
        $ip = trim((string) $ip);
        if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return null;
        }
        return mb_substr($ip, 0, 45, 'UTF-8');
    }

    private function normalizarUserAgent($userAgent) {
        $userAgent = trim((string) $userAgent);
        if ($userAgent === '') {
            return null;
        }
        return mb_substr($userAgent, 0, 255, 'UTF-8');
    }

    //Metodo para verificar token de activacion de correo electronico
    public function verificarTokenEmail(string $token): array {
        $sql = "SELECT id, usuario_id, expira_en, usado 
                FROM tokens_verificacion_email 
                WHERE token = :token AND usado = 0 
                LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':token' => $token]);
        $registroToken = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$registroToken) {
            return ['exito' => false, 'mensaje' => 'El enlace de verificación es inválido o ya ha sido utilizado.'];
        }

        if (strtotime($registroToken['expira_en']) < time()) {
            return ['exito' => false, 'mensaje' => 'El enlace de verificación ha expirado. Solicita uno nuevo.'];
        }

        try {
            $this->bd->beginTransaction();

            // Marcar token como usado
            $stmtUsado = $this->bd->prepare("UPDATE tokens_verificacion_email SET usado = 1 WHERE id = :id");
            $stmtUsado->execute([':id' => $registroToken['id']]);

            // Activar email_verificado en el usuario
            $stmtUsuario = $this->bd->prepare("UPDATE usuarios SET email_verificado = 1 WHERE id = :id");
            $stmtUsuario->execute([':id' => $registroToken['usuario_id']]);

            $this->bd->commit();

            return ['exito' => true, 'mensaje' => '¡Correo electrónico verificado exitosamente! Ya puedes iniciar sesión.'];
        } catch (\Throwable $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['exito' => false, 'mensaje' => 'Error al verificar el token: ' . $e->getMessage()];
        }
    }

    // ============================================================================
    // FICHA DE USUARIO PARA EL ADMINISTRADOR (SOLO LECTURA)
    // No se selecciona contrasena_hash ni token_sesion: nunca deben mostrarse.
    // ============================================================================

    /**
     * Reúne los datos de identidad, perfiles, equipos, actividad y accesos de un usuario.
     *
     * @return array|false
     */
    public function obtenerFichaAdmin(int $usuarioId) {
        if ($usuarioId <= 0) {
            return false;
        }

        $sql = "SELECT u.id, u.nombre_completo, u.email, u.telefono, u.foto_perfil_url,
                       u.rol_id, u.esta_activo, u.email_verificado, u.fecha_registro, u.ultimo_acceso,
                       r.nombre_rol
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol_id
                WHERE u.id = :id
                LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false;
        }

        return [
            'usuario' => $usuario,
            'perfil_jugador' => $this->obtenerPerfilJugadorAdmin($usuarioId),
            'perfil_organizador' => $this->obtenerPerfilOrganizadorAdmin($usuarioId),
            'aceptaciones' => $this->obtenerAceptacionesLegales($usuarioId),
            'equipos' => $this->obtenerEquiposDelUsuario($usuarioId),
            'participaciones' => $this->obtenerParticipacionesDeUsuario($usuarioId),
            'accesos' => $this->obtenerAccesosRecientes($usuarioId, 10),
            'resumen_accesos' => $this->obtenerResumenAccesos($usuarioId),
            'sesiones' => $this->obtenerSesionesAdmin($usuarioId),
            'actividad' => $this->obtenerActividadReciente($usuarioId, 10),
            'metricas' => $this->obtenerMetricasUsuario($usuarioId),
        ];
    }

    /**
     * Constancias de aceptación legal del usuario, de la más reciente a la más
     * antigua. Es información de solo lectura para la ficha administrativa.
     */
    public function obtenerAceptacionesLegales(int $usuarioId): array {
        if ($usuarioId <= 0) {
            return [];
        }

        $sql = "SELECT tipo_documento, version, aceptado_en, ip_origen, user_agent
                FROM aceptaciones_legales
                WHERE usuario_id = :id
                ORDER BY tipo_documento ASC, aceptado_en DESC, id DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obtenerPerfilJugadorAdmin(int $usuarioId) {
        $sql = "SELECT apodo_gamertag, bio, banner_url, nivel, experiencia_puntos,
                       pais, ciudad, fecha_nacimiento, discord_tag,
                       instagram_url, twitter_url, campos_publicos, actualizado_en
                FROM perfiles_jugadores
                WHERE usuario_id = :id
                LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        return $perfil ?: null;
    }

    private function obtenerPerfilOrganizadorAdmin(int $usuarioId) {
        $sql = "SELECT nombre_organizacion, bio_organizacion, banner_url, localidad,
                       telefono_contacto, sitio_web, verificado_oficial, campos_publicos, actualizado_en
                FROM perfiles_organizadores
                WHERE usuario_id = :id
                LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        return $perfil ?: null;
    }

    /**
     * Equipos en los que participa el usuario, incluyendo membresías inactivas
     * para que el administrador pueda auditar el historial completo.
     */
    public function obtenerEquiposDelUsuario(int $usuarioId): array {
        $sql = "SELECT e.id, e.nombre_equipo, e.escudo_url, e.activo, e.juego_id,
                       em.posicion, em.numero_camiseta, em.es_activo AS membresia_activa, em.fecha_union,
                       EXISTS (
                           SELECT 1 FROM equipo_capitanes ec
                           WHERE ec.equipo_id = e.id AND ec.usuario_id = em.usuario_id
                       ) AS es_capitan
                FROM equipo_miembros em
                INNER JOIN equipos e ON e.id = em.equipo_id
                WHERE em.usuario_id = :id
                ORDER BY em.es_activo DESC, e.nombre_equipo ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Participaciones individuales del usuario (tipo 'usuario' en participantes_torneo).
     */
    public function obtenerParticipacionesDeUsuario(int $usuarioId): array {
        $sql = "SELECT pt.id, pt.torneo_id, pt.nombre AS nombre_participante, pt.estado,
                       pt.fecha_inscripcion, t.nombre AS torneo_nombre, t.estado AS torneo_estado,
                       pos.partidos_jugados, pos.partidos_ganados, pos.partidos_empatados,
                       pos.partidos_perdidos, pos.puntos
                FROM participantes_torneo pt
                INNER JOIN torneos t ON t.id = pt.torneo_id
                LEFT JOIN torneo_posiciones pos
                       ON pos.torneo_id = pt.torneo_id AND pos.participante_id = pt.id
                WHERE pt.tipo = 'usuario' AND pt.referencia_id = :id
                ORDER BY t.fecha_inicio DESC, t.id DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Historial de intentos de acceso del usuario (exitosos y fallidos).
     */
    public function obtenerAccesosRecientes(int $usuarioId, int $limite = 10): array {
        $sql = "SELECT id, exito, ip_origen, user_agent, mensaje_error, fecha_hora
                FROM logs_acceso
                WHERE usuario_id = :id
                ORDER BY fecha_hora DESC, id DESC
                LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', max(1, $limite), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Totales de accesos del usuario y fecha del último intento fallido.
     */
    public function obtenerResumenAccesos(int $usuarioId): array {
        $sql = "SELECT COUNT(*) AS total,
                       COALESCE(SUM(exito = 1), 0) AS exitosos,
                       COALESCE(SUM(exito = 0), 0) AS fallidos,
                       MAX(CASE WHEN exito = 0 THEN fecha_hora END) AS ultimo_fallido
                FROM logs_acceso
                WHERE usuario_id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total' => (int) ($fila['total'] ?? 0),
            'exitosos' => (int) ($fila['exitosos'] ?? 0),
            'fallidos' => (int) ($fila['fallidos'] ?? 0),
            'ultimo_fallido' => $fila['ultimo_fallido'] ?? null,
        ];
    }

    /**
     * Sesiones registradas del usuario. Se excluye token_sesion deliberadamente.
     */
    public function obtenerSesionesAdmin(int $usuarioId): array {
        $sql = "SELECT id, ip_origen, user_agent, ultima_actividad, creado_en, expira_en, activa
                FROM sesiones_activas
                WHERE usuario_id = :id
                ORDER BY activa DESC, ultima_actividad DESC
                LIMIT 10";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Últimas acciones del usuario registradas en logs_actividad.
     */
    public function obtenerActividadReciente(int $usuarioId, int $limite = 10): array {
        $sql = "SELECT id, accion, descripcion, ip_origen, fecha_hora
                FROM logs_actividad
                WHERE usuario_id = :id
                ORDER BY fecha_hora DESC, id DESC
                LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', max(1, $limite), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contadores globales del usuario para el resumen de la ficha.
     */
    public function obtenerMetricasUsuario(int $usuarioId): array {
        // Placeholders distintos: la conexión usa prepares nativos y no admite
        // reutilizar el mismo parámetro nombrado en varias posiciones.
        $sql = "SELECT
                    (SELECT COUNT(*) FROM equipos WHERE creado_por = :id_creados) AS equipos_creados,
                    (SELECT COUNT(*) FROM torneos WHERE organizador_id = :id_torneos) AS torneos_organizados,
                    (SELECT COUNT(*) FROM equipo_miembros WHERE usuario_id = :id_miembros AND es_activo = 1) AS equipos_activos";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':id_creados' => $usuarioId,
            ':id_torneos' => $usuarioId,
            ':id_miembros' => $usuarioId,
        ]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'equipos_creados' => (int) ($fila['equipos_creados'] ?? 0),
            'torneos_organizados' => (int) ($fila['torneos_organizados'] ?? 0),
            'equipos_activos' => (int) ($fila['equipos_activos'] ?? 0),
        ];
    }
}
