Puntos para la segunda entrega de proyecto
3°MO. Full Stack
1. Modelo Relacional Normalizado
El modelo relacional define las tablas, campos, claves primarias (PK), claves foráneas (FK) y las
relaciones entre entidades. Para garantizar la integridad y evitar redundancias, debe cumplir con las
tres primeras formas normales (1FN, 2FN, 3FN):
• Primera Forma Normal (1FN): Eliminación de valores repetidos o atómicos. Cada celda debe
contener un único valor y cada columna debe ser única.
• Segunda Forma Normal (2FN): Cumplir 1FN + Todos los atributos que no son clave deben
depender por completo de la clave primaria (aplica especialmente a claves compuestas).
• Tercera Forma Normal (3FN): Cumplir 2FN + Ningún atributo no-clave debe depender de otro
atributo no-clave (eliminación de dependencias transitivas).
Modelos a entregar:
Pasaje a tablas y normalización:
Ej:
Alumno (dni_alumno, nombre_alumno)
Inscripcion(cod_inscripcion, fecha_inscripcion, dni_alumno FK)
Detalle_incripcion(cod_inscripcion FK, curso FK)
Curso(curso, precio_curso, id_profesor FK)
Profesor(profesor_curso, aula_profesor)
D-ER: Se enfoca en el negocio y los
requerimientos del mundo real,
ignorando los detalles técnicos de la
implementación en software.
(Este es opcional, ya que en la letra
del proyecto no lo pide)
Modelo Relacional Normalizado
(Este ejemplo está explicado paso a paso en el documento ‘15 Pasaje a tablas y normalización.pdf’ ubicado
en la carpeta UNIDAD III en CREA)
Ejemplo de Esquema (Módulo de Usuarios y Roles)
SQL
CREATE TABLE roles (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nombre VARCHAR(50) NOT NULL UNIQUE,
 descripcion VARCHAR(255)
) ENGINE=InnoDB;
CREATE TABLE usuarios (
 id INT AUTO_INCREMENT PRIMARY KEY,
 rol_id INT NOT NULL,
 nombre VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
2. DCL Implementado (Data Control Language)
Diagrama relacional (lógico/físico):
Se enfoca en cómo se estructurará
e implementará la base de datos
dentro de un motor (MySQL,
PostgreSQL, etc.).
El DCL maneja la seguridad de la base de datos mediante la asignación de permisos sobre los objetos
del sistema (tablas, vistas, procedimientos). Los comandos clave son GRANT y REVOKE.
Ejemplo de Declaraciones DCL
SQL
-- Creación de un usuario específico para el entorno de producción
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'PasswordSeguro123!';
-- Concesión de permisos específicos (Principio de mínimo privilegio)
GRANT SELECT, INSERT, UPDATE, DELETE ON mi_base_datos.usuarios TO 'app_user'@'localhost';
GRANT SELECT ON mi_base_datos.roles TO 'app_user'@'localhost';
-- Aplicar los cambios de privilegios
FLUSH PRIVILEGES;
3. Configuración de Usuarios de BD con Restricciones Pertinentes
Para proteger la base de datos frente a vulnerabilidades y accesos no autorizados, la configuración de
usuarios debe regirse por restricciones estrictas:
• Principio de Menor Privilegio: La aplicación nunca debe conectarse como root o admin. El
usuario de la aplicación solo debe tener los permisos estrictores para operar (SELECT, INSERT,
UPDATE, DELETE).
• Restricción por Host: Limitar la conexión del usuario a una IP o dominio específico
('app_user'@'localhost' o 'app_user'@'192.168.1.%').
• Restricción de Recursos: En entornos compartidos o críticos, se pueden aplicar límites de
uso de recursos:
SQL
ALTER USER 'app_user'@'localhost' WITH
 MAX_QUERIES_PER_HOUR 1000
 MAX_CONNECTIONS_PER_HOUR 100
 MAX_USER_CONNECTIONS 10;
4. Implementación de Modelos Alineados al Modelo Relacional
En la capa de código (PHP), cada tabla del modelo relacional suele mapearse a una clase (patrón
Active Record o Data Mapper). Los atributos de la clase coinciden con las columnas de la tabla y las
relaciones se gestionan mediante métodos o referencias.
PHP
<?php
// Model/Usuario.php
class Usuario {
 private ?int $id;
 private int $rolId;
 private string $nombre;
 private string $email;
 private string $passwordHash;
 public function __construct(?int $id, int $rolId, string $nombre, string $email, string
$passwordHash) {
 $this->id = $id;
 $this->rolId = $rolId;
 $this->nombre = $nombre;
 $this->email = $email;
 $this->passwordHash = $passwordHash;
 }
 // Getters
 public function getId(): ?int { return $this->id; }
 public function getRolId(): int { return $this->rolId; }
 public function getNombre(): string { return $this->nombre; }
 public function getEmail(): string { return $this->email; }
 public function getPasswordHash(): string { return $this->passwordHash; }
}
5. Integración con PHP utilizando POO (Gestión de Usuarios)
La arquitectura debe seguir la separación de responsabilidades: Conexión PDO (Singleton/Factory),
Modelos, Repositorios/DAOs y Controladores.
Ejemplo 1
Conexión PDO con Consultas Preparadas
PHP
// Config/Database.php
class Database {
 private static ?PDO $instance = null;
 public static function getConnection(): PDO {
 if (self::$instance === null) {
 $dsn = "mysql:host=localhost;dbname=mi_base_datos;charset=utf8mb4";
 self::$instance = new PDO($dsn, 'app_user', 'PasswordSeguro123!', [
 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES => false,
 ]);
 }
 return self::$instance;
 }
}
Repositorio de Usuarios (CRUD Básico)
PHP
// Repository/UsuarioRepository.php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Model/Usuario.php';
class UsuarioRepository {
 private PDO $db;
 public function __construct() {
 $this->db = Database::getConnection();
 }
 public function registrar(Usuario $usuario): bool {
 $sql = "INSERT INTO usuarios (rol_id, nombre, email, password_hash) VALUES (:rol_id, :nombre,
:email, :password_hash)";
 $stmt = $this->db->prepare($sql);
 return $stmt->execute([
 ':rol_id' => $usuario->getRolId(),
 ':nombre' => $usuario->getNombre(),
 ':email' => $usuario->getEmail(),
 ':password_hash' => password_hash($usuario->getPasswordHash(), PASSWORD_BCRYPT)
 ]);
 }
 public function obtenerPorEmail(string $email): ?array {
 $sql = "SELECT * FROM usuarios WHERE email = :email";
 $stmt = $this->db->prepare($sql);
 $stmt->execute([':email' => $email]);
 $data = $stmt->fetch();
 return $data ?: null;
 }
}
Ejemplo 2
Ejemplo con arquitectura MVC con PDO (Singleton), Modelo, Repositorio/DAO y
Controlador en PHP orientado a Objetos.
1. Conexión a la Base de Datos (Database.php)
Patrón Singleton para garantizar una única instancia de PDO en toda la aplicación.
<?php
class Database {
 private static ?PDO $instance = null;
 private function __construct() {} // Previene instanciación directa
 private function __clone() {} // Previene clonación
 public static function getInstance(): PDO {
 if (self::$instance === null) {
 $host = 'localhost';
 $db = 'sistema_usuarios';
 $user = 'root';
 $pass = '';
 $charset = 'utf8mb4';
 $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
 $options = [
 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES => false,
 ];
 self::$instance = new PDO($dsn, $user, $pass, $options);
 }
 return self::$instance;
 }
}
2. Modelo (Usuario.php)
Representa la entidad de datos y sus reglas de negocio básicas.
<?php
class Usuario {
 private ?int $id;
 private string $nombre;
 private string $email;
 public function __construct(?int $id, string $nombre, string $email) {
 $this->id = $id;
 $this->nombre = $nombre;
 $this->email = $email;
 }
 // Getters y Setters
 public function getId(): ?int { return $this->id; }
 public function getNombre(): string { return $this->nombre; }
 public function getEmail(): string { return $this->email; }

