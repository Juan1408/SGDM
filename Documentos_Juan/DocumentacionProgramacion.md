# 📖 DOCUMENTACIÓN OFICIAL DEL PROYECTO SGDM — ASCEND

## Sistema de Gestión Deportiva Modular — Plataforma ASCEND

> **Versión:** 2.0 — Segunda Entrega  
> **Fecha:** 14 de septiembre de 2026  
> **Equipo:** Grupo ASCEND — 3° BT Tecnologías de la Información  
> **Institución:** Instituto Tecnológico Superior Arias-Balparda  

---

# FASE 1 — INICIO DEL PROYECTO (Días 1 a 15)

---

## 1.1 Acta de Constitución del Proyecto (Project Charter)

### Información General

| Campo | Detalle |
|---|---|
| **Nombre del Proyecto** | SGDM — Sistema de Gestión Deportiva Modular (Plataforma ASCEND) |
| **Patrocinador** | Instituto Tecnológico Superior Arias-Balparda — Coordinación de 3° BT TI |
| **Director de Proyecto** | Líder del equipo ASCEND (asignado por el grupo) |
| **Fecha de Inicio** | 14 de mayo de 2026 (Día 1) |
| **Fecha de Cierre Estimada** | 9 de noviembre de 2026 (Día 180 calendario / Día 120 efectivo) |
| **Presupuesto Estimado** | USD 0 en infraestructura (entorno académico con XAMPP/Apache) |

### Justificación del Proyecto

El proyecto surge de una necesidad identificada en el ecosistema deportivo y recreativo: la fragmentación de herramientas para gestionar torneos de múltiples disciplinas. Actualmente, los organizadores de competencias se ven obligados a utilizar planillas manuales, aplicaciones dispersas y formatos no estandarizados para gestionar participantes, calendarios, resultados y posiciones. SGDM ASCEND propone una solución web modular, unificada y extensible que centralice la administración de torneos deportivos, mentales y electrónicos bajo una misma plataforma.

### Objetivos SMART

| Dimensión | Objetivo |
|---|---|
| **S** (Específico) | Desarrollar una plataforma web MVC en PHP 8.x con MySQL que gestione torneos en formatos Liga, Eliminación Directa y Sistema Suizo para 9 disciplinas |
| **M** (Medible) | Implementar el 100% de los 74 requisitos funcionales (RF-001 a RF-074) y los 29 requisitos no funcionales (RNF01 a RNF29) |
| **A** (Alcanzable) | Equipo de 3-4 desarrolladores con acceso a XAMPP, GitHub y documentación técnica completa |
| **R** (Relevante) | Integra las 5 asignaturas del plan curricular (Fullstack, Ingeniería de Software, Admin SO, Ciberseguridad, Tutoría UTULAB) |
| **T** (Temporal) | Tres entregas parciales (27/07, 14/09, 09/11) con defensa final del 23 al 25 de noviembre de 2026 |

### Restricciones

1. **Tecnológicas:** PHP 8.x, MySQL, HTML/CSS/JS. No se permiten frameworks externos (Laravel, Symfony) ni CMSs.
2. **Temporales:** Calendario académico fijo con 3 entregas inmutables coordinadas entre 4 escuelas.
3. **Presupuestarias:** Cero inversión monetaria — todo el stack es open-source y el despliegue local en XAMPP.
4. **Organizativas:** Equipo de 3 a 4 integrantes con roles rotativos.

### Asunciones

1. Todos los integrantes tienen acceso a un equipo con XAMPP instalado.
2. La comunicación del equipo se realiza vía Discord y GitHub.
3. El correo electrónico para verificación de cuentas se simula en entorno de desarrollo.
4. El despliegue final se realizará mediante Docker (requisito de tercera entrega).

---

## 1.2 Resumen Ejecutivo del Proyecto

**ASCEND** es una plataforma web de gestión de torneos multidisciplinarios que permite a administradores, organizadores y jugadores interactuar en un ecosistema competitivo organizado. El sistema soporta 9 disciplinas (Fútbol, Básquetbol, Tenis, League of Legends, Counter-Strike 1.6, EA Sports FC 26, Truco, Catán y Ajedrez) en tres formatos de competencia (Liga, Eliminación Directa y Sistema Suizo).

La arquitectura se basa en el patrón Modelo-Vista-Controlador con PHP 8.x, una base de datos MySQL normalizada en Tercera Forma Normal (3FN) con herencia de tablas (Class Table Inheritance), y un sistema de autenticación y autorización basado en roles (RBAC) con tres perfiles diferenciados: Administrador, Organizador y Jugador.

El proyecto integra las competencias de 5 unidades curriculares del Tramo 8 del Bachillerato Tecnológico en Tecnologías de la Información.

---

## 1.3 Caso de Negocio (Business Case)

### Problema Detectado

| Aspecto | Situación Actual | Impacto |
|---|---|---|
| **Fragmentación de herramientas** | Los organizadores usan planillas Excel, Google Sheets, WhatsApp y documentos dispersos | Errores en calendarios, resultados duplicados, falta de historial |
| **Falta de automatización** | Los brackets y emparejamientos se generan manualmente | Tiempo excesivo de administración, errores en el sorteo |
| **Nula trazabilidad** | No hay registro centralizado de resultados históricos ni rankings | Imposibilidad de medir progreso de jugadores o equipos |
| **Barrera de acceso** | Las plataformas existentes (Challonge, Toornament) son en inglés y/o de pago | Exclusión de organizadores pequeños y comunidades locales |

### Retorno Esperado

- **Reducción del 80%** en el tiempo de administración de un torneo respecto al método manual.
- **Eliminación total** de errores humanos en la generación de brackets y cálculo de posiciones.
- **Acceso democrático**: plataforma gratuita, en español, accesible desde cualquier navegador.
- **Valor académico**: Integración práctica de 5 asignaturas en un producto funcional demostrable.

---

## 1.4 Agenda de la Reunión de Inicio (Kick-off Meeting)

| N° | Punto del Orden del Día | Responsable | Duración |
|---|---|---|---|
| 1 | Lectura y análisis de la letra del proyecto (`Proyecto.md`) | Todo el equipo | 30 min |
| 2 | Asignación de roles dentro del equipo (Backend, Frontend, DBA, QA) | Líder de proyecto | 15 min |
| 3 | Definición del nombre del sistema y logotipo (ASCEND) | Todo el equipo | 20 min |
| 4 | Elección del ciclo de vida del proyecto (Modelo Híbrido Scrum-Waterfall) | Líder de proyecto | 15 min |
| 5 | Configuración del repositorio Git y estructura de ramas | Responsable de DevOps | 15 min |
| 6 | Definición de herramientas de comunicación (Discord + GitHub Issues) | Todo el equipo | 10 min |
| 7 | Establecimiento de las reglas del grupo (mínimo 7) | Todo el equipo | 15 min |
| 8 | Asignación de primeras tareas para la Primera Entrega | Líder de proyecto | 10 min |

### Acuerdos Alcanzados

1. El sistema se llamará **ASCEND** (A Sports Competition Engine for Networked Disciplines).
2. Se usará una rama `main` protegida, con ramas de feature (`feat/`) para cada módulo.
3. Las reuniones internas serán semanales (mínimo), con actas formales.
4. El estilo de código sigue PSR-12 y la prohibición de estilos CSS inline.
5. La base de datos se diseñará en 3FN estricta con herencia de tablas.

---

## 1.5 Registro de Interesados (Stakeholder Register)

| ID | Interesado | Rol | Organización | Contacto | Expectativa Principal |
|---|---|---|---|---|---|
| STK-01 | Profesores evaluadores | Patrocinador / Evaluador | ITS Arias-Balparda | Presencial | Producto funcional que demuestre competencias del Tramo 8 |
| STK-02 | Equipo ASCEND (3-4 alumnos) | Desarrolladores | 3° BT TI | Discord / GitHub | Aprobación del proyecto y aprendizaje técnico |
| STK-03 | Administrador del sistema | Usuario final (rol Admin) | ASCEND Platform | Web App | Control total de la plataforma, auditoría y gestión de usuarios |
| STK-04 | Organizadores de torneos | Usuario final (rol Organizador) | Externos | Web App | Crear, configurar y gestionar torneos de forma autónoma |
| STK-05 | Jugadores / Participantes | Usuario final (rol Jugador) | Externos | Web App | Inscribirse a torneos, consultar resultados y rankings |
| STK-06 | Público general | Visitante (sin registro) | Internet | Web App | Consultar torneos, resultados y posiciones públicas |
| STK-07 | Coordinación docente | Supervisor | ITS Arias-Balparda | Presencial | Cumplimiento del reglamento y cronograma académico |

