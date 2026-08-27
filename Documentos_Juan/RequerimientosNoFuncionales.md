Requerimientos No Funcionales de ASCEND
1. Requerimientos del producto
Usabilidad
RNF01 - Diseño responsive: La interfaz de ASCEND deberá adaptarse correctamente a dispositivos
móviles, tabletas y equipos de escritorio, manteniendo accesibles y legibles las funcionalidades
disponibles en cada tamaño de pantalla.
RNF02 - Filosofía Mobile First: La interfaz deberá diseñarse inicialmente para dispositivos móviles y
adaptarse progresivamente a resoluciones superiores, manteniendo la funcionalidad y organización del
contenido.
RNF03 - Consistencia de interfaz: Las diferentes secciones de ASCEND deberán mantener criterios
visuales y de navegación consistentes, utilizando componentes, estilos, controles y comportamientos
similares para acciones equivalentes.
RNF04 - Claridad de interacción: Los formularios, mensajes, botones y opciones deberán utilizar textos
comprensibles que permitan al usuario identificar la acción que realiza y comprender los errores
producidos.
RNF05 - Accesibilidad visual: La interfaz deberá mantener contraste suficiente entre textos, fondos y
elementos interactivos, así como tamaños de texto que permitan una lectura adecuada.
Rendimiento y eficiencia
RNF06 - Optimización de recursos: La aplicación deberá evitar la carga innecesaria de archivos, consultas
o recursos que no sean requeridos por la página solicitada, procurando un uso eficiente del servidor y del
navegador.
RNF07 - Gestión de base de datos: Las consultas frecuentes sobre usuarios, equipos, torneos, resultados y
rankings deberán utilizar estructuras e índices adecuados para evitar tiempos de búsqueda innecesarios a
medida que aumente la información almacenada.
Fiabilidad e integridad
RNF08 - Integridad de datos: La base de datos deberá mantener la consistencia de la información
mediante claves primarias, claves foráneas, restricciones y relaciones que eviten registros inválidos o
referencias inexistentes.
RNF09 - Validación de datos: Los datos ingresados por los usuarios deberán ser validados tanto en el
frontend como en el backend antes de ser procesados o almacenados, sin depender exclusivamente de las
validaciones realizadas en el navegador.
RNF10 - Manejo de errores: La aplicación deberá gestionar los errores sin exponer información técnica
sensible al usuario, mostrando mensajes comprensibles y evitando que un error aislado deje al sistema en
un estado inconsistente.
RNF11 - Persistencia de información: Los datos confirmados de usuarios, equipos, torneos, inscripciones,
encuentros y resultados deberán mantenerse almacenados de forma persistente aunque el usuario cierre la
sesión o se reinicie la aplicación.
Seguridad
RNF12 - Protección de contraseñas: Las contraseñas deberán almacenarse mediante un algoritmo de hash
seguro y no en texto plano, utilizando mecanismos adecuados para la protección de credenciales.

RNF13 - Autorización: Todas las solicitudes realizadas a funcionalidades restringidas deberán validar los
permisos del usuario en el servidor, evitando que la modificación de una URL o una petición desde el
cliente permita acceder a funciones pertenecientes a otro rol.
RNF14 - Protección de entradas: Los datos recibidos por la aplicación deberán ser tratados y validados
para reducir riesgos de ataques como inyección SQL, Cross-Site Scripting (XSS) y otras vulnerabilidades
contempladas por OWASP.
RNF15 - Protección de sesiones: Las sesiones autenticadas deberán identificar de forma segura al usuario
y evitar que los identificadores de sesión puedan utilizarse como mecanismo de acceso no autorizado.
RNF16 - Protección de información sensible: Las credenciales de base de datos, claves, secretos y demás
información sensible de configuración deberán mantenerse separadas del código fuente público y
configurarse mediante mecanismos apropiados para cada entorno.
RNF17 - Trazabilidad: Los registros de actividad y auditoría deberán mantener información suficiente
para identificar las operaciones relevantes, su fecha y el usuario responsable, sin permitir que usuarios sin
autorización alteren dichos registros.
Portabilidad y compatibilidad
RNF18 - Compatibilidad web: ASCEND deberá funcionar correctamente en versiones actuales de los
principales navegadores web compatibles con los estándares utilizados por HTML5, CSS3 y JavaScript.
RNF19 - Portabilidad mediante contenedores: La aplicación deberá poder desplegarse mediante Docker
sin depender de una configuración específica del equipo en el que fue desarrollada.
2. Requerimientos organizacionales
RNF20 - Arquitectura MVC: El código de ASCEND deberá mantener una arquitectura basada en el
patrón Modelo-Vista-Controlador, separando la lógica de negocio, la presentación y el acceso a los datos.
RNF21 - Tecnologías de implementación: El sistema deberá desarrollarse utilizando PHP para el backend,
HTML, CSS y JavaScript para el frontend y MySQL como sistema gestor de base de datos, de acuerdo
con las tecnologías establecidas para el proyecto.
RNF22 - Organización y modularidad: El código fuente deberá mantener una estructura organizada y
modular, reutilizando componentes comunes y evitando duplicación innecesaria de código.
RNF23 - Base de datos relacional: La información persistente deberá almacenarse en una base de datos
relacional normalizada, manteniendo relaciones y restricciones adecuadas entre sus entidades.
RNF24 - Estandarización de documentación: La documentación del proyecto deberá respetar los
estándares de presentación, nomenclatura y documentación establecidos por el Instituto Tecnológico
Superior Arias-Balparda para las entregas del proyecto.
3. Requerimientos externos
RNF25 - Privacidad de datos: La información personal almacenada por ASCEND deberá ser utilizada
únicamente para las finalidades propias de la plataforma y no deberá exponerse públicamente salvo
aquellos datos definidos como parte de los perfiles públicos.
RNF26 - Comunicación segura: En un entorno de producción, las comunicaciones entre el navegador y el
servidor deberán realizarse mediante HTTPS utilizando un certificado digital válido.
RNF27 - Respaldo de información: La información persistente deberá formar parte de una política de
respaldos que permita reducir el riesgo de pérdida de datos y posibilitar su recuperación ante fallos.
RNF28 - Cumplimiento de seguridad: El desarrollo deberá considerar las buenas prácticas de seguridad
web establecidas en OWASP, especialmente para autenticación, autorización, validación de entradas,
sesiones y protección de datos.

RNF29 - Protección de datos personales: El tratamiento de información personal deberá ajustarse a la
normativa aplicable sobre protección de datos personales durante la operación del sistema.