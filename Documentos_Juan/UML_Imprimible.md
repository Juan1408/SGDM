# Diagrama UML SGDM (Versión para Imprimir)

```mermaid
classDiagram
class Usuario {
  <<Entity Model>>
  #int id
  #int rolId
  #string nombreCompleto
  #string email
  #string contrasenaHash
  #bool estaActivo
  #DateTime fechaCreacion
  +obtenerPorId(int $id) Usuario
  +obtenerPorEmail(string $email) Usuario
  +crear(array $datos) int
  +actualizar(int $id, array $datos) bool
  +cambiarEstado(int $id, bool $activo) bool
  +verificarPassword(string $passwordPlana) bool
}
Usuario --|> PerfilJugador : Herencia 1:1 (Subtipo Jugador)
Usuario --|> PerfilOrganizador : Herencia 1:1 (Subtipo Organizador)
Usuario o-- Rol : Asignado a 1 Rol (N:1)
Usuario ..> Auditoria : Dispara registro en Auditoría
class PerfilJugador {
  <<Subtype Entity>>
  -int usuarioId
  -string gamertag
  -string biografia
  -string nivelHabilidad
  -int puntosExperiencia
  -string pais
  +obtenerPerfil(int $usuarioId) array
  +actualizarGamertag(int $usuarioId, string $tag) bool
  +sumarExperiencia(int $usuarioId, int $xp) bool
}
PerfilJugador --|> Usuario : Hereda de Usuario (1:1)
PerfilJugador *-- EquipoMiembro : Integra Equipos (1:N)
class PerfilOrganizador {
  <<Subtype Entity>>
  -int usuarioId
  -string razonSocial
  -string sitioWeb
  -string telefono
  -string localidad
  -bool esVerificado
  +obtenerOrganizador(int $usuarioId) array
  +solicitarVerificacion(int $usuarioId) bool
}
PerfilOrganizador --|> Usuario : Hereda de Usuario (1:1)
PerfilOrganizador *-- Torneo : Crea y Organiza Torneos (1:N)
class Rol {
  <<Catalog Entity>>
  #int id
  #string nombreRol
  #int nivelPermiso
  +obtenerTodos() array
  +obtenerPermisos(int $rolId) array
}
Rol o-- Usuario : Asigna Rol a Usuario (1:N)
class Torneo {
  <<Aggregate Root Entity>>
  #int id
  #int organizadorId
  #int juegoId
  #int modalidadId
  #string nombre
  #enum tipoFormato
  #enum estado
  #int maxParticipantes
  +crearTorneo(array $datos) int
  +cambiarEstado(int $id, string $estado) bool
  +obtenerParticipantes(int $id) array
  +generarFixture(int $id) bool
}
Torneo o-- PerfilOrganizador : Organizado por 1 Organizador
Torneo o-- Juego : Basado en 1 Juego
Torneo *-- Encuentro : Contiene N Encuentros (1:N)
Torneo *-- PosicionTorneo : Tiene 1 Tabla de Posiciones
Torneo *-- ParticipanteTorneo : Tiene N Participantes
Torneo ..> ModuloLiga : Usa Estrategia Liga
Torneo ..> ModuloEliminacion : Usa Estrategia Eliminación
Torneo ..> ModuloSuizo : Usa Estrategia Suizo
class ModuloLiga {
  <<Tournament Engine>>
  -int=3 puntosVictoria
  -int=1 puntosEmpate
  -int=0 puntosDerrota
  +generarFixtureTodosContraTodos(int $torneoId) bool
  +recalcularTablaPosiciones(int $torneoId) bool
}
ModuloLiga ..> Torneo : Ejecuta reglas para Torneo
ModuloLiga ..> PosicionTorneo : Actualiza Posiciones
class ModuloEliminacion {
  <<Tournament Engine>>
  -int totalRondas
  -bool tieneTercerPuesto
  +generarArbolLlaves(int $torneoId) bool
  +avanzarGanador(int $encuentroId, int $ganadorId) bool
}
ModuloEliminacion ..> Torneo : Ejecuta reglas para Torneo
ModuloEliminacion ..> Encuentro : Crea y conecta Encuentros
class ModuloSuizo {
  <<Tournament Engine>>
  -int totalRondas
  -string='Buchholz' criterioDesempate
  +generarRondaSuiza(int $torneoId, int $rondaNumero) bool
  +calcularBuchholz(int $torneoId, int $participanteId) float
}
ModuloSuizo ..> Torneo : Ejecuta reglas para Torneo
class Encuentro {
  <<Entity Model>>
  #int id
  #int torneoId
  #int rondaNumero
  #int participanteLocalId
  #int participanteVisitaId
  #int marcadorLocal
  #int marcadorVisita
  #int ganadorId
  #enum estado
  +registrarMarcador(int $id, int $local, int $visita) bool
  +validarResultado(int $id, int $arbitroId) bool
}
Encuentro *-- Torneo : Pertenece a 1 Torneo
Encuentro o-- ParticipanteTorneo : Enfrenta a 2 Participantes
class PosicionTorneo {
  <<Entity Model>>
  #int id
  #int torneoId
  #int participanteId
  #int=0 partidosJugados
  #int=0 victorias
  #int=0 empates
  #int=0 derrotas
  #int=0 puntos
  #int=0 diferenciaGoles
  +obtenerTablaOrdenada(int $torneoId) array
}
PosicionTorneo *-- Torneo : Tabla oficial de 1 Torneo
class ParticipanteTorneo {
  <<Association Entity>>
  #int id
  #int torneoId
  #int usuarioId
  #int equipoId
  #enum estadoInscripcion
  #int semillaRank
  +inscribir(int $torneoId, int $userId, int $teamId) bool
  +confirmarInscripcion(int $id) bool
}
ParticipanteTorneo *-- Torneo : Inscripto en Torneo
ParticipanteTorneo o-- Usuario : Jugador Individual
ParticipanteTorneo o-- Equipo : Equipo Grupal
class Equipo {
  <<Entity Model>>
  #int id
  #int capitanId
  #string nombre
  #string tag
  #string logoUrl
  +crearEquipo(array $datos) int
  +transferirCapitania(int $equipoId, int $nuevoCapitanId) bool
}
Equipo o-- PerfilJugador : Capitán del Equipo
Equipo *-- EquipoMiembro : Tiene N Miembros (1:N)
class EquipoMiembro {
  <<Association Entity>>
  #int id
  #int equipoId
  #int jugadorId
  #enum rolEnEquipo
  +agregarMiembro(int $equipoId, int $jugadorId) bool
  +removerMiembro(int $equipoId, int $jugadorId) bool
}
EquipoMiembro *-- Equipo : Roster del Equipo
class Juego {
  <<Catalog Entity>>
  #int id
  #string nombre
  #enum categoria
  +listarJuegos() array
  +crearJuego(array $datos) int
}
Juego o-- Torneo : Catálogo para Torneo
class Auditoria {
  <<Security Entity (OWASP)>>
  -int id
  -int usuarioId
  -string tablaAfectada
  -int registroId
  -enum accion
  -string datosPreviosJson
  -string datosNuevosJson
  -string direccionIp
  -DateTime fechaRegistro
  +registrar(string $tabla, int $regId, string $accion, array $previo, array $nuevo) bool
  +listarLogs(int $limit = 100) array
}
Auditoria o-- Usuario : Registra acciones de Usuarios
class ControladorBase {
  <<Abstract Controller>>
  #renderizar(string $vista, array $datos = []) void
  #responderJson(array $datos, int $status = 200) void
  #parametro(string $clave, $default = null)
}
ControladorBase --|> AuthControlador : Hereda ControladorBase
ControladorBase --|> AdminControlador : Hereda ControladorBase
ControladorBase --|> TorneoControlador : Hereda ControladorBase
class AuthControlador {
  <<Controller>>
  +login() void
  +autenticar() void
  +registro() void
  +logout() void
}
AuthControlador --|> ControladorBase : Hereda de ControladorBase
AuthControlador ..> Usuario : Usa Modelo Usuario
AuthControlador ..> Sesion : Inicia Sesión
AuthControlador ..> Validador : Valida Inputs
class AdminControlador {
  <<Controller>>
  +dashboard() void
  +configuracion() void
  +exportarReporte() void
}
AdminControlador --|> ControladorBase : Hereda de ControladorBase
AdminControlador ..> Sesion : Valida Sesion::requerirRol('admin')
AdminControlador ..> Auditoria : Consulta Logs
class TorneoControlador {
  <<Controller>>
  +index() void
  +crear() void
  +generarFixture() void
}
TorneoControlador --|> ControladorBase : Hereda de ControladorBase
TorneoControlador ..> Torneo : Usa Modelo Torneo
class Permiso {
  <<Catalog Entity>>
  #int id
  #string nombrePermiso
  #string descripcion
  +listarPermisos() array
}
Permiso o-- Rol : Asociado a Roles (N:M)
class Modalidad {
  <<Catalog Entity>>
  #int id
  #string nombreModalidad
  #int minJugadoresPorEquipo
  +obtenerPorJuego(int $juegoId) array
}
Modalidad o-- Juego : Pertenece a Juego
class SolicitudEquipo {
  <<Workflow Entity>>
  #int id
  #int equipoId
  #int jugadorId
  #enum estado
  +enviarInvitacion(int $eqId, int $jugId) bool
  +responder(int $solId, bool $aceptar) bool
}
SolicitudEquipo *-- Equipo : Solicitud para Equipo
class UsuarioControlador {
  <<Controller>>
  +index() void
  +crear() void
  +cambiarEstado() void
}
UsuarioControlador --|> ControladorBase : Hereda de ControladorBase
UsuarioControlador ..> Usuario : Gestiona Modelo Usuario
class EncuentroControlador {
  <<Controller>>
  +guardarMarcador() void
  +validarArbitraje() void
}
EncuentroControlador --|> ControladorBase : Hereda de ControladorBase
EncuentroControlador ..> Encuentro : Usa Modelo Encuentro
class Conexion {
  <<Singleton Pattern>>
  -Conexion=null instancia
  -PDO pdo
  +getInstancia() Conexion
  +getDb() PDO
}
class Sesion {
  <<Security Service>>
  +iniciar(array $usuario) void
  +getUsuarioId() int
  +esAdmin() bool
  +requerirRol(string $rol) void
  +destruir() void
}
class Validador {
  <<Validation Service>>
  +requerido(string $valor) bool
  +emailValido(string $email) bool
  +enteroEnRango(int $v, int $min, int $max) bool
  +sanitizar(string $cadena) string
}
```
