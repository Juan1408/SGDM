# 📋 Tareas de Ejecución - Módulo Organizador

*Este módulo está destinado a los usuarios con Rol 2. Su objetivo principal es gestionar sus propios torneos, administrar las inscripciones y cargar resultados.*

- [ ] **0. 🚨 RECORDATORIO OBLIGATORIO: HACER `git pull` ANTES DE EMPEZAR A PROGRAMAR**

## 1. Dashboard del Organizador (`vistas/organizador/dashboard.php`)
- [ ] Maquetar la interfaz principal (cascarón) específica para el organizador.
- [ ] Mostrar métricas rápidas: Torneos activos, Solicitudes pendientes, Torneos finalizados.
- [ ] Menú lateral exclusivo para sus funciones.

## 2. Gestión de Torneos Propios
- [ ] Crear pantalla para listar **mis torneos** (solo los creados por este organizador).
- [ ] Formulario para **Crear Torneo**: 
  - Debe consumir el Catálogo de Juegos (solo los juegos que el Administrador dejó activos).
  - Seleccionar fechas, cupo máximo de equipos y formato (Liga, Suizo, Eliminación).
- [ ] Pantalla de configuración del torneo (Modificar reglas, cambiar estado a "Inscripciones Abiertas", "En Curso", "Finalizado").

## 3. Gestión de Inscripciones (Aprobaciones)
- [ ] Bandeja de entrada de solicitudes: Ver qué equipos/jugadores quieren entrar al torneo.
- [ ] Botones para **Aprobar** o **Rechazar** la participación de un equipo.
- [ ] Control de cupos (No permitir aprobar más equipos del límite establecido).

## 4. Carga de Resultados (Fase de Partidas)
- [ ] Interfaz para visualizar el fixture/llaves del torneo.
- [ ] Formulario para ingresar el resultado de cada partido (ej. Equipo A 2 - 1 Equipo B).
- [ ] Botón para confirmar y avanzar a la siguiente ronda (en caso de eliminación directa) o recalcular la tabla de posiciones (en caso de liga).

## 5. Reportes y Cierre
- [ ] Pantalla para declarar el torneo como Finalizado.
- [ ] Emitir podio automático (1ro, 2do, 3er lugar).
