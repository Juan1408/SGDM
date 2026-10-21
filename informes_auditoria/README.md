# Informes de auditoría — SGDM / ASCEND

**Fecha:** 2026-10-21 · **Rama auditada:** `merge_16_10` (`902a2fe`)
**Estado:** auditoría de solo lectura finalizada. **No se modificó código del proyecto.**

## Documentos

| Documento | Contenido |
|-----------|-----------|
| [INFORME_AUDITORIA_INTEGRAL.md](INFORME_AUDITORIA_INTEGRAL.md) | Veredicto, método, arquitectura, hallazgos P0-P3, evidencia, discrepancias y límites |
| [MATRIZ_RUTAS_Y_FUNCIONALIDADES.md](MATRIZ_RUTAS_Y_FUNCIONALIDADES.md) | 98 rutas de la whitelist, roles, métodos, estado verificado y flujos de negocio |
| [AUDITORIA_BASE_DATOS_Y_DOCKER.md](AUDITORIA_BASE_DATOS_Y_DOCKER.md) | Esquema, semillas, columnas faltantes, migraciones y despliegue |
| [PLAN_ESTABILIZACION_5_DIAS.md](PLAN_ESTABILIZACION_5_DIAS.md) | Plan por día, tareas, responsables, dependencias, criterios y fallback |
| [CHECKLIST_ENTREGA_FINAL.md](CHECKLIST_ENTREGA_FINAL.md) | Checklist de aceptación, demo y firma |

## Veredicto en una línea

La aplicación tiene base funcional real, pero **no está apta para entrega** hasta corregir 4 bloqueantes P0 reproducidos: instalación destructiva en la guía, detalle de torneo en blanco, perfiles de organizador en blanco y edición de juegos inutilizada.

## Evidencia utilizada

- Importación real de `001`→`004` en MySQL 8 descartable: 41 tablas, 51 usuarios, 12 torneos.
- Servidor PHP embebido contra esa base, con sesiones reales de admin, organizador y jugador.
- Errores tomados de `errores_sistema` (SQLSTATE 42S22 y parse error).
- `php -l` sobre 104 archivos; `node --check` sobre 48 JS; PHPUnit 6/7.
- `docker compose config` e inspección de la imagen `docker-web`.

## Límites

- No se inició el MySQL/Apache de XAMPP para no tocar datos del estudiante.
- No se ejecutó el paso destructivo de la guía.
- No se probó MariaDB 10.4 ni un upload real de imágenes.
- Los POST no ejecutados se marcan como pendientes en la matriz; no se presentan como funcionando.

## Próximo paso

Esperar autorización para iniciar las correcciones del Día 1 del plan. Hasta entonces, no se modifica código.
