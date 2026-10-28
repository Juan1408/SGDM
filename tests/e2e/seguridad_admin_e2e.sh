#!/usr/bin/env bash
# ============================================================================
# E2E: seguridad y deuda técnica del rol administrador
# ============================================================================
# Cubre, sobre una base de datos de pruebas aislada:
#   - Login bloqueado sin email verificado y flujo de verificación.
#   - Email del jugador en solo lectura.
#   - Baja lógica y auditoría de bloquear/activar/dar de baja.
#   - Suplantación: bloqueo de auto-suplantación y de otros admins,
#     restauración por POST con CSRF y Referer no confiable.
#   - Cambio de rol con sincronización de perfil y confirmación de admin.
#
# Uso (XAMPP, Git Bash):
#   BASE_URL=http://127.0.0.1:8099 \
#   MYSQL_BIN=/c/xampp/mysql/bin/mysql.exe \
#   PHP_BIN=/c/xampp/php/php.exe \
#   bash tests/e2e/seguridad_admin_e2e.sh
#
# Uso contra el MySQL de Docker (servidor web aparte o el de docker compose):
#   BASE_URL=http://127.0.0.1:8082 DB_NAME=ascend_test \
#   MYSQL_BIN=/c/xampp/mysql/bin/mysql.exe \
#   MYSQL_HOST=127.0.0.1 MYSQL_PORT=3307 MYSQL_PASSWORD=root123 \
#   START_SERVER=0 bash tests/e2e/seguridad_admin_e2e.sh
#
# Requisitos: curl, mysql, php y la base ascend_test migrada.
# El script crea y elimina sus propios usuarios (prefijo único por ejecución).
# ============================================================================
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
BASE_URL="${BASE_URL:-http://127.0.0.1:8099}"
DB_NAME="${DB_NAME:-ascend_test}"
MYSQL_BIN="${MYSQL_BIN:-mysql}"
MYSQL_USER="${MYSQL_USER:-root}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-}"
MYSQL_HOST="${MYSQL_HOST:-}"
MYSQL_PORT="${MYSQL_PORT:-}"
PHP_BIN="${PHP_BIN:-php}"
PORT="${PORT:-8099}"
START_SERVER="${START_SERVER:-1}"

# Argumentos comunes del cliente MySQL. Permiten apuntar al MySQL de Docker
# (-h 127.0.0.1 -P 3307 -p...) sin cambiar el resto del script.
MYSQL_ARGS=(-u "$MYSQL_USER")
[[ -n "$MYSQL_PASSWORD" ]] && MYSQL_ARGS+=("-p$MYSQL_PASSWORD")
[[ -n "$MYSQL_HOST" ]] && MYSQL_ARGS+=("-h" "$MYSQL_HOST")
[[ -n "$MYSQL_PORT" ]] && MYSQL_ARGS+=("-P" "$MYSQL_PORT")

PREFIX="e2e_$(date +%s)_$$"
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

