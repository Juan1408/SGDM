#!/usr/bin/env bash
# ============================================================================
# E2E: página pública "Formatos de torneo"
# ============================================================================
# Cubre:
#   - La ruta inicio&a=formatos responde 200 y no muestra "en construcción".
#   - Existen las anclas liga, eliminacion-directa, suizo, comparativa y
#     organizadores.
#   - El contenido coincide con las reglas implementadas en el modelo:
#     todos contra todos, 2/4/8/16/32/64 y cantidad par en el suizo.
#   - El pie enlaza cada formato a su ancla y ya no a enConstruccion.
#
# Uso (XAMPP, Git Bash):
#   PHP_BIN=/c/xampp/php/php.exe bash tests/e2e/formatos_e2e.sh
#
# Uso contra un entorno ya levantado:
#   BASE_URL=http://127.0.0.1:8082 START_SERVER=0 bash tests/e2e/formatos_e2e.sh
# ============================================================================
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
BASE_URL="${BASE_URL:-http://127.0.0.1:8097}"
PORT="${PORT:-8097}"
PHP_BIN="${PHP_BIN:-php}"
START_SERVER="${START_SERVER:-1}"
TMP="$(mktemp -d)"
SERVER_PID=""

cleanup() {
    if [[ -n "$SERVER_PID" ]]; then
        kill "$SERVER_PID" 2>/dev/null || true
    fi
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

assert_not_contains() {
    local texto="$1" inesperado="$2" mensaje="$3"
    [[ "$texto" != *"$inesperado"* ]] || fail "$mensaje"
}

assert_regex() {
    local texto="$1" patron="$2" mensaje="$3"
    printf '%s' "$texto" | grep -Eq "$patron" || fail "$mensaje"
}

assert_status() {
    local obtenido="$1" esperado="$2" mensaje="$3"
    [[ "$obtenido" == "$esperado" ]] || fail "$mensaje (HTTP $obtenido)"
}

# ---------------------------------------------------------------------------
# Servidor PHP aislado (la página no consulta la base, pero el front controller
# carga la configuración completa)
# ---------------------------------------------------------------------------
if curl -fsS "$BASE_URL/index.php?c=inicio&a=index" >/dev/null 2>&1; then
    START_SERVER=0
fi

if [[ "$START_SERVER" == "1" ]]; then
    ( cd "$ROOT" && exec env DB_NAME=ascend_test APP_URL="$BASE_URL/" \
        "$PHP_BIN" -S "127.0.0.1:$PORT" -t codigo_fuente/publico >"$TMP/server.log" 2>&1 ) &
    SERVER_PID=$!

    listo=0
    for _ in $(seq 1 50); do
        if curl -fsS "$BASE_URL/index.php?c=inicio&a=index" >/dev/null 2>&1; then
            listo=1
            break
        fi
        sleep 0.2
    done
    [[ "$listo" == "1" ]] || fail "El servidor PHP no respondió. Log: $(cat "$TMP/server.log")"
fi

# ---------------------------------------------------------------------------
# 1. Página y anclas
# ---------------------------------------------------------------------------
echo "== 1. Página de formatos =="
STATUS=$(curl -sS -o "$TMP/formatos.html" -w '%{http_code}' "$BASE_URL/index.php?c=inicio&a=formatos")
assert_status "$STATUS" "200" "La página de formatos no responde."
PAGINA="$(cat "$TMP/formatos.html")"

assert_contains "$PAGINA" "<title>ASCEND - Formatos de torneo</title>" "La página no tiene el título esperado."
assert_not_contains "$PAGINA" "Pantalla en Construcción" "La página sigue mostrando el aviso de en construcción."

for ancla in liga eliminacion-directa suizo comparativa organizadores; do
    assert_regex "$PAGINA" "id=\"${ancla}\"" "Falta la sección #${ancla}."
done

# ---------------------------------------------------------------------------
# 2. Contenido alineado con el modelo
# ---------------------------------------------------------------------------
echo "== 2. Reglas de cada formato =="
assert_contains "$PAGINA" "todos contra todos" "La liga no se describe como todos contra todos."
assert_contains "$PAGINA" "2, 4, 8, 16, 32 o 64" "La eliminación directa no indica las cantidades válidas."
assert_contains "$PAGINA" "La cantidad de rondas se calcula" "El suizo no explica cómo se calculan las rondas."
assert_contains "$PAGINA" "diferencia de goles" "No se mencionan los desempates de la tabla."
assert_contains "$PAGINA" "Se necesita una cantidad <strong>par</strong>" "El suizo no exige cantidad par."
assert_contains "$PAGINA" "no se puede cargar un empate" "La eliminación directa no aclara que no admite empates."

# ---------------------------------------------------------------------------
# 3. Enlaces del pie
# ---------------------------------------------------------------------------
echo "== 3. Enlaces del pie =="
INICIO=$(curl -fsS "$BASE_URL/index.php?c=inicio&a=index")

assert_regex "$INICIO" 'href="[^"]*a=formatos#liga"[^>]*>Liga<' "El pie no enlaza Liga a #liga."
assert_regex "$INICIO" 'href="[^"]*a=formatos#eliminacion-directa"[^>]*>Eliminación Directa<' "El pie no enlaza Eliminación Directa a su ancla."
assert_regex "$INICIO" 'href="[^"]*a=formatos#suizo"[^>]*>Sistema Suizo<' "El pie no enlaza Sistema Suizo a su ancla."

[[ "$(printf '%s' "$INICIO" | grep -c 'a=enConstruccion">\(Liga\|Eliminación Directa\|Sistema Suizo\)')" == "0" ]] \
    || fail "Algún enlace de formato del pie sigue apuntando a enConstruccion."

echo "OK: formatos de torneo verificados."
