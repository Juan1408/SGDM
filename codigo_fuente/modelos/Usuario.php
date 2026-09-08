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
        //Escribimos la consulta SQL usando un marcador por seguridad (:email)
        $sql = "SELECT * FROM usuarios WHERE email = :email AND esta_activo = 1 LIMIT 1"; 

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

    public function obtenerPerfilOrganizador(int $usuarioId): ?array {
        $stmt = $this->bd->prepare(
            "SELECT u.id, u.nombre_completo, u.email, u.telefono, u.foto_perfil_url,
                    p.nombre_organizacion, p.bio_organizacion, p.localidad,
                    p.telefono_contacto, p.sitio_web
             FROM usuarios u
             LEFT JOIN perfiles_organizadores p ON p.usuario_id = u.id
             WHERE u.id = :usuario_id AND u.rol_id = 2
             LIMIT 1"
        );
        $stmt->execute([':usuario_id' => $usuarioId]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        return $perfil ?: null;
    }

    public function actualizarPerfilOrganizador(int $usuarioId, array $datos): bool {
        try {
            $this->bd->beginTransaction();
            $usuario = $this->bd->prepare(
                'UPDATE usuarios SET nombre_completo = :nombre, telefono = :telefono WHERE id = :id AND rol_id = 2'
            );
            $usuario->execute([':nombre' => $datos['nombre_completo'], ':telefono' => $datos['telefono'], ':id' => $usuarioId]);
            $perfil = $this->bd->prepare(
                "INSERT INTO perfiles_organizadores
                    (usuario_id, nombre_organizacion, bio_organizacion, localidad, telefono_contacto, sitio_web)
                 VALUES (:id, :organizacion, :bio, :localidad, :telefono, :sitio)
                 ON DUPLICATE KEY UPDATE nombre_organizacion = VALUES(nombre_organizacion),
                    bio_organizacion = VALUES(bio_organizacion), localidad = VALUES(localidad),
                    telefono_contacto = VALUES(telefono_contacto), sitio_web = VALUES(sitio_web)"
            );
            $perfil->execute([
                ':id' => $usuarioId, ':organizacion' => $datos['nombre_organizacion'],
                ':bio' => $datos['bio_organizacion'], ':localidad' => $datos['localidad'],
                ':telefono' => $datos['telefono_contacto'], ':sitio' => $datos['sitio_web'],
            ]);
            $this->bd->commit();
            return true;
        } catch (\Throwable $error) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return false;
        }
    }

    //Metodo para obtener todos los roles del sistema, con esto llenamos el <select> del formulario
    public function obtenerTodosLosRoles(){
        $sql = "SELECT id, nombre_rol FROM roles ORDER BY id ASC";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para actualizar los datos de un usuario
    public function actualizarUsuario(int $usuarioId, array $datos) {
        $sql = "UPDATE usuarios
            SET nombre_completo = :nombre_completo,
                email = :email,
                telefono = :telefono,
                rol_id = :rol
            WHERE id = :id";

        $stmt = $this->bd->prepare($sql);

        return $stmt->execute([
            'nombre_completo' => $datos['nombre_completo'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'rol' => $datos['rol_id'],
            'id' => $usuarioId
        ]);
    }

    //Metodo para eliminar un usuario (Baja fisica)
    public function eliminarUsuario(int $usuarioId){
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $usuarioId]);
    }

    //Metodo para registrar un nuevo usuario en el sistema con generacion de token de verificacion por email
    public function registrarUsuario(array $datos): array {
        $email = trim(strtolower($datos['email'] ?? ''));
        if ($this->buscarPorEmail($email)) {
            return ['exito' => false, 'mensaje' => 'El correo electrónico ya se encuentra registrado.'];
        }

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

            $this->bd->commit();

            return [
                'exito' => true,
                'usuario_id' => $usuarioId,
                'email' => $email,
                'rol_id' => $rolId,
                'token' => $token,
                'mensaje' => 'Registro completado con éxito.'
            ];
        } catch (\Throwable $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['exito' => false, 'mensaje' => 'Error al registrar el usuario: ' . $e->getMessage()];
        }
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
}