location_from() {
    grep -i '^Location:' "$1" | head -1 | sed 's/^[Ll]ocation: *//;s/\r$//'
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

HASH="$("$PHP_BIN" -r 'echo password_hash("Ascend2026!", PASSWORD_BCRYPT);')"

ADMIN_EMAIL="${PREFIX}_admin@ascend.test"
ADMIN2_EMAIL="${PREFIX}_admin2@ascend.test"
PLAYER_EMAIL="${PREFIX}_player@ascend.test"
ORG_EMAIL="${PREFIX}_org@ascend.test"

"$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" <<SQL
INSERT INTO usuarios (email, contrasena_hash, nombre_completo, telefono, rol_id, esta_activo, email_verificado)
VALUES
('${ADMIN_EMAIL}',  '${HASH}', 'E2E Admin Uno',  '+59899000101', 1, 1, 1),
('${ADMIN2_EMAIL}', '${HASH}', 'E2E Admin Dos',  '+59899000102', 1, 1, 1),
('${PLAYER_EMAIL}', '${HASH}', 'E2E Jugador',    '+59899000103', 3, 1, 1),
('${ORG_EMAIL}',    '${HASH}', 'E2E Organizador','+59899000104', 2, 1, 1);
SQL

ADMIN_ID=$(sql_val "SELECT id FROM usuarios WHERE email='${ADMIN_EMAIL}'")
ADMIN2_ID=$(sql_val "SELECT id FROM usuarios WHERE email='${ADMIN2_EMAIL}'")
PLAYER_ID=$(sql_val "SELECT id FROM usuarios WHERE email='${PLAYER_EMAIL}'")
ORG_ID=$(sql_val "SELECT id FROM usuarios WHERE email='${ORG_EMAIL}'")

"$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" <<SQL
INSERT INTO perfiles_jugadores (usuario_id, apodo_gamertag) VALUES (${PLAYER_ID}, 'E2EPlayer');
INSERT INTO perfiles_organizadores (usuario_id, nombre_organizacion, verificado_oficial)
VALUES (${ORG_ID}, 'E2E Org', 1);
SQL

COOKIE_ADMIN="$TMP/admin.cookies"
COOKIE_PLAYER="$TMP/player.cookies"

echo "== 1. Registro y verificación de email =="
REG_EMAIL="${PREFIX}_nuevo@ascend.test"
REG_PAGINA=$(curl -fsS -c "$TMP/reg.cookies" "$BASE_URL/index.php?c=auth&a=mostrarRegistro")
REG_TOKEN=$(printf '%s' "$REG_PAGINA" | csrf_from)
[[ -n "$REG_TOKEN" ]] || fail "No se pudo extraer el CSRF del registro."

curl -sS -b "$TMP/reg.cookies" -c "$TMP/reg.cookies" -o "$TMP/registro.html" \
    --data-urlencode "csrf_token=$REG_TOKEN" \
    --data-urlencode "nombre_completo=E2E Nuevo" \
    --data-urlencode "email=$REG_EMAIL" \
    --data-urlencode "telefono=+59899000105" \
    --data-urlencode "contrasena=$PASSWORD" \
    --data-urlencode "contrasena_confirmar=$PASSWORD" \
    --data-urlencode "rol=jugador" \
    "$BASE_URL/index.php?c=auth&a=procesarRegistro"

assert_contains "$(cat "$TMP/registro.html")" "verificar tu correo" "El registro no mostró el enlace de verificación."

VERIFY_TOKEN=$(grep -o 'token=[a-f0-9]\{64\}' "$TMP/registro.html" | head -1 | cut -d= -f2)
[[ -n "$VERIFY_TOKEN" ]] || fail "No se pudo extraer el token de verificación."
VERIFY_URL="index.php?c=auth&a=verificarEmail&token=$VERIFY_TOKEN"

STATUS=$(login "$TMP/unverified.cookies" "$REG_EMAIL" "$TMP/login_unverified.html")
assert_status "$STATUS" "200" "El login sin verificar no debió redirigir."
assert_contains "$(cat "$TMP/login_unverified.html")" "aún no está verificado" "El login sin verificar no fue bloqueado."

curl -fsS "$BASE_URL/$VERIFY_URL" -o "$TMP/verificado.html"
assert_contains "$(cat "$TMP/verificado.html")" "verificado exitosamente" "La verificación del token falló."

STATUS=$(login "$TMP/nuevo.cookies" "$REG_EMAIL" "$TMP/login_nuevo.html")
assert_status "$STATUS" "302" "El login verificado debió redirigir."
assert_contains "$(location_from "$TMP/login_headers.txt")" "panelJugador" "El login verificado no llegó al panel del jugador."

echo "== 2. Email del jugador en solo lectura =="
STATUS=$(login "$COOKIE_PLAYER" "$PLAYER_EMAIL" "$TMP/login_player.html")
assert_status "$STATUS" "302" "El jugador no pudo iniciar sesión."

EDIT_PAGINA=$(curl -fsS -b "$COOKIE_PLAYER" -c "$COOKIE_PLAYER" \
    "$BASE_URL/index.php?c=panelJugador&a=editarPerfil")
assert_contains "$EDIT_PAGINA" 'readonly disabled' "El email del jugador no está en solo lectura."
[[ "$EDIT_PAGINA" != *'name="email"'* ]] || fail "El formulario del jugador todavía envía el email."

EDIT_TOKEN=$(printf '%s' "$EDIT_PAGINA" | csrf_from)
curl -sS -b "$COOKIE_PLAYER" -c "$COOKIE_PLAYER" -o /dev/null -D "$TMP/perfil_headers.txt" \
    --data-urlencode "csrf_token=$EDIT_TOKEN" \
    --data-urlencode "nombre_completo=E2E Jugador Editado" \
    --data-urlencode "email=intruso@ascend.test" \
    --data-urlencode "bio=Bio E2E" \
    --data-urlencode "ciudad=Montevideo" \
    --data-urlencode "pais=Uruguay" \
    --data-urlencode "fecha_nacimiento=1990-01-01" \
    "$BASE_URL/index.php?c=panelJugador&a=actualizarPerfil"

[[ "$(sql_val "SELECT email FROM usuarios WHERE id=${PLAYER_ID}")" == "$PLAYER_EMAIL" ]] \
    || fail "El email del jugador cambió desde su perfil."
[[ "$(sql_val "SELECT email_verificado FROM usuarios WHERE id=${PLAYER_ID}")" == "1" ]] \
    || fail "La verificación del jugador se perdió."
[[ "$(sql_val "SELECT nombre_completo FROM usuarios WHERE id=${PLAYER_ID}")" == "E2E Jugador Editado" ]] \
    || fail "El nombre del jugador no se actualizó."

echo "== 3. Acciones administrativas y baja lógica =="
STATUS=$(login "$COOKIE_ADMIN" "$ADMIN_EMAIL" "$TMP/login_admin.html")
assert_status "$STATUS" "302" "El administrador no pudo iniciar sesión."
assert_contains "$(location_from "$TMP/login_headers.txt")" "c=admin" "El administrador no llegó a su panel."

LISTADO_ADMIN=$(curl -fsS -b "$COOKIE_ADMIN" "$BASE_URL/index.php?c=usuario&a=index")
assert_contains "$LISTADO_ADMIN" "Administradores del sistema" "El listado de administradores no se mostró."

for RUTA in "c=usuario&a=index" "c=jugador&a=index" "c=organizador&a=index" "c=usuario&a=ver&id=${PLAYER_ID}&origen=jugadores"; do
    STATUS=$(curl -sS -b "$COOKIE_ADMIN" -o "$TMP/pagina.html" -w '%{http_code}' "$BASE_URL/index.php?$RUTA")
    assert_status "$STATUS" "200" "La página admin '$RUTA' no respondió 200."
    [[ "$(cat "$TMP/pagina.html")" != *"Fatal error"* ]] || fail "Error fatal en '$RUTA'."
    [[ "$(cat "$TMP/pagina.html")" != *"Warning:"* ]] || fail "Warning de PHP en '$RUTA'."
done

EDIT_ADMIN_PAGINA=$(curl -fsS -b "$COOKIE_ADMIN" "$BASE_URL/index.php?c=usuario&a=editarUsuario&id=${PLAYER_ID}&origen=jugadores")
assert_contains "$EDIT_ADMIN_PAGINA" 'readonly disabled' "El admin puede editar el email."
assert_contains "$EDIT_ADMIN_PAGINA" 'name="confirmar_admin"' "Falta la confirmación para promover a admin."
ADMIN_TOKEN=$(printf '%s' "$EDIT_ADMIN_PAGINA" | csrf_from)

curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/update_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "origen=jugadores" \
    --data-urlencode "nombre_completo=E2E Jugador Admin" \
    --data-urlencode "email=intruso2@ascend.test" \
    --data-urlencode "rol_id=3" \
    "$BASE_URL/index.php?c=usuario&a=actualizarUsuario"

[[ "$(sql_val "SELECT email FROM usuarios WHERE id=${PLAYER_ID}")" == "$PLAYER_EMAIL" ]] \
    || fail "El email cambió desde el panel administrativo."
[[ "$(sql_val "SELECT nombre_completo FROM usuarios WHERE id=${PLAYER_ID}")" == "E2E Jugador Admin" ]] \
    || fail "El nombre no se actualizó desde el panel administrativo."

# Bloquear y auditar
curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/block_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "rol_id=3" \
    "$BASE_URL/index.php?c=usuario&a=cambiarEstadoUsuario"
[[ "$(sql_val "SELECT esta_activo FROM usuarios WHERE id=${PLAYER_ID}")" == "0" ]] || fail "El jugador no quedó bloqueado."
[[ "$(sql_val "SELECT COUNT(*) FROM logs_actividad WHERE accion='ADMIN_CAMBIA_ESTADO_USUARIO' AND descripcion LIKE '%${PLAYER_ID}%'")" -ge 1 ]] \
    || fail "No se auditó el bloqueo."

# Auto-bloqueo rechazado
curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/selfblock_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$ADMIN_ID" \
    --data-urlencode "rol_id=1" \
    "$BASE_URL/index.php?c=usuario&a=cambiarEstadoUsuario"
[[ "$(sql_val "SELECT esta_activo FROM usuarios WHERE id=${ADMIN_ID}")" == "1" ]] || fail "El admin pudo autobloquearse."

# Reactivar
curl -sS -b "$COOKIE_ADMIN" -o /dev/null \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "rol_id=3" \
    "$BASE_URL/index.php?c=usuario&a=cambiarEstadoUsuario"
[[ "$(sql_val "SELECT esta_activo FROM usuarios WHERE id=${PLAYER_ID}")" == "1" ]] || fail "El jugador no se reactivó."

# Baja lógica
curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/delete_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "rol_id=3" \
    "$BASE_URL/index.php?c=usuario&a=eliminarUsuario"

[[ "$(sql_val "SELECT COUNT(*) FROM usuarios WHERE id=${PLAYER_ID}")" == "1" ]] || fail "La baja borró la fila del usuario."
[[ "$(sql_val "SELECT esta_activo FROM usuarios WHERE id=${PLAYER_ID}")" == "0" ]] || fail "La baja no desactivó al usuario."
[[ "$(sql_val "SELECT COUNT(*) FROM perfiles_jugadores WHERE usuario_id=${PLAYER_ID}")" == "1" ]] || fail "La baja eliminó el perfil."
[[ "$(sql_val "SELECT COUNT(*) FROM logs_actividad WHERE accion='ADMIN_BAJA_USUARIO' AND descripcion LIKE '%${PLAYER_ID}%'")" -ge 1 ]] \
    || fail "No se auditó la baja."

# Login de una cuenta dada de baja
STATUS=$(login "$TMP/baja.cookies" "$PLAYER_EMAIL" "$TMP/login_baja.html")
assert_status "$STATUS" "200" "La cuenta dada de baja no debió redirigir."
assert_contains "$(cat "$TMP/login_baja.html")" "inactiva" "La cuenta dada de baja no fue rechazada."

# Reactivar para el resto de las pruebas
curl -sS -b "$COOKIE_ADMIN" -o /dev/null \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "rol_id=3" \
    "$BASE_URL/index.php?c=usuario&a=cambiarEstadoUsuario"

echo "== 4. Suplantación y restauración =="
curl -sS -b "$COOKIE_ADMIN" -c "$COOKIE_ADMIN" -o /dev/null -D "$TMP/imp_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    "$BASE_URL/index.php?c=usuario&a=verComo&id=${PLAYER_ID}"
assert_contains "$(location_from "$TMP/imp_headers.txt")" "panelJugador" "La suplantación no llegó al panel del jugador."

DASH_IMP=$(curl -fsS -b "$COOKIE_ADMIN" -c "$COOKIE_ADMIN" "$BASE_URL/index.php?c=panelJugador&a=dashboard")
assert_contains "$DASH_IMP" "URL_RESTAURAR_SESION" "No se inyectó el banner de suplantación."
assert_contains "$DASH_IMP" "CSRF_TOKEN_SUPLANTACION" "No se inyectó el token de restauración."

BANNER_TOKEN=$(printf '%s' "$DASH_IMP" | grep -o 'CSRF_TOKEN_SUPLANTACION = "[^"]*"' | head -1 | sed 's/.*= "//;s/"$//')
[[ -n "$BANNER_TOKEN" ]] || fail "No se pudo extraer el token del banner."

STATUS=$(curl -sS -b "$COOKIE_ADMIN" -o /dev/null -w '%{http_code}' "$BASE_URL/index.php?c=usuario&a=restaurarSesion")
assert_status "$STATUS" "405" "restaurarSesion por GET debió devolver 405."

# POST bloqueado con Referer externo: debe volver al panel, no al dominio externo
curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/ref_headers.txt" \
    -H "Referer: http://evil.example/ataque" \
    --data-urlencode "csrf_token=$BANNER_TOKEN" \
    --data-urlencode "nombre_completo=No Debe Cambiar" \
    "$BASE_URL/index.php?c=panelJugador&a=actualizarPerfil"
LOC=$(location_from "$TMP/ref_headers.txt")
[[ "$LOC" != *"evil.example"* ]] || fail "El bloqueo de escritura aceptó un Referer externo."
assert_contains "$LOC" "panelJugador" "El bloqueo de escritura no volvió al panel del rol suplantado."

curl -sS -b "$COOKIE_ADMIN" -c "$COOKIE_ADMIN" -o /dev/null -D "$TMP/restore_headers.txt" \
    --data-urlencode "csrf_token=$BANNER_TOKEN" \
    "$BASE_URL/index.php?c=usuario&a=restaurarSesion"
assert_contains "$(location_from "$TMP/restore_headers.txt")" "c=admin" "La restauración no volvió al panel admin."

STATUS=$(curl -sS -b "$COOKIE_ADMIN" -o /dev/null -w '%{http_code}' "$BASE_URL/index.php?c=admin&a=dashboard")
assert_status "$STATUS" "200" "La sesión no se restauró correctamente."
[[ "$(sql_val "SELECT COUNT(*) FROM logs_actividad WHERE accion='ADMIN_SUPLANTA_INICIO' AND descripcion LIKE '%${PLAYER_ID}%'")" -ge 1 ]] \
    || fail "No se auditó el inicio de suplantación."
[[ "$(sql_val "SELECT COUNT(*) FROM logs_actividad WHERE accion='ADMIN_SUPLANTA_FIN' AND descripcion LIKE '%${PLAYER_ID}%'")" -ge 1 ]] \
    || fail "No se auditó el fin de suplantación."

# No se puede suplantar a otro administrador
curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/imp_admin_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    "$BASE_URL/index.php?c=usuario&a=verComo&id=${ADMIN2_ID}"
STATUS=$(curl -sS -b "$COOKIE_ADMIN" -o /dev/null -w '%{http_code}' "$BASE_URL/index.php?c=admin&a=dashboard")
assert_status "$STATUS" "200" "La sesión cambió al intentar suplantar a otro admin."

echo "== 5. Cambio de rol y sincronización =="
# Promoción a admin sin confirmación: rechazada
curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/rol_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "origen=jugadores" \
    --data-urlencode "nombre_completo=E2E Jugador Admin" \
    --data-urlencode "rol_id=1" \
    "$BASE_URL/index.php?c=usuario&a=actualizarUsuario"
[[ "$(sql_val "SELECT rol_id FROM usuarios WHERE id=${PLAYER_ID}")" == "3" ]] || fail "Se promovió a admin sin confirmación."

# Con confirmación: aceptada
curl -sS -b "$COOKIE_ADMIN" -o /dev/null -D "$TMP/rol2_headers.txt" \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "origen=jugadores" \
    --data-urlencode "nombre_completo=E2E Jugador Admin" \
    --data-urlencode "rol_id=1" \
    --data-urlencode "confirmar_admin=1" \
    "$BASE_URL/index.php?c=usuario&a=actualizarUsuario"
[[ "$(sql_val "SELECT rol_id FROM usuarios WHERE id=${PLAYER_ID}")" == "1" ]] || fail "No se promovió a admin con confirmación."

# Volver a jugador
curl -sS -b "$COOKIE_ADMIN" -o /dev/null \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "origen=jugadores" \
    --data-urlencode "nombre_completo=E2E Jugador Admin" \
    --data-urlencode "rol_id=3" \
    "$BASE_URL/index.php?c=usuario&a=actualizarUsuario"
[[ "$(sql_val "SELECT rol_id FROM usuarios WHERE id=${PLAYER_ID}")" == "3" ]] || fail "No se pudo volver a jugador."

# Promover a organizador: debe crear el perfil del rol destino
curl -sS -b "$COOKIE_ADMIN" -o /dev/null \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "origen=jugadores" \
    --data-urlencode "nombre_completo=E2E Jugador Admin" \
    --data-urlencode "rol_id=2" \
    "$BASE_URL/index.php?c=usuario&a=actualizarUsuario"
[[ "$(sql_val "SELECT rol_id FROM usuarios WHERE id=${PLAYER_ID}")" == "2" ]] || fail "No se cambió a organizador."
[[ "$(sql_val "SELECT COUNT(*) FROM perfiles_organizadores WHERE usuario_id=${PLAYER_ID}")" == "1" ]] \
    || fail "No se creó el perfil de organizador al cambiar el rol."

# Volver a jugador para dejar el fixture consistente
curl -sS -b "$COOKIE_ADMIN" -o /dev/null \
    --data-urlencode "csrf_token=$ADMIN_TOKEN" \
    --data-urlencode "id=$PLAYER_ID" \
    --data-urlencode "origen=jugadores" \
    --data-urlencode "nombre_completo=E2E Jugador Admin" \
    --data-urlencode "rol_id=3" \
    "$BASE_URL/index.php?c=usuario&a=actualizarUsuario"

echo "== 6. Regresión final de páginas admin =="
for RUTA in "c=usuario&a=index" "c=jugador&a=index" "c=organizador&a=index" "c=juego&a=index" "c=auditoria&a=index" "c=usuario&a=ver&id=${PLAYER_ID}"; do
    STATUS=$(curl -sS -b "$COOKIE_ADMIN" -o "$TMP/final.html" -w '%{http_code}' "$BASE_URL/index.php?$RUTA")
    assert_status "$STATUS" "200" "Regresión: '$RUTA' no respondió 200."
    [[ "$(cat "$TMP/final.html")" != *"Fatal error"* ]] || fail "Error fatal en '$RUTA'."
    [[ "$(cat "$TMP/final.html")" != *"Warning:"* ]] || fail "Warning de PHP en '$RUTA'."
done

echo "OK: E2E de seguridad admin completado ($PREFIX)."
