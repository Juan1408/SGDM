#!/usr/bin/env bash
# ============================================================================
# E2E: aceptación de Términos y Condiciones y Política de Privacidad
# ============================================================================
# Cubre, sobre la base de pruebas aislada:
#   - El registro muestra el checkbox obligatorio y los dos enlaces legales.
#   - Las páginas legales responden 200 y muestran la versión vigente.
#   - Un POST sin aceptación no crea usuario ni constancias.
#   - Un POST con aceptación crea la cuenta y exactamente dos filas.
#   - Un segundo POST con el mismo correo no duplica la aceptación.
#   - La ficha administrativa muestra la constancia en modo lectura.
#
# Uso (XAMPP, Git Bash):
#   BASE_URL=http://127.0.0.1:8098 \
#   MYSQL_BIN=/c/xampp/mysql/bin/mysql.exe \
#   PHP_BIN=/c/xampp/php/php.exe \
#   bash tests/e2e/registro_aceptacion_e2e.sh
#
# Uso contra el MySQL de Docker:
#   El cliente mysql de XAMPP (MariaDB) no negocia caching_sha2_password con
#   MySQL 8. Usá un wrapper que ejecute el cliente dentro del contenedor:
#     printf '#!/usr/bin/env bash\nexec docker exec -i sgdm_db mysql "$@"\n' \
#       > /tmp/e2e-docker-mysql && chmod +x /tmp/e2e-docker-mysql
#   BASE_URL=http://127.0.0.1:8082 DB_NAME=ascend_test \
#   MYSQL_BIN=/tmp/e2e-docker-mysql MYSQL_HOST= MYSQL_PORT= \
#   MYSQL_PASSWORD=root123 START_SERVER=0 \
#   bash tests/e2e/registro_aceptacion_e2e.sh
#
# Requisitos: curl, mysql, php y la base ascend_test migrada (001..011).
# El script crea y elimina sus propios usuarios (prefijo único por ejecución).
# ============================================================================
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
BASE_URL="${BASE_URL:-http://127.0.0.1:8098}"
DB_NAME="${DB_NAME:-ascend_test}"
MYSQL_BIN="${MYSQL_BIN:-mysql}"
MYSQL_USER="${MYSQL_USER:-root}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-}"
MYSQL_HOST="${MYSQL_HOST:-}"
MYSQL_PORT="${MYSQL_PORT:-}"
PHP_BIN="${PHP_BIN:-php}"
PORT="${PORT:-8098}"
START_SERVER="${START_SERVER:-1}"

MYSQL_ARGS=(-u "$MYSQL_USER")
[[ -n "$MYSQL_PASSWORD" ]] && MYSQL_ARGS+=("-p$MYSQL_PASSWORD")
[[ -n "$MYSQL_HOST" ]] && MYSQL_ARGS+=("-h" "$MYSQL_HOST")
[[ -n "$MYSQL_PORT" ]] && MYSQL_ARGS+=("-P" "$MYSQL_PORT")

PREFIX="e2e_acept_$(date +%s)_$$"
PASSWORD='Ascend2026!'
TMP="$(mktemp -d)"
SERVER_PID=""

cleanup() {
    if [[ -n "$SERVER_PID" ]]; then
        kill "$SERVER_PID" 2>/dev/null || true
    fi
    "$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" -e "
        DELETE FROM logs_actividad WHERE usuario_id IN (SELECT id FROM usuarios WHERE email LIKE '${PREFIX}%');
        DELETE FROM logs_acceso WHERE email_intentado LIKE '${PREFIX}%';
        DELETE FROM usuarios WHERE email LIKE '${PREFIX}%';
    " >/dev/null 2>&1 || true
    rm -rf "$TMP"
}
trap cleanup EXIT

fail() {
    echo "FALLO: $*" >&2
    exit 1
}

assert_contains() {
    local texto="$1" esperado="$2" mensaje="$3"
    [[ "$texto" == *"$esperado"* ]] || fail "$mensaje"
}

assert_status() {
    local obtenido="$1" esperado="$2" mensaje="$3"
    [[ "$obtenido" == "$esperado" ]] || fail "$mensaje (HTTP $obtenido)"
}

assert_regex() {
    local texto="$1" patron="$2" mensaje="$3"
    printf '%s' "$texto" | grep -Eq "$patron" || fail "$mensaje"
}

sql_val() {
    "$MYSQL_BIN" -N -B "${MYSQL_ARGS[@]}" "$DB_NAME" -e "$1"
}

