# 🛡️ Políticas de Seguridad y Cumplimiento OWASP

Este documento detalla los mecanismos de seguridad implementados en el núcleo de la Plataforma ASCEND, diseñados para proteger la integridad de los datos, la privacidad de los usuarios y la estabilidad del servidor, siguiendo las directrices del **OWASP Top 10**.

---

## 1. Autenticación y Criptografía (A02:2021-Cryptographic Failures)

El sistema nunca almacena contraseñas en texto plano. 
- Se utiliza el algoritmo de hashing **Bcrypt** mediante la función nativa `password_hash()` de PHP, la cual incorpora un "salt" dinámico automáticamente.
- Cuando un usuario intenta iniciar sesión, se compara la contraseña provista contra el hash almacenado en la base de datos utilizando `password_verify()`.
- *Ver implementación:* `codigo_fuente/controladores/AuthControlador.php`.

Adicionalmente, se cuenta con una clase `PoliticaContrasena` que restringe el registro de usuarios, obligando a incluir mayúsculas, minúsculas, números y caracteres especiales, mitigando ataques de diccionario.

---

## 2. Prevención de Inyecciones SQL (A03:2021-Injection)

En toda la plataforma, el patrón de interacción con MySQL prohíbe terminantemente la concatenación de variables directamente en los strings de las consultas SQL.
- El sistema utiliza la extensión **PDO (PHP Data Objects)**.
- Todas las consultas se ejecutan mediante **Prepared Statements** (Sentencias Preparadas), asegurando que los datos introducidos por el usuario sean tratados estrictamente como "valores" y nunca como sentencias ejecutables.
- *Ver implementación:* Patrón Singleton en `codigo_fuente/modelos/Conexion.php`.

---

## 3. Control de Acceso (RBAC) (A01:2021-Broken Access Control)

ASCEND implementa un **Control de Acceso Basado en Roles (RBAC)** estricto:
- **Roles definidos:** Administrador, Organizador, Jugador.
- **Gestor de Sesiones:** El archivo `ayudantes/Sesion.php` contiene métodos estáticos (como `requerirRol()`) que se invocan al principio de cada Controlador (ej. `panelOrganizadorControlador.php`).
- Si un usuario intenta acceder a una URL a la cual no tiene permisos (Ej: un Jugador intentando ver el panel de Administrador), el sistema destruye su petición y lo redirige, bloqueando la navegación lateral y el escalado de privilegios.

---

## 4. Prevención de Robo de Sesiones (Session Fixation)

Para mitigar los ataques de fijación de sesión y robo de Cookies, al momento en que un usuario inicia sesión correctamente, el sistema ejecuta la regeneración del ID de sesión (`session_regenerate_id(true)`). 
Esto asegura que un atacante no pueda inyectar un identificador de sesión predecible para secuestrar la cuenta de un administrador.

---

## 5. Auditoría y Trazabilidad (A09:2021-Security Logging and Monitoring Failures)

La plataforma cuenta con un sistema híbrido de logs:
1. **Auditoría en Base de Datos:** La tabla `auditoria_cambios` registra modificaciones críticas (ej: creación de torneos, baneos de usuarios), documentando qué usuario hizo el cambio y en qué fecha.
2. **Auditoría de SO (Scripts Bash):** A nivel de servidor (Linux), se implementarán scripts que monitorearán los logs de Apache y la integridad (hashing SHA-256) de los archivos PHP, alertando si el código de ASCEND fue vulnerado.