 public function setNombre(string $nombre): void { $this->nombre = $nombre; }
 public function setEmail(string $email): void { $this->email = $email; }
}
3. Repositorio / DAO (UsuarioRepository.php)
Aísla las operaciones SQL directas (Data Access Object / Repository Pattern).
<?php
require_once 'Database.php';
require_once 'Usuario.php';
class UsuarioRepository {
 private PDO $db;
 public function __construct() {
 $this->db = Database::getInstance();
 }
 public function obtenerTodos(): array {
 $stmt = $this->db->query("SELECT id, nombre, email FROM usuarios");
 $filas = $stmt->fetchAll();
 $usuarios = [];
 foreach ($filas as $fila) {
 $usuarios[] = new Usuario($fila['id'], $fila['nombre'], $fila['email']);
 }
 return $usuarios;
 }
 public function guardar(Usuario $usuario): bool {
 $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email) VALUES (:nombre, :email)");
 return $stmt->execute([
 ':nombre' => $usuario->getNombre(),
 ':email' => $usuario->getEmail()
 ]);
 }
}
4. Controlador (UsuarioController.php)
Recibe la petición HTTP, interactúa con el repositorio y coordina la respuesta/vista.
<?php
require_once 'UsuarioRepository.php';
class UsuarioController {
 private UsuarioRepository $repository;
 public function __construct() {
 $this->repository = new UsuarioRepository();
 }
 // Acción: Listar usuarios
 public function listar(): void {
 $usuarios = $this->repository->obtenerTodos();
 require 'vistas/usuarios_listado.php';
 }
 // Acción: Registrar usuario
 public function registrar(string $nombre, string $email): void {
 if (!empty($nombre) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
 $nuevoUsuario = new Usuario(null, $nombre, $email);
 $this->repository->guardar($nuevoUsuario);
 header('Location: index.php?action=listar');
 } else {
 echo "Datos inválidos.";
 }
 }
}
5. Vista (vistas/usuarios_listado.php)
Renderiza el HTML recibiendo los datos listos desde el controlador.
<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
 <title>Gestión de Usuarios</title>
