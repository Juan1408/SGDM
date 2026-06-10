<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';
require_once __DIR__ . '/../modelos/Usuario.php';

/**
 * Clase RepositorioUsuario
 * 
 * Capa de acceso a datos para la entidad de Usuarios, logs de acceso,
 * historial de contraseñas y consulta de políticas vigentes.
 */
class RepositorioUsuario {
    /**
     * @var PDO $conexion Conexión activa a la base de datos MySQL.
     */
    private PDO $conexion;

    /**
     * Constructor del repositorio.
     */
    public function __construct() {
        $this->conexion = ConexionBD::obtenerInstancia();
    }

    /**
     * Busca y obtiene un usuario en base a su dirección de correo electrónico.
     * 
     * @param string $email Correo electrónico del usuario.
     * @return Usuario|null Retorna el objeto Usuario o null si no existe.
     */
    public function obtenerPorEmail(string $email): ?Usuario {
        /**
         * @var string $sql Sentencia SQL de búsqueda por email.
         */
        $sql = "SELECT id, email, contrasena_hash, nombre_completo, telefono, fecha_nacimiento, 
                       foto_perfil_url, fecha_registro, rol_id, esta_activo, email_verificado, ultimo_acceso 
                FROM usuarios 
                WHERE email = :email 
                LIMIT 1";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['email' => $email]);
        
        /**
         * @var array|false $fila Registro recuperado.
         */
        $fila = $sentencia->fetch();

        if (!$fila) {
            return null;
        }

        return new Usuario(
            (int)$fila['id'],
            $fila['email'],
            $fila['contrasena_hash'],
            $fila['nombre_completo'],
            $fila['telefono'],
            $fila['fecha_nacimiento'],
            $fila['foto_perfil_url'],
            $fila['fecha_registro'],
            (int)$fila['rol_id'],
            (bool)$fila['esta_activo'],
            (bool)$fila['email_verificado'],
            $fila['ultimo_acceso']
        );
    }

    /**
     * Guarda un nuevo registro de usuario en la base de datos.
     * 
     * @param Usuario $usuario Objeto conteniendo la información a persistir.
     * @return bool Retorna verdadero si la operación fue exitosa, falso de lo contrario.
     */
    public function guardar(Usuario $usuario): bool {
        /**
         * @var string $sql Sentencia SQL para inserción.
         */
        $sql = "INSERT INTO usuarios (email, contrasena_hash, nombre_completo, telefono, fecha_nacimiento, rol_id, esta_activo, email_verificado) 
                VALUES (:email, :contrasena_hash, :nombre_completo, :telefono, :fecha_nacimiento, :rol_id, :esta_activo, :email_verificado)";

        $sentencia = $this->conexion->prepare($sql);
        
        /**
         * @var bool $resultado Indica si la inserción fue correcta.
         */
        $resultado = $sentencia->execute([
            'email' => $usuario->email,
            'contrasena_hash' => $usuario->contrasenaHash,
            'nombre_completo' => $usuario->nombreCompleto,
            'telefono' => $usuario->telefono,
            'fecha_nacimiento' => $usuario->fechaNacimiento,
            'rol_id' => $usuario->rolId,
            'esta_activo' => $usuario->estaActivo ? 1 : 0,
            'email_verificado' => $usuario->emailVerificado ? 1 : 0
        ]);

        if ($resultado) {
            $usuario->id = (int)$this->conexion->lastInsertId();
        }

        return $resultado;
    }

