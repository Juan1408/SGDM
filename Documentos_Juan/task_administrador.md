# 📋 Tareas de Ejecución - Módulo Administrador (Master Plan)

- [ ] **0. 🚨 RECORDATORIO: HACER `git pull` ANTES DE EMPEZAR A PROGRAMAR**

## 👥 1. Gestión de Usuarios
- [x] **1.2 Organizadores**: Listar, bloquear, ver como. (Eliminar postergado).
- [x] **1.3 Jugadores**: Listar, bloquear, ver como. (Eliminar postergado).
- [ ] **1.1 Administrativos**: 
  - [ ] CRUD para crear nuevos usuarios con rol 1 (Administradores, Moderadores, Soporte).
  - [ ] Implementar edición, bloqueo y eliminación (baja lógica).

## 🛡️ 2. Equipos
- [ ] **2.1 Directorio de equipos**:
  - [ ] Listar todos los equipos.
  - [ ] Filtro dinámico por disciplina (consultando la BD).
  - [ ] Filtro por estado (activos, bloqueados, eliminados).
- ~*[2.2 Aprobar solicitudes: ELIMINADO]*~

## 🎮 3. Juegos y Disciplinas
- [x] **3.1 Ver catálogo**: Listado y cambio de estado implementado.
- [x] **3.2 Registrar nuevo juego / Editar**: Implementado.

## 🏆 4. Administrar Torneos
- [ ] **4.1 Ver y Configurar**:
  - [ ] Editar información general de un torneo (auditoría / moderación de nombres inadecuados o estados).
- [ ] **4.2 Historial de Torneos**:
  - [ ] Listar todos los torneos del sistema.
  - [ ] Filtros por: disciplina, fecha, y estado (en curso, finalizados, discrepancias, cancelados).
  - [ ] Vista detallada del torneo (Solo lectura de equipos, resultados, fechas - Se conectará al final usando los modelos de los compañeros).
- ~*[Crear torneo oficial: ELIMINADO]*~

## 📊 5. Centro de Reportes (Tickets)
- [ ] **5.1 Gestión de Tickets**:
  - [ ] Crear tabla de BD para tickets (si no existe).
  - [ ] Listar reportes recibidos desde la app.
  - [ ] Filtro por tipo: Bug, Sugerencia, Reporte de Usuarios.
  - [ ] Cambiar estado de tickets (Pendiente, Resuelto).

## ⚙️ 6. Configuración del Sistema
- [ ] **6.1 Enviar Alertas Globales**:
  - [ ] Crear sistema de mensajería broadcast (Alertas que verán los usuarios al iniciar sesión).
- [ ] **6.2 Módulos (Feature Flags - REQUISITO CLAVE)**:
  - [ ] Crear tabla `modulos_sistema` para controlar flags (Suizo, Eliminación, Fases, etc.).
  - [ ] Interfaz para activar/desactivar módulos en tiempo real.
  - [ ] Helper global para bloquear la vista/rutas en el sistema si un módulo está apagado.
- [x] **6.3 Logs de Auditoría**:
  - [x] Crear tabla `logs_auditoria` (o `logs_actividad`).
  - [x] Helper para registrar acciones.
  - [x] Pantalla para ver y filtrar logs.
- [ ] **6.4 Errores en el Sistema (Error Tracking)**:
  - [ ] Implementar un manejador global de excepciones (`set_exception_handler`) que guarde el error, usuario y stack trace en la BD.
  - [ ] Pantalla de monitoreo de errores (Ver, Marcar como resuelto, Eliminar, Limpiar todos).

## 🧠 7. Lógica Core de Torneos (Algoritmos Backend)
*Aunque los organizadores crean los torneos, tú como arquitecto backend programarás los motores matemáticos que el sistema usa.*
- [ ] **7.1 Motor de Eliminación Directa**: Algoritmo para generar llaves (brackets) aleatorias y avanzar ganadores.
- [ ] **7.2 Motor de Sistema Suizo**: Algoritmo para emparejar por puntuación evitando cruces repetidos.
- [ ] **7.3 Motor de Ligas/Fases**: Algoritmo Round-Robin (todos contra todos).