</head>
<body>
 <h2>Lista de Usuarios</h2>
 <ul>
 <?php foreach ($usuarios as $usuario): ?>
 <li>
 <strong><?= htmlspecialchars($usuario->getNombre()) ?></strong>
 (<?= htmlspecialchars($usuario->getEmail()) ?>)
 </li>
 <?php endforeach; ?>
 </ul>
 <h3>Agregar Usuario</h3>
 <form action="index.php?action=registrar" method="POST">
 <input type="text" name="nombre" placeholder="Nombre" required>
 <input type="email" name="email" placeholder="Correo" required>
 <button type="submit">Guardar</button>
 </form>
</body>
</html>
6. Punto de Entrada / Front Controller (index.php)
Recibe la solicitud y ejecuta el método correspondiente del controlador.
<?php
require_once 'UsuarioController.php';
$controller = new UsuarioController();
$action = $_GET['action'] ?? 'listar';
if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
 $controller->registrar($_POST['nombre'] ?? '', $_POST['email'] ?? '');
} else {
 $controller->listar();
}
6. Implementación con Apache
La integración de la aplicación PHP en un servidor web Apache implica la configuración del
VirtualHost, la habilitación de módulos clave (mod_php o FastCGI/PHP-FPM y mod_rewrite) y la
gestión del archivo .htaccess.
Configuración de VirtualHost (/etc/apache2/sites-available/proyecto.conf)
Apache
<VirtualHost *:80>
 ServerName proyecto.local
 DocumentRoot /var/www/proyecto/public
 <Directory /var/www/proyecto/public>
 Options -Indexes +FollowSymLinks
 AllowOverride All
 Require all granted
 </Directory>
 ErrorLog ${APACHE_LOG_DIR}/proyecto_error.log
 CustomLog ${APACHE_LOG_DIR}/proyecto_access.log combined
</VirtualHost>
Archivo .htaccess (En la carpeta /public para Front Controller)
Apache
RewriteEngine On
# Redirigir todas las peticiones a index.php si no existe el archivo o directorio real
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]