csrf_from() {
    grep -o 'name="csrf_token" value="[^"]*"' | head -1 | sed 's/.*value="//;s/"$//'
}

login() {
    local jar="$1" email="$2" out="$3"
    local pagina token
    pagina=$(curl -fsS -c "$jar" "$BASE_URL/index.php?c=auth&a=mostrarLogin")
    token=$(printf '%s' "$pagina" | csrf_from)
    [[ -n "$token" ]] || fail "No se pudo extraer el CSRF del login."

    curl -sS -b "$jar" -c "$jar" -D "$TMP/login_headers.txt" -o "$out" -w '%{http_code}' \
        --data-urlencode "csrf_token=$token" \
        --data-urlencode "email=$email" \
        --data-urlencode "contrasena=$PASSWORD" \
        "$BASE_URL/index.php?c=auth&a=procesarLogin"
}

# ---------------------------------------------------------------------------
# Servidor PHP aislado sobre ascend_test
# ---------------------------------------------------------------------------
if curl -fsS "$BASE_URL/index.php?c=auth&a=mostrarLogin" >/dev/null 2>&1; then
    START_SERVER=0
fi

if [[ "$START_SERVER" == "1" ]]; then
    ( cd "$ROOT" && exec env DB_NAME="$DB_NAME" APP_URL="$BASE_URL/" \
        "$PHP_BIN" -S "127.0.0.1:$PORT" -t codigo_fuente/publico >"$TMP/server.log" 2>&1 ) &
    SERVER_PID=$!

    listo=0
    for _ in $(seq 1 50); do
        if curl -fsS "$BASE_URL/index.php?c=auth&a=mostrarLogin" >/dev/null 2>&1; then
            listo=1
            break
        fi
        sleep 0.2
    done
    [[ "$listo" == "1" ]] || fail "El servidor PHP no respondió. Log: $(cat "$TMP/server.log")"
fi

# ---------------------------------------------------------------------------
# 0. Páginas legales y formulario
# ---------------------------------------------------------------------------
echo "== 0. Formulario y documentos legales =="
TERMINOS_HTML=$(curl -fsS "$BASE_URL/index.php?c=inicio&a=terminos")
PRIVACIDAD_HTML=$(curl -fsS "$BASE_URL/index.php?c=inicio&a=privacidad")
assert_contains "$TERMINOS_HTML" "Versión 1.0" "Los términos no muestran la versión vigente."
assert_contains "$PRIVACIDAD_HTML" "Versión 1.0" "La privacidad no muestra la versión vigente."

REG_PAGINA=$(curl -fsS -c "$TMP/sin.cookies" "$BASE_URL/index.php?c=auth&a=mostrarRegistro")
assert_contains "$REG_PAGINA" 'name="aceptar_terminos"' "El registro no incluye el checkbox de aceptación."
assert_regex "$REG_PAGINA" 'name="aceptar_terminos"[^>]*required' "El checkbox no está marcado como obligatorio."
assert_regex "$REG_PAGINA" 'href="[^"]*c=inicio&a=terminos"[^>]*target="_blank"' "El enlace a términos no abre en pestaña nueva."
assert_regex "$REG_PAGINA" 'href="[^"]*c=inicio&a=privacidad"[^>]*target="_blank"' "El enlace a privacidad no abre en pestaña nueva."

# ---------------------------------------------------------------------------
# 1. POST sin aceptación
# ---------------------------------------------------------------------------
echo "== 1. Registro sin aceptación =="
SIN_EMAIL="${PREFIX}_sin@ascend.test"
TOKEN=$(printf '%s' "$REG_PAGINA" | csrf_from)
[[ -n "$TOKEN" ]] || fail "No se pudo extraer el CSRF del registro."

STATUS=$(curl -sS -b "$TMP/sin.cookies" -c "$TMP/sin.cookies" -o "$TMP/sin.html" -w '%{http_code}' \
    --data-urlencode "csrf_token=$TOKEN" \
    --data-urlencode "nombre_completo=E2E Sin Aceptacion" \
    --data-urlencode "email=$SIN_EMAIL" \
    --data-urlencode "telefono=+59899000120" \
    --data-urlencode "contrasena=$PASSWORD" \
    --data-urlencode "contrasena_confirmar=$PASSWORD" \
    --data-urlencode "rol=jugador" \
    "$BASE_URL/index.php?c=auth&a=procesarRegistro")
