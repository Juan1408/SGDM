# 📋 Observaciones y Guía de Ajustes - Módulo Organizador

**Destinatario:** Programador del Módulo Organizador  
**Revisado por:** Arquitecto de Software & Administrador del Sistema  
**Fecha:** Septiembre 2026  
**Proyecto:** SGDM - Sistema de Gestión de Torneos y Multidisciplinas (ASCEND)

---

## 🎯 Objetivo del Documento
Este documento detalla los ajustes técnicos, mejoras de arquitectura e integración pendientes en el **Módulo Organizador** para garantizar la alineación con el patrón MVC, el estándar del proyecto y la defensa de la **Segunda Entrega**.

---

## 1. 🗄️ Base de Datos y Tipos de Datos (Puntuación)

### 1.1 Soporte de Puntuaciones Decimales (`DECIMAL(10,2)`)
* **Estado:** Aprobado en esquema relacional.
* **Justificación:** El uso de `DECIMAL(10,2)` para los campos de resultado y posiciones (`resultado_local`, `resultado_visitante`, `puntos`) permite soportar puntuaciones fraccionadas (ejemplo: `0.5` en ajedrez o empates con decimales) directamente en MySQL sin requerir multiplicaciones/divisiones mágicas en PHP.
* **Acción requerida:** Asegurar que las consultas de ordenamiento (`ORDER BY pos.puntos DESC`) y cálculo de tablas usen las funciones nativas de agregación de SQL (`SUM`, `AVG`).

---

## 2. 🔐 Seguridad, Auditoría y Sesiones

### 2.1 Registro de Auditoría Global (`Auditoria::registrar`)
* **Observación:** El controlador `panelOrganizadorControlador.php` ejecuta acciones de alto impacto sin registrar eventos en la tabla de auditoría global.
* **Acciones requeridas:**
  1. En `cambiarEstado()`: Invocar `Auditoria::registrar('CAMBIO_ESTADO_TORNEO', "Torneo ID $torneoId cambió a $estado");`.
  2. En `responderInscripcion()`: Invocar `Auditoria::registrar('INSCRIPCION_RESPONDIDA', "Inscripción $inscripcionId fue $respuesta");`.
  3. En `guardarResultado()`: Invocar `Auditoria::registrar('RESULTADO_GUARDADO', "Encuentro $encuentroId cargado con $local - $visitante");`.

---

## 3. 🌐 Enrutado y Constantes del Sistema

### 3.1 Uso de `URL_BASE` en Redirecciones y Plantillas
* **Observación:** Se detectaron rutas absolutas locales harcodeadas (ej. `/ejerciciosUTU/...`) en vistas y parciales ([navbar.html](file:///c:/xampp/htdocs/SGDM/codigo_fuente/vistas/organizador/navbar.html), [sidebar.html](file:///c:/xampp/htdocs/SGDM/codigo_fuente/vistas/organizador/sidebar.html)).
* **Acciones requeridas:**
  * Reemplazar todas las rutas absolutas locales por la constante global `URL_BASE` definida en [constantes.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/configuracion/constantes.php).
  * Ejemplo en PHP: `header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');`.

---

## 4. 🧩 Nombres de Métodos y Compatibilidad en Modelos

### 4.1 Estandarización de Métodos en [Torneo.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/Torneo.php)
* **Observación:** Se identificó una discrepancia entre `obtenerUltimosTorneos()` y `obtenerUltimosTorneosCreados()`.
* **Acciones requeridas:**
  * Mantener los nombres de métodos con una nomenclatura descriptiva y unificada.
  * Documentar en los comentarios de cabecera PHPDoc los parámetros recibidos y los tipos de retorno (`array`, `bool`, `int`).

---

## 5. 🛠️ Robustez en Generación de Fixtures

### 5.1 Manejo de Excepciones en Algoritmos de Fixtures
* **Observación:** En el sistema Suizo y Eliminación Directa, cuando no hay suficientes participantes o la cantidad no es potencia de 2, el controlador captura la excepción y muestra el mensaje en `$_SESSION['mensaje']`.
* **Acciones requeridas:**
  * Asegurar que la vista [gestionar-torneo.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/vistas/organizador/gestionar-torneo.php) muestre una alerta accesible (vía clase CSS `.alerta-error` o `.estado-fallido`) para que el usuario sepa exactamente por qué no se pudo generar el fixture.

---

## 6. 🛡️ Verificación de Cuenta de Organizador antes de Crear Torneos

### 6.1 Control de Aprobación por Administrador (`verificado_oficial`)
* **Observación:** El Administrador General ha implementado la funcionalidad de **Aprobación / Habilitación Oficial de Organizadores** desde el panel Admin (`index.php?c=organizador&a=index`), la cual gestiona la columna `verificado_oficial` en `perfiles_organizadores`.
* **Acciones requeridas en el Módulo Organizador:**
  1. En `panelOrganizadorControlador.php`, dentro del método `crear()` y `guardarEtapa()`, consultar la propiedad `verificado_oficial` del perfil del organizador autenticado.
  2. Si `verificado_oficial == 0` (el Administrador aún no aprobó la cuenta), **bloquear el acceso a la creación de torneos** y redirigir al dashboard con el mensaje:
     > `"Tu cuenta de Organizador aún se encuentra pendiente de aprobación por el Administrador General. No puedes crear torneos hasta ser verificado."`

---

## 📊 Resumen de Tareas Pendientes para el Desarrollador del Organizador

| # | Tarea | Componente | Prioridad |
|---|---|---|---|
| 1 | Validar `verificado_oficial == 1` antes de permitir crear torneos | `panelOrganizadorControlador.php` | Crítica |
| 2 | Agregar llamadas a `Auditoria::registrar()` en operaciones clave | `panelOrganizadorControlador.php` | Alta |
| 3 | Limpiar cualquier ruta hardcodeada local por `URL_BASE` | Vistas / Parciales JS | Alta |
| 4 | Agregar PHPDoc a métodos nuevos del modelo | `Torneo.php` | Media |
| 5 | Validar renderizado de mensajes de error de fixture en la vista | `gestionar-torneo.php` | Media |
