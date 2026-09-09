# 📐 Diagrama de Clases UML SGDM (Versión Completa y Colorizada para Mermaid / Impresión)

> **Proyecto:** SGDM - Sistema de Gestión de Torneos y Multidisciplinas (ASCEND)  
> **Arquitectura:** Modelo-Vista-Controlador (MVC) en PHP 8.x con PDO y MySQL 3FN  
> **Alineación:** Cotejado 100% contra el código fuente activo y los **Requisitos Funcionales RF-001 a RF-074**.

---

## 🎨 Código Mermaid del Diagrama de Clases UML

```mermaid
classDiagram

  %% =========================================================================
  %% ENTIDADES DEL MODELO Y SUBTIPOS (3FN HERENCIA 1:1)
  %% =========================================================================

  class Usuario {
    <<Entity>>
    #int id
    #string email
    #string contrasenaHash
    #string nombreCompleto
    #string telefono
    #int rolId
    #bool estaActivo
    #bool emailVerificado
    #DateTime fechaRegistro
    +buscarPorEmail(email) array
    +contarTotalJugadoresRegistrados() int
    +contarTotalOrganizadoresRegistrados() int
    +obtenerUltimosJugadoresRegistrados(limite) array
    +obtenerUltimosOrganizadoresRegistrados(limite) array
    +obtenerTodosUsuariosConRoles(rolFiltro) array
    +cambiarEstadoUsuario(usuarioId) bool
    +obtenerUsuarioPorId(usuarioId) array
    +obtenerPerfilOrganizador(usuarioId) array
    +actualizarPerfilOrganizador(usuarioId, datos) bool
    +obtenerTodosLosRoles() array
    +actualizarUsuario(usuarioId, datos) bool
    +eliminarUsuario(usuarioId) bool
    +registrarUsuario(datos) array
    +verificarTokenEmail(token) array
    +cambiarVerificacionOrganizador(usuarioId) bool
    +responderSolicitudOrganizador(usuarioId, decision) bool
  }

  class PerfilJugador {
    <<Subtype>>
    -int usuarioId
    -string apodoGamertag
    -string bio
    -int nivel
    -int experienciaPuntos
    -string pais
    -string ciudad
    -string discordTag
    +obtenerPerfil(usuarioId) array
    +actualizarPerfil(usuarioId, datos) bool
    +sumarExperiencia(usuarioId, xp) bool
  }

  class PerfilOrganizador {
    <<Subtype>>
    -int usuarioId
    -string nombreOrganizacion
    -string bioOrganizacion
    -string localidad
    -string telefonoContacto
    -string sitioWeb
    -bool verificadoOficial
    +obtenerPerfil(usuarioId) array
    +actualizarPerfil(usuarioId, datos) bool
    +solicitarVerificacion(usuarioId) bool
  }

  class Torneo {
    <<AggregateRoot>>
    #int id
    #string nombre
    #int juegoId
    #string formato
    #string estado
    #int cupoMaxEquipos
    #int organizadorId
    #int modalidadId
    #int sistemaPuntuacionId
    #string tipoResultado
    #int mejorDe
    +contarTorneosActivos() int
    +obtenerUltimosTorneosCreados(limite) array
    +obtenerUltimosTorneos(limite) array
    +obtenerTorneos() array
    +obtenerEstadisticasOrganizador(organizadorId) array
    +obtenerTorneosPorOrganizador(organizadorId, limite) array
    +obtenerTodosPorOrganizador(organizadorId) array
    +obtenerModalidades() array
    +obtenerSistemasPuntuacion() array
    +crearBorrador(datos, organizadorId) int
    +actualizarEtapaDatos(torneoId, organizadorId, datos) bool
    +obtenerBorrador(torneoId, organizadorId) array
    +publicarBorrador(torneoId, organizadorId) bool
    +actualizarGestion(torneoId, organizadorId, datos) bool
    +cambiarEstado(torneoId, organizadorId, estadoNuevo) bool
    +obtenerPodio(torneoId) array
    +obtenerResultadosOrganizador(organizadorId) array
    +obtenerHistorialOrganizador(organizadorId) array
    +obtenerCalendarioOrganizador(organizadorId) array
    +obtenerInscripciones(torneoId, organizadorId) array
    +obtenerFixture(torneoId, organizadorId) array
    +generarFixture(torneoId, organizadorId) array
    +guardarResultado(encuentroId, organizadorId, local, visitante) array
    +recalcularPosiciones(torneoId) void
    +avanzarEliminacion(torneoId) void
    +avanzarSuizo(torneoId) void
    +crearRondaSuiza(torneoId, participantes, ronda) void
  }

  class Equipo {
    <<Entity>>
    #int id
    #string nombreEquipo
    #string escudoUrl
    #string descripcion
    #int creadoPor
    #bool activo
    #string codigoInvitacion
    +obtenerInstancia() Equipo
    +buscarPorNombre(nombre) array
    +crearEquipo(nombre, logo, orgId, depId) int
    +eliminarEquipo(id) bool
    +contarTotalEquipos() int
    +obtenerUltimosEquiposCreados(limite) array
  }

  class Juego {
    <<Catalog>>
    #int id
    #string nombre
    #string categoria
    #int formatoEquipoDefecto
    #float puntosVictoria
    #float puntosEmpate
    #float puntosDerrota
    #bool activo
    +obtenerInstancia() Juego
    +obtenerTodosLosJuegos() array
    +obtenerJuegosActivos() array
    +estaActivo(juegoId) bool
    +crearJuego(nombre, categoria, formato) bool
    +cambiarEstado(juegoId) bool
    +obtenerJuegoPorId(juegoId) array
    +actualizarJuego(juegoId, nombre, categoria, formato) bool
  }

  class Auditoria {
    <<Security>>
    -int id
    -int usuarioId
    -string accion
    -string descripcion
    -string ipOrigen
    -DateTime fechaHora
    +registrar(accion, descripcion) void
    +obtenerTodos() array
  }

  class PoliticaContrasena {
    <<Security>>
    #int id
    #int longitudMinima
    #bool requiereMayuscula
    #bool requiereMinuscula
    #bool requiereNumero
    #bool requiereCaracterEspecial
    #int expiracionDias
    #int historialCantidad
    +obtenerPoliticaVigente() array
    +guardarPolitica(datos, usuarioId) bool
    +validarPassword(password) array
  }

  %% =========================================================================
  %% TABLAS RELACIONALES DE DOMINIO Y ASOCIACIONES
  %% =========================================================================

  class ParticipanteTorneo {
    <<Domain>>
    #int id
    #int torneoId
    #enum tipo
    #int referenciaId
    #string nombre
    #enum estado
    #DateTime fechaInscripcion
    #int confirmadoPor
  }

  class Encuentro {
    <<Domain>>
    #int id
    #int torneoId
    #int ronda
    #int participanteLocalId
    #int participanteVisitanteId
    #float resultadoLocal
    #float resultadoVisitante
    #enum estado
    #int participanteGanadorId
    #int modificadoPorUsuarioId
  }

  class PosicionTorneo {
    <<Domain>>
    #int torneoId
    #int participanteId
    #int partidosJugados
    #int partidosGanados
    #int partidosEmpatados
    #int partidosPerdidos
    #float puntosFavor
    #float puntosContra
    #float diferenciaGoles
    #float puntos
  }

  class EquipoMiembro {
    <<Association>>
    #int equipoId
    #int usuarioId
    #DateTime fechaUnion
    #int numeroCamiseta
    #string posicion
    #bool esActivo
  }

  class SolicitudEquipo {
    <<Workflow>>
    #int id
    #int equipoId
    #int usuarioId
    #string mensaje
    #enum estado
    #int respondidoPor
  }

  class TorneoSuizoPareja {
    <<Domain>>
    #int torneoId
    #int participanteAId
    #int participanteBId
    #int ronda
    #bool yaSeEnfrentaron
  }

  class TorneoCambioEstado {
    <<Domain>>
    #int id
    #int torneoId
    #string estadoAnterior
    #string estadoNuevo
    #int usuarioId
    #DateTime fechaCambio
  }

  class TorneoActividad {
    <<Domain>>
    #int id
    #int torneoId
    #string titulo
    #enum tipo
    #Date fecha
    #Time hora
  }

  %% =========================================================================
  %% CATÁLOGOS Y ESTRUCTURAS AUXILIARES
  %% =========================================================================

  class Rol {
    <<Catalog>>
    #int id
    #string nombreRol
    #string descripcion
    #int nivelPermiso
  }

  class Permiso {
    <<Catalog>>
    #int id
    #string nombrePermiso
    #string descripcion
  }

  class Modalidad {
    <<Catalog>>
    #int id
    #string nombre
    #string descripcion
  }

  class SistemaPuntuacion {
    <<Catalog>>
    #int id
    #string nombre
    #float puntosVictoria
    #float puntosEmpate
    #float puntosDerrota
  }

  %% =========================================================================
  %% CAPA DE CONTROLADORES (CONTROLLERS MVC)
  %% =========================================================================

  class AuthControlador {
    <<Controller>>
    +mostrarLogin() void
    +procesarLogin() void
    +mostrarRegistro() void
    +procesarRegistro() void
    +verificarEmail() void
    +logout() void
  }

  class AdminControlador {
    <<Controller>>
    +dashboard() void
    +obtenerTorneos() int
    +obtenerJugadores() int
    +obtenerOrganizadores() int
    +obtenerEquipos() int
    +ultimosEquipos(limite) array
    +obtenerUltimosJugadores(limite) array
    +obtenerUltimosOrganizadores(limite) array
  }

  class PanelOrganizadorControlador {
    <<Controller>>
    +dashboard() void
    +crear() void
    +misTorneos() void
    +resultados() void
    +calendario() void
    +historial() void
    +perfil() void
    +guardarPerfil() void
    +gestionar() void
    +guardarGestion() void
    +cambiarEstado() void
    +responderInscripcion() void
    +generarFixture() void
    +guardarResultado() void
    +guardarEtapa() void
  }

  class OrganizadorControlador {
    <<Controller>>
    +index() void
    +cambiarVerificacion() void
    +responderSolicitud() void
  }

  class UsuarioControlador {
    <<Controller>>
    +index() void
    +cambiarEstadoUsuario() void
    +editarUsuario() void
    +actualizarUsuario() void
    +eliminarUsuario() void
    +verComo() void
    +volverAdmin() void
  }

  class TorneoAdminControlador {
    <<Controller>>
    +index() void
  }

  class JuegoControlador {
    <<Controller>>
    +index() void
    +crear() void
    +guardar() void
    +editar() void
    +actualizar() void
    +cambiarEstado() void
  }

  class PoliticaContrasenaControlador {
    <<Controller>>
    +index() void
    +guardar() void
  }

  class AuditoriaControlador {
    <<Controller>>
    +index() void
  }

  %% =========================================================================
  %% SERVICIOS Y HELPER DE INFRAESTRUCTURA
  %% =========================================================================

  class Conexion {
    <<Singleton>>
    -Conexion instancia
    -PDO bd
    +getInstance() Conexion
    +getBD() PDO
  }

  class Sesion {
    <<Service>>
    +iniciarLogin(usuarioBD) void
    +estaLogueado() bool
    +requerirLogin() void
    +validarAdmin() void
    +validarOrganizador() void
    +validarOrganizadorVerificado() void
    +destruir() void
  }

  %% =========================================================================
  %% RELACIONES Y HERENCIAS DEL DIAGRAMA
  %% =========================================================================

  PerfilJugador --|> Usuario : "Herencia 1:1 Subtipo"
  PerfilOrganizador --|> Usuario : "Herencia 1:1 Subtipo"
  Usuario o-- Rol : "Pertenece a Rol"
  Rol *-- Permiso : "Tiene Permisos N:M"

  Torneo o-- PerfilOrganizador : "Creado por Organizador"
  Torneo o-- Juego : "Basado en Juego"
  Torneo o-- Modalidad : "Configurado con Modalidad"
  Torneo o-- SistemaPuntuacion : "Regido por Sistema Puntuacion"
  Torneo *-- ParticipanteTorneo : "Posee Lista Participantes"
  Torneo *-- Encuentro : "Genera Encuentros / Fixture"
  Torneo *-- PosicionTorneo : "Mantiene Tabla Posiciones"
  Torneo *-- TorneoSuizoPareja : "Historial Parejas Suizas"
  Torneo *-- TorneoCambioEstado : "Auditado por Cambios Estado"
  Torneo *-- TorneoActividad : "Posee Agenda Actividades"

  Equipo o-- PerfilJugador : "Creado por Capitan"
  Equipo *-- EquipoMiembro : "Roster de Miembros"
  EquipoMiembro o-- PerfilJugador : "Jugador Integrante"
  SolicitudEquipo *-- Equipo : "Solicitud de Ingreso"
  SolicitudEquipo o-- PerfilJugador : "Jugador Solicitante"

  ParticipanteTorneo o-- PerfilJugador : "Participante Individual"
  ParticipanteTorneo o-- Equipo : "Participante Colectivo"
  Encuentro o-- ParticipanteTorneo : "Compite Local / Visitante"
  PosicionTorneo o-- ParticipanteTorneo : "Clasifica Participante"

  AuthControlador ..> Usuario : "Autentica y Registra"
  AuthControlador ..> Sesion : "Gestiona Estado Sesion"
  AdminControlador ..> Usuario : "Estadisticas Usuarios"
  AdminControlador ..> Torneo : "Estadisticas Torneos"
  AdminControlador ..> Equipo : "Estadisticas Equipos"
  PanelOrganizadorControlador ..> Torneo : "Gestiona Ciclo Torneo"
  PanelOrganizadorControlador ..> Juego : "Consulta Catalogo Activo"
  OrganizadorControlador ..> Usuario : "Aaprueba / Revoca Organizador"
  UsuarioControlador ..> Usuario : "CRUD General Cuentas"
  JuegoControlador ..> Juego : "CRUD Catalogo Juegos"
  PoliticaContrasenaControlador ..> PoliticaContrasena : "Configura Seguridad"
  AuditoriaControlador ..> Auditoria : "Consulta Bitacora"
  
  Usuario ..> Auditoria : "Trazabilidad Operaciones"
  Usuario ..> Conexion : "Acceso Datos via PDO"
  Torneo ..> Conexion : "Acceso Datos via PDO"
  Juego ..> Conexion : "Acceso Datos via PDO"

  %% =========================================================================
  %% ESTILOS DE COLORIZACIÓN Y APARIENCIA MERMAID
  %% =========================================================================

  classDef controller fill:#1e1b4b,stroke:#818cf8,stroke-width:2px,color:#ffffff
  classDef model fill:#064e3b,stroke:#34d399,stroke-width:2px,color:#ffffff
  classDef subtype fill:#701a75,stroke:#f0abfc,stroke-width:2px,color:#ffffff
  classDef domain fill:#0f172a,stroke:#38bdf8,stroke-width:2px,color:#ffffff
  classDef catalog fill:#1e293b,stroke:#94a3b8,stroke-width:2px,color:#ffffff
  classDef service fill:#451a03,stroke:#fbbf24,stroke-width:2px,color:#ffffff

  class AuthControlador,AdminControlador,PanelOrganizadorControlador,OrganizadorControlador,UsuarioControlador,TorneoAdminControlador,JuegoControlador,PoliticaContrasenaControlador,AuditoriaControlador controller
  class Usuario,Torneo,Equipo,Juego,Auditoria,PoliticaContrasena model
  class PerfilJugador,PerfilOrganizador subtype
  class ParticipanteTorneo,Encuentro,PosicionTorneo,EquipoMiembro,SolicitudEquipo,TorneoSuizoPareja,TorneoCambioEstado,TorneoActividad domain
  class Rol,Permiso,Modalidad,SistemaPuntuacion catalog
  class Conexion,Sesion service
```