---

## 1.6 Análisis de Interesados — Matriz de Poder vs. Interés

```
            ALTO PODER
                │
    STK-01 ◉   │   ◉ STK-07
   (Profesores) │  (Coordinación)
                │
  ──────────────┼──────────────
                │
    STK-02 ◉   │   ◉ STK-03
    (Equipo)    │  (Admin ASCEND)
                │
                │   ◉ STK-04
                │  (Organizadores)
                │
                │   ◉ STK-05
                │  (Jugadores)
            BAJO PODER
                │   ◉ STK-06
                │  (Público)
       BAJO     │        ALTO
      INTERÉS   │      INTERÉS
```

| Cuadrante | Estrategia | Interesados |
|---|---|---|
| Alto Poder / Alto Interés | **Gestionar de cerca** | STK-01, STK-07 |
| Alto Poder / Bajo Interés | **Mantener satisfechos** | — |
| Bajo Poder / Alto Interés | **Mantener informados** | STK-02, STK-03, STK-04, STK-05 |
| Bajo Poder / Bajo Interés | **Monitorear** | STK-06 |

---

## 1.7 Documento de Viabilidad

### Viabilidad Técnica

| Componente | Tecnología | Justificación |
|---|---|---|
| Servidor Web | Apache 2.4 (XAMPP) | Incluido en XAMPP, configuración cero. Compatible con PHP 8.x |
| Lenguaje Backend | PHP 8.x | Requerido por la letra del proyecto. Soporta POO, namespaces, tipos estrictos |
| Base de Datos | MySQL 8.0 (MariaDB) | Motor relacional incluido en XAMPP. Soporte de InnoDB, FK, transacciones |
| Frontend | HTML5, CSS3, JavaScript ES6 | Tecnologías nativas requeridas. Sin dependencia de frameworks |
| Control de Versiones | Git + GitHub | Gratuito, accesible, requerido por la asignatura Tutoría UTULAB |
| Contenedores | Docker | Requerido para la tercera entrega. Imágenes oficiales de PHP y MySQL |

### Viabilidad Económica

| Rubro | Costo | Observación |
|---|---|---|
| Infraestructura de desarrollo | $0 | XAMPP es gratuito y corre en los equipos personales del equipo |
| Hosting (desarrollo) | $0 | `localhost` vía Apache |
| Licencias de software | $0 | Todo el stack es open-source (PHP, MySQL, Apache, Git) |
| Herramientas de diseño | $0 | Mermaid (diagramas), draw.io (DER), VS Code (IDE) |
| Horas hombre estimadas | 480 h/h | 4 integrantes × 120 días × ~1 hora/día promedio |

### Viabilidad Operativa

- El equipo tiene experiencia previa en HTML/CSS/JS (primer y segundo año del BT).
- PHP fue introducido en la asignatura de Programación Fullstack.
- La metodología Scrum fue estudiada en Ingeniería de Software.
- El entorno XAMPP es conocido por todos los integrantes.

### Viabilidad Legal

- No se procesan pagos ni datos financieros.
- Los datos personales almacenados son mínimos (nombre, email, contraseña hasheada).
- Se aplica el principio de minimización de datos conforme a buenas prácticas de protección de datos personales.
- Las contraseñas se almacenan con hash bcrypt, nunca en texto plano.

---

## 1.8 Declaración del Alcance Preliminar

### Dentro del Alcance

- Sistema de autenticación y autorización con 3 roles (Administrador, Organizador, Jugador).
- Gestión completa de torneos en 3 formatos (Liga, Eliminación Directa, Sistema Suizo).
- 9 disciplinas configuradas: Fútbol, Básquetbol, Tenis, LoL, CS 1.6, EA Sports FC 26, Truco, Catán, Ajedrez.
- Generación automática de fixtures, brackets y emparejamientos.
- Tabla de posiciones y rankings por disciplina.
- Panel de administración con CRUD completo de usuarios, equipos y torneos.
- Consulta pública sin registro de torneos, resultados y clasificaciones.
- Registro de auditoría de operaciones.
- Despliegue mediante Docker (tercera entrega).

### Fuera del Alcance (Exclusiones Explícitas)

Según [Proyecto.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Proyecto.md) § 9:

- Procesamiento de pagos o integración con pasarelas de pago.
- Integración con redes sociales.
- Inteligencia artificial embebida.
- Arbitraje automático.
- Sistema de apuestas.
- Venta de entradas.
- Control de acceso físico a eventos.

---

## 1.9 Registro de Requisitos (Requirement Gathering)

### Metodología de Relevamiento

Se utilizó un enfoque híbrido de relevamiento:

1. **Análisis documental**: Estudio de la letra del proyecto (`Proyecto.md`) y los requisitos distinguidos por asignatura.
2. **Brainstorming estructurado**: Sesiones internas del equipo para identificar casos de uso no explícitos.
3. **Benchmarking**: Análisis de plataformas existentes (Challonge, Toornament, Start.gg) para identificar funcionalidades esperadas.
4. **Cuestionario**: Documento [cuestionario.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/cuestionario.md) con preguntas dirigidas a potenciales usuarios.

### Resumen de Requisitos Levantados

| Tipo | Cantidad | Documento |
|---|---|---|
| **Requisitos Funcionales** | 74 (RF-001 a RF-074) | [RequisitosFuncionales.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/RequisitosFuncionales.md) |
| **Requisitos No Funcionales** | 29 (RNF01 a RNF29) | [RequerimientosNoFuncionales.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/RequerimientosNoFuncionales.md) |

Los requisitos funcionales están organizados en 9 módulos:

1. Módulo de Usuarios y Autenticación (RF-001 a RF-007)
2. Módulo de Gestión de Participantes y Equipos (RF-008 a RF-016)
3. Módulo de Torneos (RF-017 a RF-034)
4. Módulo de Liga (RF-035 a RF-036)
5. Módulo de Eliminación Directa (RF-037 a RF-038)
6. Módulo de Sistema Suizo (RF-039 a RF-041)
7. Módulo de Resultados (RF-042 a RF-048)
8. Módulo de Consulta Pública (RF-049 a RF-058)
9. Módulo de Administración del Sistema (RF-059 a RF-074)

---

# FASE 2 — PLANIFICACIÓN DEL PROYECTO (Días 16 a 45)

---

## 2.1 Plan de Gestión del Proyecto

### Enfoque Metodológico: Modelo Híbrido Scrum + Waterfall

El proyecto adopta un enfoque híbrido:

- **Waterfall** para las fases de documentación, diseño de base de datos y arquitectura inicial, donde los entregables son secuenciales y dependientes.
- **Scrum** para la fase de ejecución del código, con sprints de 2 semanas, dailies asíncronas vía Discord y retrospectivas al cierre de cada sprint.

