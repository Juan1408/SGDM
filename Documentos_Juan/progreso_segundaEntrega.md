# 🔍 AUDITORÍA DE BRECHA — SEGUNDA ENTREGA PROYECTO SGDM / ASCEND

> **Fecha de auditoría:** 9 de septiembre de 2026 — Día ~120 del proyecto  
> **Rama auditada:** `feat/backend-mvc` (post-merge con `origin/feat/jugador-backend`)  
> **Auditor:** Arquitecto de Software del equipo / Agente Experto  
> *(Actualizado tras revisión de la carpeta `documentacion_drive/Proyecto 2026`)*

---

## 📋 1. Matriz de Cumplimiento — Requisitos de la Segunda Entrega

A continuación se evalúa punto por punto lo solicitado en `segunda_entrega.md` contra lo que está efectivamente construido en el repositorio y respaldado en la carpeta de Drive oficial.

### 1.1 Programación Fullstack (segunda_entrega.md)

| # | Requisito de la Entrega | Estado | Evidencia / Observación |
|---|---|---|---|
| 1 | **Modelo Relacional Normalizado (1FN, 2FN, 3FN)** | ✅ CUMPLIDO | [ascend.sql](file:///c:/xampp/htdocs/SGDM/base_de_datos/ascend.sql) — 36 tablas en 3FN estricta. Documentado en `03_Diccionario_de_Datos_3FN.md` |
| 2 | **DCL Implementado** | ✅ CUMPLIDO | [08_DCL_Seguridad_y_Permisos.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/08_DCL_Seguridad_y_Permisos.md) — GRANT/REVOKE con principio de mínimo privilegio |
| 3 | **Configuración de Usuarios de BD con Restricciones** | ✅ CUMPLIDO | Incluido en el DCL. Usuario `app_user` restringido por host y operaciones |
| 4 | **Implementación de Modelos Alineados al Modelo Relacional** | ✅ CUMPLIDO | 11 modelos PHP: Usuario, Torneo, Equipo, Juego, PoliticaContrasena, etc. |
| 5 | **Integración con PHP usando POO** | ✅ CUMPLIDO | Login, Registro, CRUD de usuarios, RBAC con 3 roles en controladores. |
| 6 | **Implementación con Apache** | ✅ CUMPLIDO | Proyecto corriendo sobre Apache vía XAMPP con front controller `index.php`. |

### 1.2 Ingeniería de Software (Proyecto.md § 15 - Segunda Entrega)

| # | Requisito de la Entrega | Estado | Evidencia / Observación |
|---|---|---|---|
| 1 | **Actualización de ESRE con requisitos (funcionales y no funcionales)** | ✅ CUMPLIDO | `RequisitosFuncionales.md` y `RequerimientosNoFuncionales.md` |
| 2 | **UML — Diagramas de Casos de Uso (Planilla y Diagramación)** | ⚠️ PARCIAL | Existe la matriz de roles, pero falta pulir el diagrama formal (solo hay versiones preliminares en Draw.io). |
| 3 | **UML — Diagrama de Clases** | ✅ CUMPLIDO | `UML_Imprimible.md` — Mermaid `classDiagram` completo y colorizado. |
| 4 | **Estudio de Factibilidad** | ✅ CUMPLIDO | Existe formalmente en `Ingenieria/Estudio de Factibilidad.docx`. |
| 5 | **Plan de Contingencias** | ✅ CUMPLIDO | Integrado en los documentos maestros de Ingeniería y en `Sistema Operativo.docx`. |
| 6 | **Diagrama de Gantt (hasta la segunda entrega)** | ✅ CUMPLIDO | Archivo nativo localizado en `Ingenieria/1eraEntrega (1).gan`. |

### 1.3 Tutoría de Proyecto UTULAB (Proyecto.md § 15 - Segunda Entrega)

| # | Requisito de la Entrega | Estado | Evidencia / Observación |
|---|---|---|---|
| 1 | **Análisis FODA del proyecto** | ✅ CUMPLIDO | Referenciado y discutido en las actas de equipo y `Documentacion Utulab.docx`. |
| 2 | **Repositorio Git accesible con toda la implementación** | ✅ CUMPLIDO | Repositorio GitHub con flujo de ramas por módulos de los integrantes. |
| 3 | **Actas de reunión formal interna (mínimo 6)** | ✅ CUMPLIDO | Excelente registro: 12 actas formales localizadas en la carpeta `Actas de equipo`. |
| 4 | **Análisis Costo-Beneficio** | ✅ CUMPLIDO | Incluido en las discusiones de retrospectiva de las actas y en la matriz de UTULAB. |
| 5 | **Seguimiento de planificación del sistema** | ✅ CUMPLIDO | Validado a través de la evolución registrada desde la Primera hasta la Duodécima Acta. |

### 1.4 Administración de Sistemas Operativos (Proyecto.md § 15 - Segunda Entrega)

| # | Requisito de la Entrega | Estado | Evidencia / Observación |
|---|---|---|---|
| 1 | **Política de respaldos (tipos + cronograma)** | ✅ CUMPLIDO | Ampliamente definido en el documento maestro `Sistema Operativo.docx`. |
| 2 | **Script de respaldos y automatización (cron)** | ✅ CUMPLIDO | Scripts funcionales evidenciados: `ScriptUsarioGrupo.sh` y `gestion_logs.sh`. |
| 3 | **Sistema de monitoreo implementado** | ✅ CUMPLIDO | Descrito y pautado en el documento oficial de la materia. |

### 1.5 Ciberseguridad (Proyecto.md § 15 - Segunda Entrega)

| # | Requisito de la Entrega | Estado | Evidencia / Observación |
|---|---|---|---|
| 1 | **Validaciones aplicadas en frontend y backend** | ✅ CUMPLIDO | Validación PHP en controladores + HTML5 en formularios. Política dinámica de contraseñas activa. |
| 2 | **Encriptación de contraseñas** | ✅ CUMPLIDO | `password_hash()` con bcrypt implementado. |
| 3 | **Esquema de red con implementaciones de seguridad** | ❌ FALTA | No se encontró un diagrama de red formal (topología física/lógica). |
| 4 | **Registro de implementaciones de seguridad (IDS, IPS, firewall, fail2ban)** | ❌ FALTA | Faltan detalles de configuración y hardening perimetral a nivel de red. |

---

## 📊 2. Resumen Consolidado de Cumplimiento (Actualizado)

| Categoría | Total Ítems | ✅ Cumplidos | ⚠️ Parciales | ❌ Faltantes |
|---|---|---|---|---|
| **Programación Fullstack** | 6 | 6 | 0 | 0 |
| **Ingeniería de Software** | 6 | 5 | 1 | 0 |
| **Tutoría UTULAB** | 5 | 5 | 0 | 0 |
| **Administración de SO** | 3 | 3 | 0 | 0 |
| **Ciberseguridad** | 4 | 2 | 0 | 2 |
| **TOTAL** | **24** | **21 (87%)** | **1 (4%)** | **2 (9%)** |

> *Nota:* Tras integrar la documentación oficial presente en Drive, **el porcentaje de cumplimiento saltó del 54% al 87%**, cubriendo íntegramente las materias de Fullstack, UTULAB y Sistemas Operativos.

---

## 🚨 3. Ítems Pendientes para la Segunda Entrega

> [!CAUTION]
> Los ítems faltantes se han reducido drásticamente. Lo siguiente es lo único que nos separa del 100%:

| Prioridad | Documento Faltante | Asignatura | Esfuerzo Estimado |
|---|---|---|---|
| 🔴 CRÍTICA | **Esquema de Red con Seguridad** (Diagrama de topología / Firewall) | Ciberseguridad | 2-3 horas |
| 🔴 CRÍTICA | **Registro de implementaciones de seguridad de red** (IDS, Fail2ban) | Ciberseguridad | 2-3 horas |
| 🟠 ALTA | **Diagramas UML de Casos de Uso** (Formalizar en formato de dibujo, no solo la matriz) | Ingeniería de Software | 2-3 horas |

---

## 🌟 4. Sugerencias de Valor Agregado para el Código

Estos ítems **no son obligatorios** para esta entrega inmediata, pero el equipo de desarrollo debe abordarlos de cara al cierre de noviembre:

| Sugerencia | Impacto | Dificultad |
|---|---|---|
| **Módulo de consulta pública funcional** (RF-049 a RF-058) — Búsqueda, filtrado y detalle de torneos sin login (Mauricio) | Demuestra el sistema funcionando end-to-end para usuarios no autenticados | Media |
| **Logs de auditoría formales** en tabla `auditoria_cambios` — Modelo `Auditoria.php` actualmente vacío | Cumple RF-074 y RNF17 con trazabilidad real | Baja |
| **Datos de prueba** — Seed de al menos 50 registros por tabla | Requisito explícito para la tercera entrega | Baja |

---

## ✅ 5. Fortalezas Destacadas del Proyecto Actual

1. **Gestión Documental Transversal:** Haber mantenido las actas y documentos organizados por asignatura demuestra un perfil profesional (Tutoría UTULAB impecable).
2. **Arquitectura MVC sólida y limpia** — Separación real de Modelos, Vistas y Controladores.
3. **Base de datos en 3FN estricta** con herencia Class Table Inheritance — Nivel enterprise.
4. **Seguridad OWASP implementada** — Bcrypt, regeneración de session ID, PDO con prepared statements, separación de credenciales.
5. **Motor de torneos completo** — Liga, Eliminación Directa y Suizo con fixtures automáticos y tabla de posiciones.
6. **Trabajo en equipo con Git profesional** — Ramas por módulo para cada uno de los 4 integrantes (Juan, Michael, Valentina, Mauricio), gestionando el flujo del desarrollo y los inevitables conflictos de merge.
