-- =============================================================================
-- 011_aceptaciones_legales.sql
-- =============================================================================
-- PROPÓSITO
--   Registrar la aceptación de los Términos y Condiciones y de la Política de
--   Privacidad en el momento del alta de la cuenta.
--
-- ALCANCE
--   Solo agrega una tabla nueva. No altera usuarios, perfiles, tokens ni
--   ninguna otra tabla existente. No modifica ni elimina datos.
--
--   Cada fila guarda:
--     - usuario_id        cuenta que aceptó.
--     - tipo_documento    'terminos' o 'privacidad'.
--     - version           versión vigente del documento al momento del alta.
--     - aceptado_en       fecha y hora del servidor.
--     - ip_origen         IP de la conexión (hasta 45 caracteres, soporta IPv6).
--     - user_agent        navegador declarado, truncado a 255 caracteres.
--
--   La clave única (usuario_id, tipo_documento, version) impide duplicados por
--   doble envío del formulario. El registro es inmutable: la aplicación no
--   expone operaciones de edición ni de borrado sobre esta tabla, y el
--   administrador solo la consulta en modo lectura desde la ficha del usuario.
--
-- ORDEN E IDEMPOTENCIA
--   Debe ejecutarse después de 001 (o sobre una base existente que ya tenga
--   usuarios). Es idempotente: CREATE TABLE IF NOT EXISTS permite aplicarla
--   varias veces sin error ni cambios adicionales.
--
--   Base existente en XAMPP (elegí la base destino; el archivo no hace USE):
--     mysql -uroot ascend      < base_de_datos/migraciones/011_aceptaciones_legales.sql
--     mysql -uroot ascend_test < base_de_datos/migraciones/011_aceptaciones_legales.sql
--
--   Docker (volumen ya inicializado):
--     docker exec -i sgdm_db mysql -uroot -p<clave> ascend \
--       < base_de_datos/migraciones/011_aceptaciones_legales.sql
--
--   En una instalación limpia, MySQL ejecuta 001..011 en orden y la tabla ya
--   viene incluida también en 001; esta migración no duplica nada.
-- =============================================================================

CREATE TABLE IF NOT EXISTS aceptaciones_legales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_documento ENUM('terminos', 'privacidad') NOT NULL,
    version VARCHAR(20) NOT NULL,
    aceptado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_origen VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    UNIQUE KEY uq_aceptacion (usuario_id, tipo_documento, version),
    CONSTRAINT fk_aceptacion_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