| Aspecto | Decisión |
|---|---|
| Duración del Sprint | 2 semanas |
| Daily Standup | Asíncrono vía canal Discord (#daily-updates) |
| Sprint Review | Al cierre de cada sprint, con demostración de funcionalidad |
| Sprint Retrospective | Documentada en actas de reunión |
| Product Backlog | GitHub Issues + archivos `task_*.md` en `Documentos_Juan/` |
| Definition of Done | Código funcional, sin inline styles, con validación frontend+backend, pushed a la rama correspondiente |

---

## 2.2 Estructura de Desglose del Trabajo (EDT / WBS)

```
SGDM — ASCEND
├── 1. ARQUITECTURA Y DISEÑO
│   ├── 1.1 Diseño de Base de Datos (3FN, DER, Diccionario)
│   ├── 1.2 Diseño UML (Clases, Casos de Uso)
│   ├── 1.3 Arquitectura MVC (estructura de carpetas)
│   └── 1.4 Documentación de Seguridad (DCL, RBAC)
│
├── 2. BACKEND (PHP 8.x MVC)
│   ├── 2.1 Módulo de Autenticación (Login, Registro, Verificación Email)
│   ├── 2.2 Módulo de Administración (Dashboard, CRUD Usuarios, Aprobación Organizadores)
│   ├── 2.3 Módulo de Organizador (Creación de Torneos, Gestión, Resultados)
│   ├── 2.4 Módulo de Jugador (Dashboard, Equipos, Inscripciones)
│   ├── 2.5 Motor de Competencia (Liga, Eliminación Directa, Suizo)
│   └── 2.6 Módulo de Auditoría (Logs de actividad)
│
├── 3. FRONTEND (HTML5/CSS3/JS)
│   ├── 3.1 Maquetado Mobile First
│   ├── 3.2 Vistas de Autenticación (Login, Registro)
│   ├── 3.3 Panel de Administración
│   ├── 3.4 Panel de Organizador
│   ├── 3.5 Panel de Jugador
│   └── 3.6 Vistas Públicas
│
├── 4. BASE DE DATOS
│   ├── 4.1 Script SQL (ascend.sql)
│   ├── 4.2 DCL y Seguridad
│   ├── 4.3 Datos de Prueba (Seeds)
│   └── 4.4 Procedimientos y Vistas SQL
│
├── 5. INFRAESTRUCTURA
│   ├── 5.1 Configuración Apache/XAMPP
│   ├── 5.2 Scripts de Respaldo (bash + cron)
│   ├── 5.3 Sistema de Monitoreo
│   └── 5.4 Dockerización (Tercera Entrega)
│
└── 6. DOCUMENTACIÓN Y CIERRE
    ├── 6.1 Documentación Funcional
    ├── 6.2 Documentación Técnica
    ├── 6.3 Manual de Usuario
    ├── 6.4 Plan de Testing
    └── 6.5 Defensa Final
```

---

## 2.3 Presupuesto del Proyecto

### Estimación por Horas Hombre

| Rubro | Horas Estimadas | Costo Unitario (Referencia mercado Jr. UY) | Costo Total (Teórico) |
|---|---|---|---|
| Diseño de BD y Arquitectura | 60 h | UYU 350/h | UYU 21,000 |
| Desarrollo Backend PHP | 180 h | UYU 400/h | UYU 72,000 |
| Desarrollo Frontend HTML/CSS/JS | 120 h | UYU 350/h | UYU 42,000 |
| Documentación técnica y funcional | 60 h | UYU 300/h | UYU 18,000 |
| Testing y QA | 40 h | UYU 300/h | UYU 12,000 |
| Infraestructura y DevOps | 20 h | UYU 450/h | UYU 9,000 |
| **TOTAL** | **480 h** | | **UYU 174,000** (~USD 4,000) |

### Infraestructura

| Recurso | Costo Real | Proveedor |
|---|---|---|
| XAMPP (Apache + MySQL + PHP) | Gratuito | Apache Friends |
| Visual Studio Code | Gratuito | Microsoft |
| GitHub (repositorio privado) | Gratuito | GitHub Inc. |
| Docker Desktop | Gratuito (uso educativo) | Docker Inc. |
| Discord (comunicación) | Gratuito | Discord Inc. |
| Draw.io / Mermaid (diagramas) | Gratuito | Open Source |

---

## 2.4 Cronograma del Proyecto — Diagrama de Hitos

| Hito | Fecha | Día | Estado |
|---|---|---|---|
| 📌 Inicio del proyecto | 14/05/2026 | 1 | ✅ Completado |
| 📌 Kickoff Meeting y conformación de equipo | 21/05/2026 | 7 | ✅ Completado |
| 📌 Diseño de Base de Datos (DER + 3FN) | 04/06/2026 | 21 | ✅ Completado |
| 📌 Maquetado Frontend Mobile First | 25/06/2026 | 42 | ✅ Completado |
| 📌 **PRIMERA ENTREGA** | **27/07/2026** | **74** | ✅ Completado |
| 📌 Backend MVC — Autenticación funcional | 10/08/2026 | 88 | ✅ Completado |
| 📌 Backend MVC — Panel Admin + Organizador | 31/08/2026 | 109 | ✅ Completado |
| 📌 Motor de Torneos (Liga, ED, Suizo) | 07/09/2026 | 116 | ✅ Completado |
| 📌 Integración y merge de ramas | 08/09/2026 | 117 | ✅ Completado |
| 📌 **SEGUNDA ENTREGA** | **14/09/2026** | **123** | 🔄 En curso |
| 📌 Docker + Datos de prueba (50 reg/tabla) | 20/10/2026 | 159 | ⏳ Pendiente |
| 📌 Testing + Plan de seguridad OWASP | 31/10/2026 | 170 | ⏳ Pendiente |
| 📌 **TERCERA ENTREGA (impresa)** | **09/11/2026** | **179** | ⏳ Pendiente |
| 📌 **DEFENSAS** | **23-25/11/2026** | **193-195** | ⏳ Pendiente |

---

## 2.5 Plan de Gestión del Alcance

### Procedimiento de Control de Alcance

1. **Solicitud de cambio**: Cualquier integrante puede proponer un cambio mediante una Issue en GitHub o un mensaje formal en Discord.
2. **Evaluación de impacto**: El líder de proyecto evalúa el impacto en tiempo, código existente y documentación.
3. **Aprobación**: Cambios menores (< 2 horas de esfuerzo) son aprobados por el líder. Cambios mayores requieren consenso del equipo.
4. **Registro**: Todo cambio aprobado se documenta en el Registro de Cambios (§ 3.2).
5. **Implementación**: Se crea una rama `feat/` específica para el cambio.

### Control de Scope Creep

- Los requisitos funcionales RF-001 a RF-074 son el baseline aprobado.
- Las funcionalidades opcionales (Proyecto.md § 10) solo se abordan si el baseline está al 100%.
- Cualquier requisito nuevo debe ser cotejado contra la letra del proyecto antes de su aceptación.

---

## 2.6 Plan de Gestión de Riesgos

### Matriz de Riesgos (Probabilidad × Impacto)

| ID | Riesgo | Probabilidad | Impacto | P×I | Plan de Mitigación | Plan de Contingencia |
|---|---|---|---|---|---|---|
| R-01 | Conflictos de merge en Git que corrompan código funcional | Alta (4) | Alto (4) | 16 | Ramas por módulo, merges frecuentes a `feat/backend-mvc` | Restaurar desde commit anterior vía `git reflog` |
| R-02 | Un integrante abandona el equipo | Media (3) | Alto (4) | 12 | Documentación completa de cada módulo para onboarding rápido | Redistribución de tareas entre los integrantes restantes |
| R-03 | Pérdida de datos de la base de datos de desarrollo | Baja (2) | Crítico (5) | 10 | Script SQL versionado en Git (`ascend.sql`) | Re-ejecutar el script SQL desde el repositorio |
| R-04 | Retraso en la entrega de un módulo específico | Media (3) | Medio (3) | 9 | Sprints de 2 semanas con seguimiento en Discord | Priorizar funcionalidades core sobre opcionales |
| R-05 | Sesiones PHP no funcionan correctamente tras merge | Media (3) | Medio (3) | 9 | `Sesion.php` centralizado como servicio RBAC | Revisión manual de `$_SESSION` y regeneración de IDs |
| R-06 | Incompatibilidad de versiones PHP entre equipos | Baja (2) | Medio (3) | 6 | Todos usan XAMPP con PHP 8.x | Docker como entorno unificado (tercera entrega) |
| R-07 | El motor de torneos genera fixtures incorrectos | Media (3) | Alto (4) | 12 | Testing manual con 4, 5, 8, 16 participantes | Algoritmo de fallback con validación de integridad |

### Escala de Valoración

- **Probabilidad:** 1 (Muy Baja) — 2 (Baja) — 3 (Media) — 4 (Alta) — 5 (Muy Alta)
- **Impacto:** 1 (Negligible) — 2 (Bajo) — 3 (Medio) — 4 (Alto) — 5 (Crítico)

---

## 2.7 Plan de Gestión de Cambios

### Flujo de Solicitud de Cambio

```
[Solicitante] → [GitHub Issue / Discord] → [Líder evalúa impacto]
                                                    │
                                          ┌─────────┴─────────┐
                                          │                   │
                                    Cambio Menor         Cambio Mayor
                                    (< 2h esfuerzo)     (> 2h esfuerzo)
                                          │                   │
                                   Aprobación Líder    Consenso Equipo
                                          │                   │
                                          └─────────┬─────────┘
                                                    │
                                          [Crear rama feat/]
                                          [Implementar]
                                          [Pull Request]
                                          [Merge]
                                          [Documentar CR]
```

---

## 2.8 Plan de Gestión de Recursos

### Asignación de Roles en el Equipo

| Rol Técnico | Responsable | Módulo(s) Asignado(s) |
|---|---|---|
| **Arquitecto Backend / DBA** | Juan (Líder) | Administrador, Base de datos, Arquitectura MVC, Documentación |
| **Desarrollador Backend** | Compañero A | Módulo Organizador (Panel, Torneos, Resultados) |
| **Desarrollador Backend** | Compañero B | Módulo Jugador (Panel, Equipos, Inscripciones) |
| **Desarrollador Frontend / QA** | Compañero C (si aplica) | Maquetado Mobile First, Vistas públicas, Testing |

---

## 2.9 Plan de Comunicación del Proyecto

| Canal | Propósito | Frecuencia | Participantes |
|---|---|---|---|
| **Discord #general** | Comunicación diaria, dudas rápidas | Diario | Todo el equipo |
| **Discord #daily-updates** | Standup asíncrono (qué hice, qué haré, bloqueos) | Diario | Todo el equipo |
| **GitHub Issues** | Registro formal de bugs y tareas | Por evento | Todo el equipo |
| **GitHub Pull Requests** | Revisión de código antes de merge | Por feature | Autor + Revisor |
| **Reuniones presenciales** | Sprint Review, Planning, Retrospectiva | Semanal | Todo el equipo |
| **Correo institucional** | Comunicación con docentes | Por necesidad | Líder → Docentes |

---

## 2.10 Matriz RACI

| Entregable | Juan (Líder/Arq.) | Compañero A (Org.) | Compañero B (Jug.) | Docentes |
|---|---|---|---|---|
| Diseño BD (`ascend.sql`) | **R, A** | C | C | I |
| UML Diagrama de Clases | **R, A** | C | C | I |
| Backend Auth (Login/Registro) | **R** | C | C | I |
| Backend Admin Panel | **R, A** | I | I | I |
| Backend Organizador | C | **R, A** | I | I |
| Backend Jugador/Equipos | C | I | **R, A** | I |
| Motor de Torneos (Liga/ED/Suizo) | **R** | **R** | I | I |
| Frontend Maquetado | C | C | **R** | I |
| Documentación técnica | **R, A** | C | C | I |
| Testing e integración | **R, A** | **R** | **R** | I |
| Docker y despliegue | **R** | C | C | I |

> **R** = Responsable, **A** = Aprobador, **C** = Consultado, **I** = Informado

---

## 2.11 Plan de Adquisiciones

| Recurso | Tipo | Licencia | Fuente |
|---|---|---|---|
| XAMPP 8.2+ | Software | Apache License 2.0 | apachefriends.org |
| PHP 8.x | Runtime | PHP License | php.net |
| MySQL / MariaDB | RDBMS | GPL v2 | mysql.com / mariadb.org |
| Apache HTTP Server | Web Server | Apache License 2.0 | httpd.apache.org |
| Visual Studio Code | IDE | MIT License | code.visualstudio.com |
| Git | VCS | GPL v2 | git-scm.com |
| GitHub | Hosting VCS | Gratuito (plan Free) | github.com |
| Docker | Contenedores | Apache License 2.0 | docker.com |
| Mermaid.js | Diagramas | MIT License | mermaid.js.org |
| Draw.io | Diagramas | Apache License 2.0 | diagrams.net |

---

## 2.12 Plan de Calidad

### Estándares Aplicados

| Área | Estándar | Implementación |
|---|---|---|
| **Código PHP** | PSR-12 (Extended Coding Style) | Namespaces, camelCase para métodos, PascalCase para clases |
| **CSS** | Prohibición de inline styles (`style=""`) | Archivos CSS externos por módulo |
| **SQL** | 3FN con integridad referencial | FK con ON DELETE CASCADE/SET NULL/RESTRICT según contexto |
| **Seguridad** | OWASP Top 10 | Prepared statements (PDO), bcrypt, regeneración de session ID, validación de entradas |
| **Arquitectura** | MVC estricto | Separación en carpetas `modelos/`, `vistas/`, `controladores/`, `ayudantes/`, `configuracion/` |

### Criterios de Aceptación de Código

1. Sin errores de sintaxis PHP (error reporting E_ALL en desarrollo).
2. Sin atributos `style=""` en HTML.
3. Todas las consultas SQL usan prepared statements.
4. Las contraseñas se hashean con `password_hash()`.
5. Los controladores validan permisos vía `Sesion::requerirRol()`.
6. Los formularios tienen validación frontend Y backend. En el registro, el correo se valida además en el servidor con `FILTER_VALIDATE_EMAIL` (controlador y modelo); `type=email` es solo una ayuda del navegador.

---

## 2.13 Plan de Gestión de Interesados

| Interesado | Nivel de Engagement | Estrategia |
|---|---|---|
| **Profesores evaluadores** | Líder | Entregas puntuales, documentación impecable, demos en cada entrega |
| **Equipo ASCEND** | Supportive | Reuniones semanales, dailies asíncronas, retrospectivas |
| **Usuarios finales (hipotéticos)** | Neutral | Diseño centrado en el usuario, interfaces intuitivas, mensajes claros |

---

## 2.14 Plan de Gestión del Cronograma

### Tolerancias de Desvío

| Tipo de Tarea | Tolerancia Máxima | Acción si se Excede |
|---|---|---|
| Tarea de sprint (feature) | +3 días | Reasignar o simplificar alcance de la feature |
| Entrega parcial académica | 0 días (fecha inmutable) | Entregar lo disponible y documentar pendientes |
| Merge de rama | +1 día | Resolución inmediata de conflictos con apoyo del equipo |

---

## 2.15 Plan de Gestión de Costos

### Control del Valor Ganado (EVM Conceptual)

| Métrica | Definición | Valor al Día 120 |
|---|---|---|
| **BAC** (Budget at Completion) | 480 horas-hombre totales planificadas | 480 h |
| **PV** (Planned Value) | Trabajo planificado al día 120: 100% de ejecución | 360 h (75%) |
| **EV** (Earned Value) | Trabajo efectivamente completado | ~300 h (63%) |
| **AC** (Actual Cost) | Horas realmente invertidas | ~320 h |
| **SPI** (Schedule Performance Index) | EV / PV | 0.83 (leve retraso) |
| **CPI** (Cost Performance Index) | EV / AC | 0.94 (eficiencia aceptable) |

> El retraso se debe a la complejidad del motor de torneos (subestimado) y la resolución de conflictos de merge entre ramas.

---

# FASE 3 — EJECUCIÓN DEL PROYECTO (Días 46 a 90)

---

## 3.1 Plan de Ejecución del Proyecto

### Sprints Realizados

| Sprint | Período | Objetivo | Resultado |
|---|---|---|---|
| Sprint 1 | Días 46-60 | Base de datos 3FN + Modelo `Conexion.php` Singleton | ✅ Completado — [ascend.sql](file:///c:/xampp/htdocs/SGDM/base_de_datos/ascend.sql) con 30+ tablas |
| Sprint 2 | Días 61-74 | Autenticación completa (Login, Registro, Verificación Email) | ✅ Completado — [AuthControlador.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/controladores/AuthControlador.php) |
| Sprint 3 | Días 75-88 | Panel de Administración + CRUD Usuarios | ✅ Completado — [AdminControlador.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/controladores/AdminControlador.php), [UsuarioControlador.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/controladores/UsuarioControlador.php) |
| Sprint 4 | Días 89-102 | Panel de Organizador + Creación de Torneos | ✅ Completado — [panelOrganizadorControlador.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/controladores/panelOrganizadorControlador.php) |
| Sprint 5 | Días 103-116 | Motor de torneos (Liga, ED, Suizo) + Panel Jugador | ✅ Completado — [Torneo.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/Torneo.php) (50KB), [panelJugadorControlador.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/controladores/panelJugadorControlador.php) |
| Sprint 6 | Días 117-123 | Integración, merge de ramas, documentación | 🔄 En curso |

---

## 3.2 Solicitudes de Cambio (Change Requests)

| CR-ID | Fecha | Solicitante | Descripción | Impacto | Estado |
|---|---|---|---|---|---|
| CR-001 | Día 65 | Equipo | Simular verificación de email en desarrollo en lugar de SMTP real | Bajo — Solo afecta entorno dev | ✅ Aprobado e implementado |
| CR-002 | Día 78 | Juan | Adoptar herencia Class Table Inheritance (1:1) para `perfiles_jugadores` y `perfiles_organizadores` en lugar de columnas NULL en `usuarios` | Alto — Rediseño de 3 tablas | ✅ Aprobado e implementado |
| CR-003 | Día 95 | Compañero A | Unificar `Torneo.php` como modelo central para Organizador y Jugador en lugar de dos modelos separados | Medio — Refactoring de controladores | ✅ Aprobado e implementado |
| CR-004 | Día 110 | Juan | Agregar política de contraseñas dinámica administrable por el Admin | Bajo — Nuevo modelo + controlador | ✅ Aprobado e implementado ([PoliticaContrasena.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/PoliticaContrasena.php)) |
| CR-005 | Día 117 | Equipo | Agregar botón de rechazar organizador (además de aprobar) en panel admin | Bajo — Modificación de vista y controlador | ✅ Aprobado e implementado |

---

## 3.3 Órdenes de Cambio — Impacto en Tiempo y Código

| CR-ID | Archivos Modificados | Tiempo Invertido | Impacto en Cronograma |
|---|---|---|---|
| CR-002 | `ascend.sql`, `Usuario.php`, `AuthControlador.php` | 8 horas | +2 días en Sprint 2 |
| CR-003 | `Torneo.php`, `panelOrganizadorControlador.php`, `panelJugadorControlador.php` | 6 horas | +1 día en Sprint 5 |

---

## 3.4 Hojas de Tiempo (Resumen de Esfuerzo por Módulo)

| Módulo | Horas Estimadas | Horas Reales | Variación |
|---|---|---|---|
| Base de datos (diseño + SQL) | 40 h | 50 h | +25% (complejidad de herencia 3FN) |
| Autenticación y RBAC | 30 h | 35 h | +17% (verificación email simulada) |
| Panel Administrador | 50 h | 45 h | -10% (más eficiente de lo esperado) |
| Panel Organizador | 60 h | 70 h | +17% (motor de torneos complejo) |
| Panel Jugador + Equipos | 50 h | 55 h | +10% (invitaciones y solicitudes) |
| Frontend / Maquetado | 80 h | 40 h | -50% (prioridad al backend) |
| Documentación | 40 h | 35 h | -13% |
| **TOTAL** | **350 h** | **330 h** | **-6%** |

---

## 3.5 Informes de Estado del Proyecto

### Status Report — Día 60

| Indicador | Valor |
|---|---|
| **Progreso General** | 25% |
| **Backend** | 15% (Autenticación en desarrollo) |
| **Base de Datos** | 90% (SQL casi final) |
| **Frontend** | 40% (Maquetado HTML completado) |
| **Documentación** | 50% (Requisitos y diccionarios listos) |
| **Riesgos Activos** | R-01 (merges), R-04 (retrasos) |
| **Bloqueos** | Ninguno |

### Status Report — Día 90

| Indicador | Valor |
|---|---|
| **Progreso General** | 55% |
| **Backend** | 60% (Auth + Admin + Organizador parcial) |
| **Base de Datos** | 100% (SQL final con seeds parciales) |
| **Frontend** | 45% (Vistas PHP integradas con backend) |
| **Documentación** | 60% |
| **Riesgos Activos** | R-01, R-07 |
| **Bloqueos** | Conflictos de merge entre ramas `feat/jugador-backend` y `feat/backend-mvc` |

---

## 3.6 Registro de Problemas (Issue Log)

| Issue-ID | Fecha | Descripción | Severidad | Estado | Resolución |
|---|---|---|---|---|---|
| ISS-001 | Día 85 | Organizador podía crear torneos sin estar aprobado por el Admin | Alta | ✅ Resuelto | Validación en `panelOrganizadorControlador.php`: verificar `estado_organizador === 'aprobado'` antes de mostrar formulario |
| ISS-002 | Día 92 | Sesiones expiraban sin mensaje al usuario | Media | ✅ Resuelto | Timeboxing de 30 min en `Sesion.php` con redirección al login |
| ISS-003 | Día 110 | Conflictos de merge en `Sesion.php` entre ramas jugador y admin | Alta | ✅ Resuelto | Merge manual conservando funciones de ambos módulos |
| ISS-004 | Día 110 | `Torneo.php` con conflictos de merge — métodos duplicados | Alta | ✅ Resuelto | Unificación de métodos bajo secciones comentadas (Organizador / Jugador) |
| ISS-005 | Día 115 | Botón "Aprobar Organizador" sin opción de "Rechazar" | Baja | ✅ Resuelto | Agregado botón de rechazar con endpoint separado (CR-005) |

---

## 3.7 Registro de Decisiones Técnicas

| Dec-ID | Fecha | Decisión | Justificación | Impacto |
|---|---|---|---|---|
| DEC-001 | Día 21 | Usar PDO Singleton para conexión a BD | Evita múltiples conexiones, centraliza configuración, facilita testing | [Conexion.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/Conexion.php) |
| DEC-002 | Día 21 | Base de datos en 3FN con herencia Class Table Inheritance | Elimina columnas NULL innecesarias, respeta polimorfismo de roles | [ascend.sql](file:///c:/xampp/htdocs/SGDM/base_de_datos/ascend.sql) |
| DEC-003 | Día 30 | Cero inline styles — CSS en archivos externos | Separación de concerns, mantenibilidad, requisito del equipo | Estándar de todo el proyecto |
| DEC-004 | Día 46 | Bcrypt (`PASSWORD_DEFAULT`) para hash de contraseñas | OWASP recommendation, resistente a ataques de fuerza bruta | [AuthControlador.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/controladores/AuthControlador.php) |
| DEC-005 | Día 65 | Regeneración de session ID tras login exitoso | OWASP Session Management — Previene session fixation | [Sesion.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/ayudantes/Sesion.php) |
| DEC-006 | Día 78 | Separar credenciales de BD en `base_de_datos.php` fuera del código | Principio de segregación de secretos (RNF16) | [base_de_datos.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/configuracion/base_de_datos.php) |
| DEC-007 | Día 95 | `Torneo.php` como modelo unificado para Organizador y Jugador | Evitar duplicación de código, mantener DRY | [Torneo.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/Torneo.php) |

---

## 3.8 Actas de Reuniones de Trabajo (Resumen)

| Reunión | Fecha | Duración | Tema Principal | Acuerdos |
|---|---|---|---|---|
| Kick-off | Día 1 | 2h | Inicio del proyecto, asignación de roles | Ver § 1.4 |
| Sprint 1 Planning | Día 46 | 1h | Planificación de BD + modelo Singleton | Completar `ascend.sql` en 2 semanas |
| Sprint 2 Review | Día 74 | 1.5h | Demo de Login/Registro funcional | Auth completa, pasar a Panel Admin |
| Sprint 3 Planning | Día 75 | 1h | División de módulos Admin / Organizador / Jugador | Juan: Admin; Comp. A: Org.; Comp. B: Jug. |
| Merge Meeting | Día 110 | 2h | Resolución de conflictos entre ramas | Merge exitoso, `feat/backend-mvc` como punto de encuentro |
| Sprint 6 Planning | Día 117 | 1h | Documentación para Segunda Entrega | Priorizar documentos faltantes |

---

## 3.9 Registro de Entregables

| ID | Entregable | Tipo | Archivo(s) | Estado |
|---|---|---|---|---|
| E-01 | Base de datos normalizada (3FN) | SQL | [ascend.sql](file:///c:/xampp/htdocs/SGDM/base_de_datos/ascend.sql) | ✅ Entregado |
| E-02 | Diagrama UML de Clases | Documentación | [UML_Imprimible.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/UML_Imprimible.md) | ✅ Entregado |
| E-03 | Requisitos Funcionales | Documentación | [RequisitosFuncionales.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/RequisitosFuncionales.md) | ✅ Entregado |
| E-04 | Requisitos No Funcionales | Documentación | [RequerimientosNoFuncionales.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/RequerimientosNoFuncionales.md) | ✅ Entregado |
| E-05 | Sistema de Autenticación | Software | `AuthControlador.php`, `Usuario.php`, vistas `auth/` | ✅ Entregado |
| E-06 | Panel de Administración | Software | `AdminControlador.php`, vistas `admin/` | ✅ Entregado |
| E-07 | Panel de Organizador | Software | `panelOrganizadorControlador.php`, vistas `organizador/` | ✅ Entregado |
| E-08 | Panel de Jugador | Software | `panelJugadorControlador.php`, vistas `jugador/` | ✅ Entregado |
| E-09 | Motor de Torneos | Software | [Torneo.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/Torneo.php) (50 KB) | ✅ Entregado |
| E-10 | DCL y Seguridad de BD | Documentación | [08_DCL_Seguridad_y_Permisos.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/08_DCL_Seguridad_y_Permisos.md) | ✅ Entregado |
| E-11 | Diccionario de Datos | Documentación | [03_Diccionario_de_Datos_Completo_y_Mapeo_SQL.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/03_Diccionario_de_Datos_Completo_y_Mapeo_SQL.md) | ✅ Entregado |
| E-12 | Estudio de Factibilidad | Documentación | [EstudioFactibilidad.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/EstudioFactibilidad.md) | ✅ Entregado |

---

# FASE 4 — MONITOREO Y CONTROL (Días 91 a 110)

---

## 4.1 Informe de Estado Actual (Día 120)

### Progreso Porcentual por Módulo

| Módulo | Progreso | Observación |
|---|---|---|
| Base de Datos (3FN) | 100% | SQL final, DCL documentado |
| Autenticación y RBAC | 100% | Login, Registro, Verificación Email, Sesión con timeboxing |
| Panel Administrador | 85% | CRUD usuarios, aprobación organizadores, juegos, política contraseñas. Falta: mod. torneos, bloqueo equipos, módulos |
| Panel Organizador | 90% | Crear/gestionar torneos, 3 motores de competencia. Falta: calendario integrado |
| Panel Jugador | 80% | Dashboard, equipos, invitaciones, guardados. Falta: inscripción a torneos |
| Consulta Pública | 30% | Vistas HTML estáticas sin integración PHP |
| Motor de Torneos | 95% | Liga, Eliminación Directa y Suizo funcionales. Falta: ranking por disciplina |
| Documentación | 70% | 66 archivos en `Documentos_Juan/`. Faltan: Casos de Uso UML, Plan Contingencias, Gantt, FODA, Actas |
| Infraestructura | 20% | Apache/XAMPP funcionando. Falta: Docker, scripts de respaldo, monitoreo |
| **TOTAL PONDERADO** | **~72%** | |

---

## 4.2 Registro de Cambios Consolidado

Ver § 3.2 — Se registraron 5 Change Requests, todos aprobados e implementados.

---

## 4.3 Registro de Riesgos Actualizado (Fase Control)

| ID | Riesgo | Estado al Día 120 | Acción Tomada |
|---|---|---|---|
| R-01 | Conflictos de merge en Git | ✅ Mitigado | Merge exitoso en Día 110 con resolución manual |
| R-02 | Abandono de integrante | 🟢 No materializado | Equipo completo |
| R-03 | Pérdida de datos BD | 🟢 No materializado | `ascend.sql` versionado en Git |
| R-04 | Retraso en módulo | ⚠️ Parcialmente materializado | Consulta pública retrasada — prioridad al motor de torneos |
| R-05 | Problemas de sesión tras merge | ✅ Mitigado | `Sesion.php` unificado exitosamente |
| R-07 | Fixtures incorrectos | ✅ Mitigado | Testing con múltiples tamaños de torneo |

---

## 4.4 Registro de Problemas Abiertos vs. Cerrados

| Estado | Cantidad | Issues |
|---|---|---|
| **Cerrados** | 5 | ISS-001 a ISS-005 |
| **Abiertos** | 2 | ISS-006: Consulta pública sin backend; ISS-007: Modelos `Auditoria.php` y `Rol.php` vacíos (89 y 85 bytes) |

---

## 4.5 Registro de Decisiones Técnicas Consolidadas

Ver § 3.7 — Se registraron 7 decisiones técnicas (DEC-001 a DEC-007), todas vigentes y sin reversiones.

---

## 4.6 Reporte de Variación del Cronograma y Costos

| Métrica | Planificado | Real | Variación |
|---|---|---|---|
| Horas invertidas al Día 120 | 360 h | 330 h | -8.3% (favorable) |
| Features completados | 74 RF | ~55 RF | -25.7% (desfavorable) |
| Documentos completados | 15 docs | 12 docs | -20% (parcialmente desfavorable) |
| SPI (Schedule Performance Index) | 1.00 | 0.83 | Leve retraso |

---

## 4.8 Matriz de Trazabilidad de Requisitos

### Trazabilidad: Requisito → Tabla SQL → Clase PHP → Vista

| RF | Requisito | Tabla(s) SQL | Modelo PHP | Controlador | Vista(s) |
|---|---|---|---|---|---|
| RF-001 | Registro de Jugador | `usuarios`, `perfiles_jugadores` | `Usuario.php` | `AuthControlador.php` | `auth/registro.php` |
| RF-002 | Solicitud Organizador | `usuarios`, `perfiles_organizadores` | `Usuario.php` | `AuthControlador.php` | `auth/registro.php` |
| RF-003 | Inicio de sesión | `usuarios`, `sesiones_activas` | `Usuario.php` | `AuthControlador.php` | `auth/login.php` |
| RF-007 | Autorización por rol | `roles` | `Sesion.php` | Todos los controladores | Guard en cada vista |
| RF-008 | Creación de equipos | `equipos`, `equipo_miembros` | `Equipo.php` | `EquipoControlador.php` | `jugador/crear-equipo.php` |
| RF-010 | Envío invitaciones | `solicitudes_equipo` | `Equipo.php` | `EquipoControlador.php` | `jugador/gestionar-equipo.php` |
| RF-017 | Creación de torneos | `torneos`, `torneo_config` | `Torneo.php` | `panelOrganizadorControlador.php` | `organizador/crear-torneo.php` |
| RF-019 | Selección disciplina | `juegos` | `Juego.php` | `JuegoControlador.php` | `organizador/crear-torneo.php` |
| RF-032 | Generación enfrentamientos | `torneo_encuentros` | `Torneo.php` | `panelOrganizadorControlador.php` | `organizador/gestionar-torneo.php` |
| RF-035 | Calendario Liga | `torneo_encuentros`, `torneo_posiciones` | `Torneo.php` | `panelOrganizadorControlador.php` | `organizador/gestionar-torneo.php` |
| RF-037 | Sorteo Eliminación Directa | `torneo_encuentros` | `Torneo.php` | `panelOrganizadorControlador.php` | `organizador/gestionar-torneo.php` |
| RF-039 | Sistema Suizo | `torneo_encuentros`, `torneo_suizo_parejas` | `Torneo.php` | `panelOrganizadorControlador.php` | `organizador/gestionar-torneo.php` |
| RF-042 | Registro resultados | `resultados_detalle`, `torneo_encuentros` | `Torneo.php` | `panelOrganizadorControlador.php` | `organizador/resultados.php` |
| RF-059 | Panel Admin | `usuarios` | `Usuario.php` | `AdminControlador.php` | `admin/dashboard.php` |
| RF-061 | Aprobación Organizador | `perfiles_organizadores` | `Usuario.php` | `AdminControlador.php` | `admin/organizadores/` |
| RF-074 | Registro de actividad | `auditoria_cambios`, `logs_actividad` | `Auditorias.php` | `AuditoriaControlador.php` | `admin/auditoria/` |

---

## 4.9 Checklist de Calidad

| # | Criterio | Cumple | Evidencia |
|---|---|---|---|
| 1 | Código PHP sin errores de sintaxis | ✅ | `error_reporting(E_ALL)` activado en desarrollo |
| 2 | Cero atributos `style=""` en HTML | ✅ | Estándar del equipo, CSS externo por módulo |
| 3 | Todas las queries SQL usan prepared statements (PDO) | ✅ | Revisión de todos los modelos |
| 4 | Contraseñas almacenadas con bcrypt | ✅ | `password_hash()` en `AuthControlador.php` |
| 5 | Regeneración de session ID tras login | ✅ | `session_regenerate_id(true)` en `Sesion.php` |
| 6 | Validación frontend + backend en formularios | ✅ | HTML5 `required` + PHP `filter_var()` / `empty()` |
| 7 | Credenciales de BD separadas del código | ✅ | `configuracion/base_de_datos.php` excluido de Git |
| 8 | Estructura MVC respetada (sin lógica en vistas) | ✅ | Controladores coordinan; vistas solo renderizan |
| 9 | Base de datos en 3FN | ✅ | Documentación + script SQL |
| 10 | Archivos README.md en directorios clave | ✅ | `controladores/README.md`, `modelos/README.md` |

---

## 4.10 Auditoría del Proyecto — Código y Estructura Relacional

### Hallazgos de la Auditoría de Código (Día 120)

| Hallazgo | Severidad | Archivo | Recomendación |
|---|---|---|---|
| `Auditoria.php` y `Rol.php` son archivos vacíos (< 90 bytes) | Media | `modelos/Auditoria.php`, `modelos/Rol.php` | Implementar o eliminar |
| Algunos archivos `.html` sin conexión PHP en `organizador/` | Baja | `organizador/crear-torneo.html`, etc. | Migrar a `.php` o eliminar duplicados |
| `Torneo.php` excede 50 KB (modelo "god object") | Media | `modelos/Torneo.php` | Considerar refactorizar en submódulos (TorneoLiga, TorneoED, TorneoSuizo) |
| Auditorías registradas vía clase helper separada (`Auditorias.php`) | Info | `ayudantes/Auditorias.php` | Consistente con el patrón elegido |

---

## 4.11 Informes de Cumplimiento Académico

### Cobertura por Asignatura (Segunda Entrega)

| Asignatura | Ítems Requeridos | Cumplidos | Cobertura |
|---|---|---|---|
| Programación Fullstack | 6 | 6 | **100%** ✅ |
| Ingeniería de Software | 6 | 4 | **67%** ⚠️ |
| Tutoría UTULAB | 5 | 2 | **40%** ❌ |
| Admin. de SO | 3 | 0 | **0%** ❌ |
| Ciberseguridad | 4 | 2 | **50%** ⚠️ |

---

## 4.12 Tablero de Control del Proyecto (KPIs)

| KPI | Valor | Meta | Estado |
|---|---|---|---|
| Requisitos Funcionales implementados | 55/74 | 74/74 | ⚠️ 74% |
| Requisitos No Funcionales cubiertos | 22/29 | 29/29 | ⚠️ 76% |
| Archivos de documentación | 66 | 80+ | 🔄 En progreso |
| Horas invertidas | 330 h | 480 h | 🟢 69% (dentro del presupuesto) |
| Bugs abiertos | 2 | 0 | ⚠️ |
| Cobertura de testing | Manual | Unitario (phpunit) | ❌ Pendiente para tercera entrega |
| Despliegue Docker | No | Sí | ❌ Pendiente para tercera entrega |

---

# FASE 5 — CIERRE DEL PROYECTO (Días 111 a 120 — Proyección de Cierre)

---

## 5.1 Documento de Lecciones Aprendidas

| # | Lección | Categoría | Impacto | Recomendación |
|---|---|---|---|---|
| 1 | Los conflictos de merge en Git son inevitables cuando múltiples desarrolladores trabajan en los mismos archivos (`Sesion.php`, `Torneo.php`) | DevOps | Alto | Establecer "propietarios de archivo" y hacer merges más frecuentes (mínimo semanal) |
| 2 | El motor de torneos fue significativamente subestimado en complejidad (3 algoritmos diferentes con edge cases) | Planificación | Alto | Agregar un 40% de buffer a estimaciones de algoritmos complejos |
| 3 | La documentación PMBOK consume tiempo pero ordena el pensamiento del equipo | Proceso | Medio | Iniciar la documentación desde el Día 1, no dejarla para el final |
| 4 | El manejo de sesiones PHP con RBAC debe diseñarse como un servicio centralizado desde el inicio | Arquitectura | Alto | `Sesion.php` como clase helper estática es un patrón exitoso |
| 5 | La herencia de tablas (Class Table Inheritance) en MySQL requiere más joins pero es más limpia que las columnas NULL | Base de Datos | Medio | Documentar el patrón para que todos los integrantes lo comprendan |
| 6 | Los estándares de código (cero inline styles, PSR-12) reducen drásticamente los conflictos de merge | Calidad | Alto | Definir y documentar los estándares antes de escribir la primera línea de código |

---

## 5.2 Lista de Pendientes (Punch List / Backlog Final)

### Para la Tercera Entrega (9 de noviembre de 2026)

| # | Pendiente | Asignatura | Prioridad | Esfuerzo |
|---|---|---|---|---|
| 1 | Módulo de consulta pública con integración PHP | Fullstack | Alta | 12 h |
| 2 | Docker Compose para despliegue | Admin SO | Alta | 8 h |
| 3 | Datos de prueba (mínimo 50 registros por tabla) | Fullstack | Alta | 4 h |
| 4 | Plan de testing + pruebas unitarias (PHPUnit) | Ing. Software | Alta | 12 h |
| 5 | Plan de seguridad OWASP formal | Ciberseguridad | Alta | 4 h |
| 6 | Certificado SSL en servidor | Ciberseguridad | Media | 2 h |
| 7 | Scripts de respaldo + cron automatizado | Admin SO | Alta | 6 h |
| 8 | Sistema de monitoreo (Zabbix/Grafana) | Admin SO | Media | 8 h |
| 9 | Manual de usuario completo | Tutoría / Ing. Software | Alta | 8 h |
| 10 | Manual de administrador | Ing. Software | Alta | 6 h |
| 11 | Documento ISO 25000 (calidad de software web) | Ing. Software | Media | 4 h |
| 12 | Encuesta de percepción de calidad | Ing. Software | Media | 2 h |
| 13 | Cálculo de métricas de punto de función | Ing. Software | Media | 4 h |
| 14 | Modelo 3D de producto para marketing | Tutoría UTULAB | Media | 4 h |
| 15 | Implementar `Auditoria.php` y `Rol.php` | Fullstack | Baja | 3 h |

---

## 5.3 Informe Final del Proyecto (Borrador)

### Resumen Ejecutivo de Logros

El proyecto SGDM ASCEND ha alcanzado un **72% de completitud general** al Día 120, con el núcleo funcional del sistema plenamente operativo:

- ✅ Sistema de autenticación y autorización con 3 roles funcionales.
- ✅ Base de datos relacional con 30+ tablas en 3FN estricta.
- ✅ Motor de torneos con 3 formatos de competencia (Liga, Eliminación Directa, Suizo).
- ✅ Panel de Administración con CRUD completo.
- ✅ Panel de Organizador con creación y gestión de torneos.
- ✅ Panel de Jugador con gestión de equipos e invitaciones.
- ✅ 66 documentos técnicos generados.

### Áreas Pendientes

- Módulo de consulta pública (integración PHP).
- Despliegue Docker.
- Scripts de respaldo y monitoreo.
- Testing automatizado.
- Documentación OWASP y ISO 25000.

---

## 5.4 Acta de Cierre del Proyecto (Draft)

> Este documento será finalizado tras la aprobación de la tercera entrega y la defensa del proyecto.

| Campo | Detalle |
|---|---|
| **Proyecto** | SGDM — ASCEND |
| **Fecha de cierre estimada** | 9 de noviembre de 2026 |
| **Alcance cumplido** | ~72% de RF implementados, 100% de la BD |
| **Presupuesto consumido** | 330 h de 480 h planificadas (69%) |
| **Calidad** | Código PSR-12, 3FN, OWASP parcial, cero inline styles |
| **Satisfacción del cliente (docentes)** | Pendiente de evaluación formal |

---

## 5.5 Liberación Formal de Recursos

| Recurso | Estado Post-Proyecto |
|---|---|
| Repositorio GitHub | Permanece activo (público o privado según decisión del equipo) |
| XAMPP en equipos personales | Puede ser desinstalado |
| Docker containers | Se destruyen tras la defensa |
| Canal Discord | Se archiva |

---

## 5.6 Evaluación del Desempeño del Equipo

| Integrante | Módulo(s) | Fortaleza | Área de Mejora |
|---|---|---|---|
| Juan (Líder) | Admin, BD, Arquitectura, Documentación | Diseño arquitectónico, documentación exhaustiva | Delegación de tareas |
| Compañero A | Organizador, Motor de Torneos | Lógica de negocio compleja | Resolución de conflictos Git |
| Compañero B | Jugador, Equipos | Interfaces de usuario, lógica de invitaciones | Documentación técnica |

---

## 5.7 Confirmación de Cumplimiento del Alcance

### Cotejo contra `Proyecto.md`

| Requisito de Proyecto.md | Estado |
|---|---|
| Arquitectura MVC | ✅ Cumplido |
| Sistema de autenticación y autorización (3 roles) | ✅ Cumplido |
| Al menos 3 módulos de competencia (Liga, ED, Suizo) | ✅ Cumplido |
| Generación automática de enfrentamientos | ✅ Cumplido |
| Tabla de posiciones y clasificaciones | ✅ Cumplido |
| Base de datos relacional con integridad | ✅ Cumplido |
| Interfaz pública responsive | ⚠️ HTML estático sin integración PHP |
| Panel de administración | ✅ Cumplido |
| Documentación técnica, funcional y de seguridad | ✅ Parcial — 66 documentos generados |
| Despliegue con Docker | ⏳ Pendiente (tercera entrega) |
| Controles de ciberseguridad OWASP | ✅ Parcial — bcrypt, PDO, sesiones seguras |

---

## 5.8 Checklist de Cierre Académico

| # | Ítem | Primera Entrega | Segunda Entrega | Tercera Entrega |
|---|---|---|---|---|
| 1 | Documentación de inicio y planificación | ✅ | — | — |
| 2 | Requisitos funcionales y no funcionales | ✅ | ✅ Actualizado | — |
| 3 | UML Diagrama de Clases | — | ✅ | — |
| 4 | UML Casos de Uso | — | ⚠️ Parcial | — |
| 5 | Modelo relacional normalizado + SQL | — | ✅ | — |
| 6 | DCL + Usuarios BD | — | ✅ | — |
| 7 | Integración PHP POO (gestión usuarios) | — | ✅ | — |
| 8 | Implementación Apache | — | ✅ | — |
| 9 | Estudio de factibilidad | — | ✅ | — |
| 10 | Plan de contingencias | — | ❌ | — |
| 11 | Diagrama de Gantt | ✅ (parcial) | ❌ | — |
| 12 | Docker + Datos de prueba | — | — | ⏳ |
| 13 | Testing (PHPUnit/Cypress) | — | — | ⏳ |
| 14 | OWASP + Certificado SSL | — | — | ⏳ |
| 15 | Manuales de usuario y administrador | — | — | ⏳ |

---

## 5.9 Documentación Entregada al Cliente / Tribunal

### Inventario de Documentos del Proyecto

| # | Documento | Ubicación | Formato |
|---|---|---|---|
| 1 | Propuesta del Proyecto | [Proyecto.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Proyecto.md) | Markdown + PDF |
| 2 | Requisitos Funcionales (RF-001 a RF-074) | [RequisitosFuncionales.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/RequisitosFuncionales.md) | Markdown + PDF |
| 3 | Requisitos No Funcionales (RNF01 a RNF29) | [RequerimientosNoFuncionales.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/RequerimientosNoFuncionales.md) | Markdown |
| 4 | Diagrama UML de Clases | [UML_Imprimible.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/UML_Imprimible.md) | Mermaid + PDF |
| 5 | Esquema SQL de la Base de Datos | [ascend.sql](file:///c:/xampp/htdocs/SGDM/base_de_datos/ascend.sql) | SQL |
| 6 | Diccionario de Datos (1FN, 2FN, 3FN) | [03_Diccionario_de_Datos_Completo_y_Mapeo_SQL.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/03_Diccionario_de_Datos_Completo_y_Mapeo_SQL.md) | Markdown + PDF |
| 7 | DCL — Seguridad y Permisos | [08_DCL_Seguridad_y_Permisos.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/08_DCL_Seguridad_y_Permisos.md) | Markdown |
| 8 | Estudio de Factibilidad | [EstudioFactibilidad.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/EstudioFactibilidad.md) | Markdown |
| 9 | Arquitectura Backend | [04_Hoja_de_Ruta_PHP_y_Arquitectura_Backend.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/04_Hoja_de_Ruta_PHP_y_Arquitectura_Backend.md) | Markdown + PDF |
| 10 | Arquitectura Frontend y Componentes | [01_Arquitectura_Frontend_y_Componentes.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/01_Arquitectura_Frontend_y_Componentes.md) | Markdown + PDF |
| 11 | Estándares de Arquitectura del Equipo | [Estandares_Arquitectura_Equipo.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Estandares_Arquitectura_Equipo.md) | Markdown + PDF |
| 12 | Matriz de Roles y Funcionalidades | [Matriz_Roles_y_Funcionalidades.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Matriz_Roles_y_Funcionalidades.md) | Markdown |
| 13 | Guía de Diseño DER/MER | [Guia_Diseno_DER_MER.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Guia_Diseno_DER_MER.md) | Markdown + PDF |
| 14 | Guía de Diseño UML Clases | [Guia_Diseno_UML_Clases.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Guia_Diseno_UML_Clases.md) | Markdown + PDF |
| 15 | Documentación BackEnd | [Documentacion_BackEnd.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Documentacion_BackEnd.md) | Markdown + PDF |
| 16 | Roadmap de Trabajo en Equipo | [06_Roadmap_Trabajo_en_Equipo_y_Division_Backend.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/06_Roadmap_Trabajo_en_Equipo_y_Division_Backend.md) | Markdown + PDF |
| 17 | Guía de Defensa para Presentación | [02_Guia_de_Defensa_para_Presentacion.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/02_Guia_de_Defensa_para_Presentacion.md) | Markdown + PDF |
| 18 | Comandos Git para el Equipo | [Git_Comandos.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/Git_Comandos.md) | Markdown |
| 19 | Esta Documentación (PMBOK) | DocumentacionProgramacion.md | Markdown |

---

## 5.10 Formulario de Aprobación del Patrocinador / Docente

| Campo | Contenido |
|---|---|
| **Proyecto** | SGDM — ASCEND |
| **Entrega** | Segunda Entrega — 14 de septiembre de 2026 |
| **Equipo** | Grupo ASCEND — 3° BT TI |
| **Firma del Líder de Proyecto** | _________________________ |
| **Firma del Docente de Fullstack** | _________________________ |
| **Firma del Docente de Ingeniería de Software** | _________________________ |
| **Firma del Docente de Admin. de SO** | _________________________ |
| **Firma del Docente de Ciberseguridad** | _________________________ |
| **Firma del Docente de Tutoría UTULAB** | _________________________ |
| **Fecha** | ____/____/2026 |
| **Observaciones** | |

---

## 5.11 Archivo de Documentación Final — Inventario de Artefactos

### Estructura del Repositorio

```
SGDM/
├── .htaccess                          # Configuración Apache (Front Controller)
├── .gitignore                         # Archivos excluidos de Git
├── README.md                          # Readme principal del proyecto
├── test.php                           # Script de prueba de conexión
│
├── base_de_datos/
│   └── ascend.sql                     # Schema completo MySQL 3FN (40 KB)
│
├── codigo_fuente/
│   ├── configuracion/
│   │   ├── base_de_datos.php          # Credenciales PDO (excluido de Git)
│   │   ├── constantes.php             # Constantes globales del sistema
│   │   └── README.md
│   │
│   ├── modelos/                       # Capa de acceso a datos (M en MVC)
│   │   ├── Conexion.php               # Singleton PDO
│   │   ├── Usuario.php                # CRUD de usuarios (16 KB)
│   │   ├── Torneo.php                 # Motor de torneos (50 KB)
│   │   ├── Equipo.php                 # Gestión de equipos (23 KB)
│   │   ├── Juego.php                  # Catálogo de disciplinas
│   │   ├── Jugador.php                # Perfil del jugador
│   │   ├── Guardado.php               # Torneos guardados
│   │   ├── PoliticaContrasena.php     # Políticas dinámicas de contraseña
│   │   ├── Auditoria.php              # (Pendiente de implementación)
│   │   ├── Rol.php                    # (Pendiente de implementación)
│   │   └── README.md
│   │
│   ├── controladores/                 # Capa lógica (C en MVC)
│   │   ├── AuthControlador.php        # Login, Registro, Verificación Email
│   │   ├── AdminControlador.php       # Dashboard admin, aprobación organizadores
│   │   ├── UsuarioControlador.php     # CRUD usuarios desde admin
│   │   ├── panelOrganizadorControlador.php  # Panel completo del organizador
│   │   ├── panelJugadorControlador.php      # Panel del jugador
│   │   ├── EquipoControlador.php      # CRUD equipos, invitaciones
│   │   ├── JuegoControlador.php       # CRUD disciplinas
│   │   ├── TorneoAdminControlador.php # Gestión de torneos por admin
│   │   ├── OrganizadorControlador.php # Gestión de organizadores
│   │   ├── JugadorControlador.php     # Gestión de jugadores
│   │   ├── GuardadosControlador.php   # Torneos guardados
│   │   ├── PoliticaContrasenaControlador.php # Admin de políticas
│   │   ├── AuditoriaControlador.php   # Consulta de auditoría
│   │   ├── RolControlador.php         # (Pendiente)
│   │   └── README.md
│   │
│   ├── vistas/                        # Capa de presentación (V en MVC)
│   │   ├── auth/                      # Login, Registro, Verificación
│   │   ├── admin/                     # Panel de administración (14 subdirs)
│   │   ├── organizador/               # Panel de organizador (17 archivos)
│   │   ├── jugador/                   # Panel de jugador (8 archivos)
│   │   ├── publico/                   # Vistas públicas (9 archivos)
│   │   └── componentes/               # Componentes reutilizables
│   │
│   ├── ayudantes/                     # Servicios transversales
│   │   ├── Sesion.php                 # RBAC y gestión de sesiones
│   │   ├── Auditorias.php             # Logger de auditoría
│   │   ├── Validador.php              # Validaciones de datos
│   │   └── README.md
│   │
│   ├── publico/                       # Assets estáticos (CSS, JS, imágenes)
│   └── logs/                          # Logs de la aplicación
│
├── Documentos_Juan/                   # Documentación del proyecto (66+ archivos)
│   ├── Proyecto.md                    # Letra del proyecto
│   ├── RequisitosFuncionales.md       # RF-001 a RF-074
│   ├── RequerimientosNoFuncionales.md # RNF01 a RNF29
│   ├── UML_Imprimible.md             # Diagrama de clases Mermaid
│   ├── EstudioFactibilidad.md        # Estudio de factibilidad
│   ├── DocumentacionProgramacion.md   # ESTE DOCUMENTO
│   └── ... (60+ documentos adicionales)
│
├── documentacion/                     # Documentación complementaria
│   ├── DocumentacionProgramacion.md
└── pruebas/                           # Scripts de testing
```

---

> **Fin del Documento — Versión 2.0 — Segunda Entrega**  
> **Generado:** 9 de septiembre de 2026  
> **Próxima actualización:** Tercera Entrega (9 de noviembre de 2026)
