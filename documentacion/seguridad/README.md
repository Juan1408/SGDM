# Documentación de seguridad ASCEND

Análisis OWASP, políticas, gestión de contraseñas y riesgos.

## 1. Arquitectura de Seguridad (Autenticación y Autorización)

El sistema implementa una seguridad basada en roles (RBAC) y autenticación robusta.

### Flujo de Inicio de Sesión
1.  **Autenticación**:
    *   El usuario envía credenciales (correo/teléfono y contraseña).
    *   El servidor verifica contra la base de datos.
2.  **Autorización (Sesiones)**:
    *   Se generan dos tokens JWT: `AccessToken` (corto plazo) y `RefreshToken` (largo plazo).
    *   **AccessToken**: Se usa para acceder a recursos protegidos. Expira en 15 minutos.
    *   **RefreshToken**: Se usa para renovar el acceso sin volver a loguearse. Expira en 7 días.
3.  **Protección de Vistas (Admin)**:
    *   Las vistas de administración (`vistas/admin/*.html`) están protegidas.
    *   El sistema verifica la validez de los tokens y el rol (`admin`) antes de mostrar contenido.

### Roles
*   **Administrador**: Acceso total al sistema.
*   **Participante**: Acceso solo a sus torneos.
*   **Organizador**: Acceso a sus complejos y torneos.

---

## 2. Principales Vulnerabilidades OWASP (2021) y Controles Implementados

| Categoría OWASP | Riesgo | Mitigación en ASCEND |
| :--- | :--- | :--- |
| **A01: Broken Access Control** | Fallos en la autorización que permiten acceso a datos ajenos. | **Tokens JWT con roles**: El servidor valida el rol en cada petición. **Protección por vista**: El frontend solo carga secciones si el rol es correcto. |
| **A02: Cryptographic Failures** | Exposición de datos sensibles (contraseñas, tokens). | **Hashing**: Contraseñas almacenadas con Argon2 (bcrypt). **HTTPS**: Comunicación cifrada (configurado en nginx). **Tokens JWT**: Firmados digitalmente. |
| **A03: Injection** | Inyección SQL u otras inyecciones de código. | **Mapeo Objeto-Relacional (ORM)**: Se utiliza Supabase/PostgREST que previene inyección SQL por defecto. **Validación de entrada**. |
| **A04: Insecure Design** | Fallos en el diseño arquitectónico. | **Diseño por roles**: Separación clara de responsabilidades. **Manejo de sesiones**: Sistema de refresh tokens. |
| **A05: Security Misconfiguration** | Configuración incorrecta del servidor o aplicaciones. | **Headers de seguridad**: CSP, X-Content-Type-Options, etc. (configurados en nginx). **RHEL/Rocky Linux**: Sistema endurecido. |
| **A06: Vulnerable and Outdated Components** | Uso de librerías desactualizadas. | **Dependencias gestionadas**: Con `package.json`, `composer.json`, `go.mod` y `requirements.txt`. **Análisis de vulnerabilidades**: (pendiente de implementar formalmente). |
| **A07: Identification and Authentication Failures** | Fallos en el proceso de login. | **Rate Limiting**: Prevención de ataques de fuerza bruta (configurado en nginx). **Manejo de sesiones** robusto. |
| **A08: Software and Data Integrity Failures** | Inyección de actualizaciones o datos maliciosos. | **Firmas digitales**: Validación de actualizaciones de software. **Integridad de datos** en base de datos. |
| **A09: Security Logging and Monitoring Failures** | Falta de logs para detectar ataques. | **Registros de auditoría**: Implementados en la base de datos (`audit_logs`). **Logs de errores** del servidor. |
| **A10: Server-Side Request Forgery (SSRF)** | Solicitudes no autorizadas a recursos internos. | **Validación estricta**: Todas las peticiones externas pasan por validación. **Aislamiento de red**. |

---

## 3. Política de Gestión de Contraseñas

### Requisitos para Contraseñas
*   **Longitud Mínima**: 8 caracteres.
*   **Complejidad**: Debe incluir una combinación de:
    *   Mayúsculas (A-Z)
    *   Minúsculas (a-z)
    *   Números (0-9)
    *   Símbolos (!@#$%^&*)
*   **Prohibido**: Contraseñas comunes (ej. "password", "123456"), información personal identificable o palabras de diccionario.
*   **Rotación**: Se recomienda cambiar la contraseña cada 90 días.

### Almacenamiento
Las contraseñas se almacenan utilizando el algoritmo de hash **Argon2** con una sal (salt) única para cada usuario, implementado a través de la librería `argon2`.

---

## 4. Recomendaciones de Seguridad

### Para Desarrolladores
1.  **Siempre usar consultas parametrizadas** para evitar inyección SQL (ya implementado con Supabase/PostgREST).
2.  **Validar todas las entradas de usuario** (frontend y backend).
3.  **No exponer información sensible** en logs o respuestas del servidor.
4.  **Implementar rate limiting** en endpoints críticos (ej. login).

### Para Administradores
1.  **Mantener el sistema actualizado**: Aplicar parches de seguridad y actualizar dependencias regularmente.
2.  **Monitorear logs de auditoría**: Revisar periódicamente los logs para detectar actividades sospechosas.
3.  **Configurar HTTPS**: Asegurar que el servidor esté configurado con TLS/SSL.
4.  **Gestión de claves**: Rotar claves de API y certificados regularmente.

---

## 5. Riesgos Actuales y Futuros

### Riesgos Actuales
*   **Dependencias desactualizadas**: Es necesario un proceso formal para actualizar librerías y frameworks.
*   **Auditoría continua**: Falta un monitoreo en tiempo real de posibles ataques.

### Riesgos Futuros
*   **Ataques de fuerza bruta**: Con el crecimiento del sistema, aumenta la necesidad de protección avanzada.
*   **Configuraciones de producción**: Asegurar que el entorno de producción esté hardening según las mejores prácticas.