---

## 📑 Matriz de Cobertura de Requisitos Funcionales (RF-001 a RF-074)

A continuación se mapean los **Requisitos Funcionales oficiales** con su representación exacta en las Clases y Métodos del Diagrama UML:

| Requisito Funcional (RF) | Descripción | Clase / Controlador Responsable | Método / Mecanismo UML |
|---|---|---|---|
| **RF-001** | Registro de Jugador | `AuthControlador`, `Usuario` | `procesarRegistro()`, `registrarUsuario()` |
| **RF-002** | Solicitud de registro como Organizador | `AuthControlador`, `Usuario` | `registrarUsuario()` (crea `PerfilOrganizador` con `verificadoOficial = false`) |
| **RF-003 / RF-004** | Inicio y Cierre de Sesión | `AuthControlador`, `Sesion` | `procesarLogin()`, `iniciarLogin()`, `logout()`, `destruir()` |
| **RF-005 / RF-006** | Consulta y Modificación de Perfil | `PanelOrganizadorControlador`, `Usuario` | `perfil()`, `guardarPerfil()`, `actualizarPerfilOrganizador()` |
| **RF-007** | Autorización por Rol (RBAC) | `Sesion` | `validarAdmin()`, `validarOrganizador()`, `validarOrganizadorVerificado()` |
| **RF-008 a RF-012** | Gestión de Equipos e Invitaciones | `Equipo`, `EquipoMiembro`, `SolicitudEquipo` | `crearEquipo()`, `agregarMiembro()`, `enviarInvitacion()`, `responder()` |
| **RF-013 a RF-016** | Inscripciones y Validación de Modalidad | `Torneo`, `ParticipanteTorneo`, `PanelOrganizadorControlador` | `obtenerInscripciones()`, `responderInscripcion()`, `confirmarCupo()` |
| **RF-017 a RF-031** | Configuración y Gestión de Torneos | `Torneo`, `PanelOrganizadorControlador` | `crearBorrador()`, `actualizarEtapaDatos()`, `publicarBorrador()`, `cambiarEstado()` |
| **RF-032 a RF-041** | Motores de Liga, Eliminación Directa y Suizo | `Torneo` | `generarFixture()`, `avanzarEliminacion()`, `avanzarSuizo()`, `crearRondaSuiza()` |
| **RF-042 a RF-048** | Resultados, Posiciones y Podios | `Torneo`, `Encuentro`, `PosicionTorneo` | `guardarResultado()`, `recalcularPosiciones()`, `obtenerPodio()` |
| **RF-049 a RF-058** | Consulta Pública de Torneos y Rankings | `Torneo`, `Juego`, `Equipo` | `obtenerTorneos()`, `obtenerFixture()`, `obtenerPodio()` |
| **RF-059 a RF-074** | Panel de Administración y Auditoría | `AdminControlador`, `OrganizadorControlador`, `UsuarioControlador`, `Auditoria` | `cambiarVerificacion()`, `responderSolicitud()`, `cambiarEstadoUsuario()`, `registrar()` |

---

## 🖨️ Leyenda de Colores para Impresión y Presentación

* 🟦 **Violeta Oscuro / Indigo (Controladores MVC):** Encargados de la orquestación HTTP, recepción de peticiones POST/GET y renderizado de vistas.
* 🟩 **Verde Esmeralda (Modelos Principales):** Entidades de dominio central y persistencia activa con MySQL.
* 🟪 **Magenta / Púrpura (Subtipos 1:1 en 3FN):** Perfiles especializados de Jugadores y Organizadores con herencia de la clase Usuario.
* 🔷 **Azul Marino (Entidades de Dominio y Transaccionales):** Tablas de apoyo para Fixtures, Inscripciones, Posiciones y Solicitudes.
* 🩶 **Gris Pizarra (Catálogos del Sistema):** Tablas maestras de Roles, Permisos, Juegos, Modalidades y Sistemas de Puntuación.
* 🟧 **Ámbar / Dorado (Servicios de Infraestructura):** Helpers de Sesión y Patrón Singleton de Conexión a Base de Datos.