    /**
     * Actualiza la fecha y hora del último acceso de un usuario.
     * 
     * @param int $usuarioId Identificador del usuario.
     * @return void
     */
    public function actualizarUltimoAcceso(int $usuarioId): void {
        /**
         * @var string $sql Sentencia SQL de actualización.
         */
        $sql = "UPDATE usuarios SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = :id";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['id' => $usuarioId]);
    }

    /**
     * Registra un evento de intento de acceso en la tabla logs_acceso.
     * 
     * @param string $email Correo electrónico ingresado en el intento.
     * @param int|null $usuarioId Identificador de usuario si existe.
     * @param bool $exito Resultado del intento (correcto/fallido).
     * @param string $ip Dirección IP del solicitante.
     * @param string $userAgent Agente de usuario del cliente.
     * @param string|null $mensajeError Detalle del error si falló.
     * @return void
     */
    public function registrarLogAcceso(
        string $email,
        ?int $usuarioId,
        bool $exito,
        string $ip,
        string $userAgent,
        ?string $mensajeError
    ): void {
        /**
         * @var string $sql Sentencia SQL de inserción en log.
         */
        $sql = "INSERT INTO logs_acceso (email_intentado, usuario_id, exito, ip_origen, user_agent, mensaje_error) 
                VALUES (:email, :usuario_id, :exito, :ip_origen, :user_agent, :mensaje_error)";
        
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([
            'email' => $email,
            'usuario_id' => $usuarioId,
            'exito' => $exito ? 1 : 0,
            'ip_origen' => $ip,
            'user_agent' => $userAgent,
            'mensaje_error' => $mensajeError
        ]);
    }

    /**
     * Obtiene los parámetros activos de la política de contraseñas.
     * 
     * @return array|null Retorna un array asociativo con la política activa o null.
     */
    public function obtenerPoliticaContrasenas(): ?array {
        /**
         * @var string $sql Consulta SQL para la política.
         */
        $sql = "SELECT longitud_minima, requiere_mayuscula, requiere_minuscula, requiere_numero, 
                       requiere_caracter_especial, expiracion_dias, historial_cantidad 
                FROM politicas_contrasenas 
                LIMIT 1";
        
        $sentencia = $this->conexion->query($sql);
        
        /**
         * @var array|false $fila Fila recuperada.
         */
        $fila = $sentencia->fetch();
        return $fila ? $fila : null;
    }

    /**
     * Obtiene los hashes de contraseñas guardados en el historial de un usuario.
     * 
     * @param int $usuarioId Identificador de usuario.
     * @return string[] Lista de hashes de contraseñas anteriores.
     */
    public function obtenerHistorialContrasenas(int $usuarioId): array {
        /**
         * @var string $sql Consulta de historial ordenada por fecha.
         */
        $sql = "SELECT hash_anterior FROM historial_contrasenas WHERE usuario_id = :usuario_id ORDER BY fecha_cambio DESC";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['usuario_id' => $usuarioId]);
        return $sentencia->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Registra un nuevo hash en el historial de contraseñas de un usuario.
     * 
     * @param int $usuarioId Identificador del usuario.
     * @param string $hashContrasena Hash de la contraseña cambiada.
     * @return void
     */
    public function registrarHistorialContrasena(int $usuarioId, string $hashContrasena): void {
        /**
         * @var string $sql Inserción del hash en el historial.
         */
        $sql = "INSERT INTO historial_contrasenas (usuario_id, hash_anterior) VALUES (:usuario_id, :hash_anterior)";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([
            'usuario_id' => $usuarioId,
            'hash_anterior' => $hashContrasena
        ]);

        /**
         * @var array|null $politica Consulta la cantidad máxima permitida en el historial.
         */
        $politica = $this->obtenerPoliticaContrasenas();
        /**
         * @var int $limite Límite de contraseñas a conservar en historial.
         */
        $limite = $politica ? (int)$politica['historial_cantidad'] : 5;

        // Limpieza automática del historial si supera el límite de la política
        $sqlLimpieza = "DELETE FROM historial_contrasenas 
                        WHERE usuario_id = :usuario_id 
                          AND id NOT IN (
                              SELECT id FROM (
                                  SELECT id FROM historial_contrasenas 
                                  WHERE usuario_id = :usuario_id 
                                  ORDER BY fecha_cambio DESC 
                                  LIMIT :limite
                              ) as subconsulta
                          )";
        
        $sentenciaLimpieza = $this->conexion->prepare($sqlLimpieza);
        $sentenciaLimpieza->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $sentenciaLimpieza->bindValue(':limite', $limite, PDO::PARAM_INT);
        $sentenciaLimpieza->execute();
    }
}