assert_status "$STATUS" "200" "El POST sin aceptación no debió redirigir."
assert_contains "$(cat "$TMP/sin.html")" "Debés aceptar los Términos" "No se mostró el error de aceptación."
[[ "$(sql_val "SELECT COUNT(*) FROM usuarios WHERE email='${SIN_EMAIL}'")" == "0" ]] \
    || fail "Se creó un usuario sin aceptar los documentos."
[[ "$(sql_val "SELECT COUNT(*) FROM aceptaciones_legales a INNER JOIN usuarios u ON u.id=a.usuario_id WHERE u.email='${SIN_EMAIL}'")" == "0" ]] \
    || fail "Se registró una aceptación sin checkbox."

# ---------------------------------------------------------------------------
# 1b. POST con correo de formato inválido
# ---------------------------------------------------------------------------
echo "== 1b. Registro con correo inválido =="
INVALID_EMAIL="${PREFIX}_sin_arroba"
TOKEN=$(curl -fsS -c "$TMP/invalid.cookies" "$BASE_URL/index.php?c=auth&a=mostrarRegistro" | csrf_from)
[[ -n "$TOKEN" ]] || fail "No se pudo extraer el CSRF del registro inválido."

STATUS=$(curl -sS -b "$TMP/invalid.cookies" -c "$TMP/invalid.cookies" -o "$TMP/invalid.html" -w '%{http_code}' \
    --data-urlencode "csrf_token=$TOKEN" \
    --data-urlencode "nombre_completo=E2E Correo Invalido" \
    --data-urlencode "email=$INVALID_EMAIL" \
    --data-urlencode "telefono=+59899000122" \
    --data-urlencode "contrasena=$PASSWORD" \
    --data-urlencode "contrasena_confirmar=$PASSWORD" \
    --data-urlencode "rol=jugador" \
    --data-urlencode "aceptar_terminos=1" \
    "$BASE_URL/index.php?c=auth&a=procesarRegistro")
assert_status "$STATUS" "200" "El POST con correo inválido no debió redirigir."
assert_contains "$(cat "$TMP/invalid.html")" "correo electrónico válido" "No se rechazó el correo con formato inválido."
[[ "$(sql_val "SELECT COUNT(*) FROM usuarios WHERE email='${INVALID_EMAIL}'")" == "0" ]] \
    || fail "Se creó un usuario con un correo de formato inválido."
[[ "$(sql_val "SELECT COUNT(*) FROM aceptaciones_legales a INNER JOIN usuarios u ON u.id=a.usuario_id WHERE u.email='${INVALID_EMAIL}'")" == "0" ]] \
    || fail "Se registró una aceptación para un correo inválido."

# ---------------------------------------------------------------------------
# 2. POST con aceptación
# ---------------------------------------------------------------------------
echo "== 2. Registro con aceptación =="
REG_EMAIL="${PREFIX}_ok@ascend.test"
REG_PAGINA=$(curl -fsS -c "$TMP/ok.cookies" "$BASE_URL/index.php?c=auth&a=mostrarRegistro")
TOKEN=$(printf '%s' "$REG_PAGINA" | csrf_from)
[[ -n "$TOKEN" ]] || fail "No se pudo extraer el CSRF del segundo registro."

STATUS=$(curl -sS -b "$TMP/ok.cookies" -c "$TMP/ok.cookies" -o "$TMP/ok.html" -w '%{http_code}' \
    --data-urlencode "csrf_token=$TOKEN" \
    --data-urlencode "nombre_completo=E2E Con Aceptacion" \
    --data-urlencode "email=$REG_EMAIL" \
    --data-urlencode "telefono=+59899000121" \
    --data-urlencode "contrasena=$PASSWORD" \
    --data-urlencode "contrasena_confirmar=$PASSWORD" \
    --data-urlencode "rol=jugador" \
    --data-urlencode "aceptar_terminos=1" \
    "$BASE_URL/index.php?c=auth&a=procesarRegistro")
assert_status "$STATUS" "200" "El registro con aceptación no debió redirigir."
assert_contains "$(cat "$TMP/ok.html")" "verificar tu correo" "El registro no mostró el enlace de verificación."

REG_ID=$(sql_val "SELECT id FROM usuarios WHERE email='${REG_EMAIL}'")
[[ -n "$REG_ID" ]] || fail "No se creó el usuario con aceptación."
[[ "$(sql_val "SELECT COUNT(*) FROM aceptaciones_legales WHERE usuario_id=${REG_ID}")" == "2" ]] \
    || fail "No se registraron exactamente dos aceptaciones."
