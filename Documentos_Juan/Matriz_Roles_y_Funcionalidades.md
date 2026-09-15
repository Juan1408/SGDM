# 📋 Matriz Integral de Roles, Tareas y Funcionalidades
## Proyecto SGDM ASCEND (Sistema de Gestión Deportiva y Multideportiva)

> **Propósito del Documento:**  
> Este documento define y desglosa exhaustivamente todas las tareas, funcionalidades, casos de uso y reglas de negocio del sistema **SGDM ASCEND**, detallando a qué rol corresponden, cómo se integran en la arquitectura MVC (*Modelo-Vista-Controlador*) y cuáles son los controles de seguridad y validaciones aplicadas.  
> 
> Diseñado como guía de referencia oficial para el equipo de desarrollo y soporte documental para la defensa del proyecto ante el comité evaluador.

---

## 📑 Índice de Roles del Sistema

1. [Visión General de la Jerarquía y Control de Acceso (RBAC)](#1-visión-general-de-la-jerarquía-y-control-de-acceso-rbac)
2. [Rol 1: Administrador General (Superusuario del Sistema)](#2-rol-1-administrador-general-superusuario-del-sistema)
3. [Rol 2: Organizador de Torneos (Gestor de Competencias)](#3-rol-2-organizador-de-torneos-gestor-de-competencias)
4. [Rol 3: Jugador / Participante (Competidor Individual)](#4-rol-3-jugador--participante-competidor-individual)
5. [Rol 4: Capitán de Equipo (Gestor de Escuadras y Rosters)](#5-rol-4-capitán-de-equipo-gestor-de-escuadras-y-rosters)
6. [Rol 5: Usuario Público / Visitante (Consulta y Seguimiento)](#6-rol-5-usuario-público--visitante-consulta-y-seguimiento)
7. [Procesos Automáticos del Sistema (Motor Backend)](#7-procesos-automáticos-del-sistema-motor-backend)
8. [Matriz Resumen de Permisos CRUD por Entidad](#8-matriz-resumen-de-permisos-crud-por-entidad)
9. [Límites y Exclusiones Formales del Alcance](#9-límites-y-exclusiones-formales-del-alcance)

---

## 1. Visión General de la Jerarquía y Control de Acceso (RBAC)

El sistema implementa un modelo **RBAC (*Role-Based Access Control* - Control de Acceso Basado en Roles)**, garantizando el principio de menor privilegio (*Principle of Least Privilege - PoLP*).

```
                            ┌─────────────────────────┐
                            │  ADMINISTRADOR GENERAL  │ (Nivel 100 - Control Total)
                            └────────────┬────────────┘
                                         │ Supervisa y Audita
                                         ▼
                            ┌─────────────────────────┐
                            │  ORGANIZADOR DE TORNEO  │ (Nivel 50 - Gestión de sus Torneos)
                            └────────────┬────────────┘
                                         │ Administra Encuentros
                                         ▼
                    ┌─────────────────────────────────────────┐
                    │          JUGADOR / PARTICIPANTE         │ (Nivel 10 - Competidor)
                    │  ┌───────────────────────────────────┐  │
                    │  │   CAPITÁN DE EQUIPO (Especial)    │  │ (Gestiona Roster de Equipo)
                    │  └───────────────────────────────────┘  │
                    └────────────────────┬────────────────────┘
                                         │ Consulta Libre
                                         ▼
                            ┌─────────────────────────┐
                            │    USUARIO PÚBLICO      │ (Nivel 0 - Solo Lectura)
                            └─────────────────────────┘
```

---

## 2. Rol 1: Administrador General (Superusuario del Sistema)

### 2.1 Perfil y Responsabilidad
Es el responsable de la salud operativa, seguridad y configuración global de la plataforma. Posee permisos irrestrictos de administración, gestión de usuarios, auditoría forense y control de catálogo.

### 2.2 Tabla Detallada de Tareas y Funcionalidades

| ID | Funcionalidad / Tarea | Descripción Técnica | Capa MVC Involucrada | Tablas Afectadas | Reglas de Negocio y Validaciones |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **ADM-01** | **Dashboard Estadístico Global** | Visualización de métricas en tiempo real: total usuarios, torneos activos, partidos jugados y alertas de seguridad. | `AdminControlador::dashboard()` ➔ `Usuario`, `Torneo`, `Encuentro` | `usuarios`, `torneos`, `torneo_encuentros` | Agregaciones SQL optimizadas (`COUNT(*)`, `SUM()`) con caché en memoria. |
| **ADM-02** | **Gestión Integral de Usuarios (CRUD)** | Alta de cuentas administrativas, edición de datos, reseteo de contraseñas y desactivación lógica (*Soft Delete*). | `UsuarioControlador` ➔ `Usuario`, `Rol` | `usuarios`, `perfiles_jugadores`, `perfiles_organizadores` | Validación de email único, hash seguro con `password_hash(PASSWORD_BCRYPT)`. |
| **ADM-03** | **Asignación de Roles y Permisos** | Cambio de nivel de acceso de usuarios (ej. promover Jugador a Organizador). | `RolControlador::asignar()` ➔ `Rol` | `usuarios(rol_id)`, `rol_permisos` | No se permite que un administrador se desactive a sí mismo si es el único superadmin. |
| **ADM-04** | **Catálogo de Juegos y Modalidades** | Creación y mantenimiento de disciplinas (Ajedrez, Fútbol 5, League of Legends, CS:GO) y formatos (1v1, 5v5). | `JuegoControlador` ➔ `Juego`, `Modalidad` | `juegos`, `modalidades` | No se puede eliminar un juego si existen torneos históricos vinculados (`ON DELETE RESTRICT`). |
| **ADM-05** | **Supervisión y Cancelación de Torneos** | Capacidad de intervenir cualquier torneo ante irregularidades, pausarlo o darlo de baja forzada. | `TorneoAdminControlador::cambiarEstado()` | `torneos`, `torneo_cambio_estado` | Registro obligatorio del motivo de cancelación en el log histórico. |
| **ADM-06** | **Consulta de Auditoría Forense (OWASP)** | Visualización inmutable de todas las acciones críticas ejecutadas en el sistema con filtrado por fecha, IP y usuario. | `AuditoriaControlador::index()` ➔ `Auditoria` | `auditoria_cambios` | Prohibido terminantemente cualquier `UPDATE` o `DELETE` sobre la tabla de auditoría (tabla de solo inserción). |
| **ADM-07** | **Configuración Global de Políticas** | Ajustes de complejidad de contraseñas, tiempo de expiración de sesiones y activación del modo mantenimiento. | `AdminControlador::guardarConfig()` | `politicas_contrasenas`, `torneo_config` | Validar rangos numéricos (ej. expiración de sesión entre 15 y 1440 minutos). |

---

## 3. Rol 2: Organizador de Torneos (Gestor de Competencias)

### 3.1 Perfil y Responsabilidad
Entidad o persona verificada encargada de planificar, crear y dirigir competencias deportivas, mentales o electrónicas dentro del SGDM.

### 3.2 Tabla Detallada de Tareas y Funcionalidades

| ID | Funcionalidad / Tarea | Descripción Técnica | Capa MVC Involucrada | Tablas Afectadas | Reglas de Negocio y Validaciones |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **ORG-01** | **Creación de Competencia** | Asistente de creación de torneo definiendo nombre, juego, formato (Liga, Eliminación, Suizo), fechas y cupos. | `TorneoControlador::crear()` ➔ `Torneo` | `torneos`, `torneo_config` | Fecha de fin debe ser posterior a fecha de inicio. Cupo mínimo de 2 participantes. |
| **ORG-02** | **Gestión de Inscripciones** | Aprobación o rechazo de solicitudes de inscripción enviadas por jugadores o equipos. | `InscripcionControlador::gestionar()` | `participantes_torneo` | No permitir inscripciones si el torneo alcanzó `max_participantes` o el estado no es `inscripcion`. |
| **ORG-03** | **Generación de Fixture / Llaves** | Disparo del algoritmo generador de emparejamientos según el formato seleccionado: | `FixtureControlador::generar()` ➔ `ModuloLiga`, `ModuloEliminacion`, `ModuloSuizo` | `torneo_encuentros`, `torneo_posiciones`, `torneo_suizo_parejas` | Requiere cupo mínimo completo. Formato eliminación directa requiere potencia de 2 ($2^n$) o generación de *Byes*. |
| | ↳ *Formato Liga (Round Robin)* | Algoritmo *Berger Round-Robin* (todos contra todos en ida o ida/vuelta). | | | Cada participante enfrenta a todos los demás exactamente una vez por rueda. |
| | ↳ *Formato Eliminación Directa* | Creación de árbol binario de llaves (*Brackets*) desde Cuartos/Octavos a Final. | | | Los perdedores quedan eliminados inmediatamente; los ganadores avanzan de ronda. |
| | ↳ *Formato Sistema Suizo* | Emparejamiento por puntuación acumulada evitando cruces repetidos. | | | Ningún participante repite rival en rondas sucesivas; cálculo de desempate *Buchholz*. |
| **ORG-04** | **Carga y Validación de Marcadores** | Ingreso de resultados de los encuentros (goles, sets, rondas, jaques) y asignación de ganador o empate. | `EncuentroControlador::guardarResultado()` | `torneo_encuentros`, `resultados_detalle` | Solo se puede cargar si el encuentro está `en_juego` o `pendiente`. No se permiten marcadores negativos. |
| **ORG-05** | **Cierre de Torneo y Premiación** | Finalización formal del evento, congelamiento de posiciones finales y acreditación de puntos de experiencia (XP). | `TorneoControlador::finalizar()` | `torneos`, `perfiles_jugadores(puntos_experiencia)` | Requiere que el 100% de los encuentros de la última ronda estén validados. |

---

## 4. Rol 3: Jugador / Participante (Competidor Individual)

### 4.1 Perfil y Responsabilidad
Usuario que compite en torneos individuales (1v1) o forma parte de escuadras grupales.

### 4.2 Tabla Detallada de Tareas y Funcionalidades

| ID | Funcionalidad / Tarea | Descripción Técnica | Capa MVC Involucrada | Tablas Afectadas | Reglas de Negocio y Validaciones |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **JUG-01** | **Gestión de Perfil de Jugador** | Personalización de Gamertag, biografía, redes de contacto y visualización de historial deportivo. | `PerfilJugadorControlador::editar()` ➔ `PerfilJugador` | `perfiles_jugadores`, `usuarios` | Gamertag alfanumérico único de 3 a 30 caracteres. |
| **JUG-02** | **Inscripción a Torneos Individuales** | Postulación a torneos 1v1 en estado de inscripción abierta. | `InscripcionControlador::postular()` | `participantes_torneo` | Un jugador no puede inscribirse dos veces al mismo torneo. Debe cumplir requisitos de juego. |
| **JUG-03** | **Mi Calendario y Notificaciones** | Consulta personalizada de próximos partidos, horarios asignados y rivales de turno. | `JugadorControlador::miCalendario()` | `torneo_encuentros`, `notificaciones` | Filtro por `usuario_id` autenticado mediante sesión activa. |
| **JUG-04** | **Consulta de Estadísticas Personales** | Visualización de porcentaje de victorias (*Winrate*), puntos acumulados y torneos disputados. | `JugadorControlador::estadisticas()` | `torneo_posiciones`, `torneo_encuentros` | Cálculo dinámico derivado de encuentros oficiales finalizados. |

---

## 5. Rol 4: Capitán de Equipo (Gestor de Escuadras y Rosters)

### 5.1 Perfil y Responsabilidad
Rol especial conferido a un jugador que crea y lidera un equipo o clan multideportivo.

### 5.2 Tabla Detallada de Tareas y Funcionalidades

| ID | Funcionalidad / Tarea | Descripción Técnica | Capa MVC Involucrada | Tablas Afectadas | Reglas de Negocio y Validaciones |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **CAP-01** | **Creación y Edición de Equipo** | Registro de escuadra con nombre, etiqueta (*TAG* de 3 a 5 caracteres) y logo representativo. | `EquipoControlador::crear()` ➔ `Equipo` | `equipos`, `equipo_capitanes`, `equipo_miembros` | TAG único en el sistema. El creador se convierte automáticamente en Capitán titular. |
| **CAP-02** | **Gestión de Roster e Invitaciones** | Envío de invitaciones a jugadores, aceptación de solicitudes entrantes y remoción de integrantes. | `EquipoControlador::gestionarMiembros()` | `equipo_miembros`, `solicitudes_equipo` | Un jugador no puede estar duplicado en el mismo equipo. Respetar límite de miembros. |
| **CAP-03** | **Inscripción de Equipo a Torneos** | Postulación oficial del equipo a torneos grupales (5v5, 3v3, etc.). | `InscripcionControlador::inscribirEquipo()` | `participantes_torneo(equipo_id)` | El equipo debe contar con el número mínimo de integrantes exigido por la modalidad del juego. |
| **CAP-04** | **Traspaso de Capitanía** | Cesión voluntaria del rol de capitán a otro miembro activo del equipo. | `EquipoControlador::transferirCapitania()` | `equipo_capitanes`, `equipos` | El nuevo capitán debe ser miembro confirmado del equipo. |

---

## 6. Rol 5: Usuario Público / Visitante (Consulta y Seguimiento)

### 6.1 Perfil y Responsabilidad
Cualquier persona que navega en la plataforma sin necesidad de registrarse ni iniciar sesión.

### 6.2 Tabla Detallada de Tareas y Funcionalidades

| ID | Funcionalidad / Tarea | Descripción Técnica | Capa MVC Involucrada | Tablas Afectadas | Reglas de Negocio y Validaciones |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **PUB-01** | **Explorador y Buscador de Torneos** | Catálogo interactivo con filtros por juego, estado (En curso, Próximos, Finalizados) y modalidad. | `PublicoControlador::explorarTorneos()` | `torneos`, `juegos`, `modalidades` | Solo se muestran torneos que NO estén en estado `borrador` o `cancelado`. |
| **PUB-02** | **Visualización de Llaves (*Brackets*)** | Consulta interactiva del árbol de eliminación directa en tiempo real. | `PublicoControlador::verLlaves()` | `torneo_encuentros` | Renderizado dinámico en frontend con SVG/Canvas de conectores de llaves. |
| **PUB-03** | **Consulta de Tablas de Posiciones** | Tabla de clasificación oficial ordenada por Puntos, Diferencia de Goles/Puntos y Partidos Ganados. | `PublicoControlador::verPosiciones()` | `torneo_posiciones` | `ORDER BY puntos DESC, diferencia_goles DESC, victorias DESC`. |
| **PUB-04** | **Consulta de Perfiles Públicos** | Visualización de ficha informativa de equipos y jugadores (gamertag, estadísticas públicas, logros). | `PublicoControlador::verPerfil()` | `equipos`, `perfiles_jugadores` | Ocultar datos privados (correos electrónicos, IPs, contraseñas). |

---

## 7. Procesos Automáticos del Sistema (Motor Backend)

### 7.1 Tareas Desencadenadas por Eventos (*Event-Driven Engine*)

| ID | Proceso Automático | Evento Disparador (*Trigger*) | Acción del Sistema | Lógica Aplicada |
| :--- | :--- | :--- | :--- | :--- |
| **SYS-01** | **Recálculo de Tabla de Posiciones** | Validación de un resultado en torneo tipo Liga. | Actualiza `torneo_posiciones`. | Suma 3 puntos por victoria, 1 por empate, recalcula goles a favor, en contra y diferencia. |
| **SYS-02** | **Avance de Llave de Eliminación** | Validación del ganador de un encuentro de llave. | Asigna `participante_id` al encuentro de la siguiente ronda. | Encuentra el encuentro padre mediante `siguiente_encuentro_id` e inserta al ganador. |
| **SYS-03** | **Emparejamiento Suizo Siguiente Ronda** | Cierre de la ronda $N$ en torneo Suizo. | Ejecuta algoritmo de parejas. | Ordena por puntos acumulados, empareja vecinos en tabla y verifica que no hayan jugado antes. |
| **SYS-04** | **Registro Automático de Auditoría** | Cualquier `INSERT`, `UPDATE` o `DELETE` en tablas críticas. | Inserta fila en `auditoria_cambios`. | Captura `usuario_id`, tabla, IP, estado JSON previo y nuevo mediante disparadores/helpers. |

---

## 8. Matriz Resumen de Permisos CRUD por Entidad

> **Leyenda:**  
> **C** = *Create (Crear)* | **R** = *Read (Leer / Consultar)* | **U** = *Update (Modificar)* | **D** = *Delete (Eliminar / Desactivar)* | **—** = *Sin Acceso*

| Entidad / Módulo del SGDM | Administrador General | Organizador de Torneo | Capitán de Equipo | Jugador / Participante | Usuario Público |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Usuarios y Roles** | **C - R - U - D** | — | — | R (Propio) - U (Propio) | — |
| **Catálogo de Juegos** | **C - R - U - D** | R | R | R | R |
| **Torneos Propios** | **C - R - U - D** | **C - R - U - D** | R | R | R |
| **Torneos Ajenos** | **R - U - D** | R | R | R | R |
| **Inscripciones / Cupos** | **C - R - U - D** | **C - R - U - D** | C (Equipo) - R | C (Individual) - R | R |
| **Encuentros y Fixtures** | **C - R - U - D** | **C - R - U** | R | R | R |
| **Carga de Marcadores** | **C - R - U - D** | **C - R - U** | — | — | — |
| **Equipos y Rosters** | **C - R - U - D** | R | **C - R - U - D** | R | R |
| **Auditoría y Logs** | **R** | — | — | — | — |

---

## 9. Límites y Exclusiones Formales del Alcance

Para garantizar la fidelidad con los términos del proyecto institucional y evitar desviaciones de alcance (*Scope Creep*), el SGDM **excluye explícitamente**:

1. ❌ **Procesamiento de pagos y pasarelas bancarias** (las inscripciones se gestionan como estados administrativos).
2. ❌ **Integración con redes sociales externas** (autenticación nativa local por base de datos).
3. ❌ **Inteligencia artificial embebida** (los emparejamientos y tablas se basan en algoritmos matemáticos deterministas: Berger, Single Elimination y Swiss-System).
4. ❌ **Arbitraje automático** (la validación de resultados requiere confirmación humana del organizador o admin).
5. ❌ **Sistemas de apuestas o pronósticos monetarios**.
6. ❌ **Venta y ticketing de entradas**.
7. ❌ **Control de acceso físico a eventos o molinetes**.