[[ "$(sql_val "SELECT GROUP_CONCAT(CONCAT(tipo_documento, ':', version) ORDER BY tipo_documento) FROM aceptaciones_legales WHERE usuario_id=${REG_ID}")" == "terminos:1.0,privacidad:1.0" ]] \
    || fail "Las aceptaciones no tienen el tipo y la versión esperados."
[[ "$(sql_val "SELECT COUNT(*) FROM aceptaciones_legales WHERE usuario_id=${REG_ID} AND aceptado_en IS NOT NULL AND ip_origen IS NOT NULL")" == "2" ]] \
    || fail "Las aceptaciones no registraron fecha e IP."
[[ "$(sql_val "SELECT email_verificado FROM usuarios WHERE id=${REG_ID}")" == "0" ]] \
    || fail "El registro no dejó el email pendiente de verificación."

# ---------------------------------------------------------------------------
# 3. Reenvío del mismo registro
# ---------------------------------------------------------------------------
echo "== 3. Reenvío y no duplicación =="
REG_PAGINA=$(curl -fsS -c "$TMP/dup.cookies" "$BASE_URL/index.php?c=auth&a=mostrarRegistro")
TOKEN=$(printf '%s' "$REG_PAGINA" | csrf_from)
curl -sS -b "$TMP/dup.cookies" -c "$TMP/dup.cookies" -o "$TMP/dup.html" \
    --data-urlencode "csrf_token=$TOKEN" \
    --data-urlencode "nombre_completo=E2E Con Aceptacion" \
    --data-urlencode "email=$REG_EMAIL" \
    --data-urlencode "contrasena=$PASSWORD" \
    --data-urlencode "contrasena_confirmar=$PASSWORD" \
    --data-urlencode "rol=jugador" \
    --data-urlencode "aceptar_terminos=1" \
    "$BASE_URL/index.php?c=auth&a=procesarRegistro"
assert_contains "$(cat "$TMP/dup.html")" "ya se encuentra registrado" "El reenvío no fue rechazado."
[[ "$(sql_val "SELECT COUNT(*) FROM usuarios WHERE email='${REG_EMAIL}'")" == "1" ]] \
    || fail "El reenvío duplicó el usuario."
[[ "$(sql_val "SELECT COUNT(*) FROM aceptaciones_legales WHERE usuario_id=${REG_ID}")" == "2" ]] \
    || fail "El reenvío duplicó las aceptaciones."

# ---------------------------------------------------------------------------
# 4. Ficha administrativa
# ---------------------------------------------------------------------------
echo "== 4. Ficha administrativa =="
HASH="$("$PHP_BIN" -r 'echo password_hash("Ascend2026!", PASSWORD_BCRYPT);')"
ADMIN_EMAIL="${PREFIX}_admin@ascend.test"
"$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" <<SQL
INSERT INTO usuarios (email, contrasena_hash, nombre_completo, rol_id, esta_activo, email_verificado)
VALUES ('${ADMIN_EMAIL}', '${HASH}', 'E2E Admin Aceptacion', 1, 1, 1);
SQL

STATUS=$(login "$TMP/admin.cookies" "$ADMIN_EMAIL" "$TMP/admin_login.html")
assert_status "$STATUS" "302" "El administrador no pudo iniciar sesión."

FICHA=$(curl -fsS -b "$TMP/admin.cookies" \
    "$BASE_URL/index.php?c=usuario&a=ver&id=${REG_ID}&origen=usuarios")
assert_contains "$FICHA" "Aceptación de documentos legales" "La ficha no muestra la sección de aceptación."
assert_contains "$FICHA" "Vigente" "La ficha no marca la aceptación vigente."
REG_IP=$(sql_val "SELECT ip_origen FROM aceptaciones_legales WHERE usuario_id=${REG_ID} AND tipo_documento='terminos'")
[[ -n "$REG_IP" ]] || fail "La aceptación no registró la IP de origen."
assert_contains "$FICHA" "IP ${REG_IP}" "La ficha no muestra la IP de la aceptación."
assert_contains "$FICHA" "v1.0" "La ficha no muestra la versión aceptada."
[[ "$FICHA" != *"Confirmar aceptación"* ]] || fail "La ficha ofrece una acción de aceptación que no corresponde."
[[ "$FICHA" != *"Revocar aceptación"* ]] || fail "La ficha ofrece revocar la aceptación."

echo "OK: aceptación legal verificada de punta a punta."
