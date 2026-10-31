#!/usr/bin/env bash
set -Eeuo pipefail

PROJECT_ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
COMPOSE_FILE="${PROJECT_ROOT}/docker/docker-compose.yml"
SVC_DB="${SVC_DB:-db}"
PORT_APP=8080
PORT_PMA=8081
PORT_GRAFANA=3001
cd -- "$PROJECT_ROOT"

die() {
  printf '\n[ERROR] %s\n' "$*" >&2
  exit 1
}

SERVER_HOST="${SERVER_HOST:-$(hostname -I 2>/dev/null | awk '{print $1}')}"
SERVER_HOST="${SERVER_HOST:-localhost}"
APP_URL="${APP_URL:-http://${SERVER_HOST}:${PORT_APP}/}"

printf '=== Desplegando ASCEND desde el paquete local ===\n'

command -v docker >/dev/null 2>&1 || die "Docker no está instalado."
docker compose version >/dev/null 2>&1 || die "Falta el plugin 'docker compose' (v2)."
docker info >/dev/null 2>&1 || die "Docker no está activo o tu usuario no tiene permisos."
command -v curl >/dev/null 2>&1 || die "curl es necesario para verificar la web."
command -v grep >/dev/null 2>&1 || die "grep es necesario para verificar los contenedores."
[ -f "$COMPOSE_FILE" ] || die "No se encontró $COMPOSE_FILE"
[ -f "${PROJECT_ROOT}/docker/Dockerfile" ] || die "No se encontró el Dockerfile."
[ -d "${PROJECT_ROOT}/codigo_fuente" ] || die "No se encontró codigo_fuente/."
[ -d "${PROJECT_ROOT}/base_de_datos/migraciones" ] || die "No se encontró base_de_datos/migraciones/."

DC=(docker compose -f "$COMPOSE_FILE")

printf '[1/5] Validando configuración de Docker...\n'
"${DC[@]}" config >/dev/null

printf '[2/5] Construyendo y levantando todos los contenedores...\n'
"${DC[@]}" up -d --build

RUNNING_SERVICES=$("${DC[@]}" ps --status running --services)
for service in web db phpmyadmin grafana; do
  if ! grep -Fxq "$service" <<<"$RUNNING_SERVICES"; then
    "${DC[@]}" ps
    "${DC[@]}" logs --tail=40 "$service" || true
    die "El servicio '$service' no quedó en ejecución."
  fi
done

printf '[3/5] Esperando a que MySQL responda por TCP...\n'
MYSQL_READY=0
for ((attempt = 1; attempt <= 60; attempt++)); do
  if "${DC[@]}" exec -T "$SVC_DB" \
    sh -c 'mysqladmin ping -h 127.0.0.1 -uroot -p"$MYSQL_ROOT_PASSWORD" --silent' \
    >/dev/null 2>&1; then
    MYSQL_READY=1
    break
  fi
  sleep 2
done

if [ "$MYSQL_READY" -ne 1 ]; then
  "${DC[@]}" logs --tail=40 "$SVC_DB" || true
  die "MySQL tardó demasiado en responder."
fi

printf '[4/5] Verificando la base de datos inicializada por las migraciones...\n'
if ! TABLE_COUNT=$("${DC[@]}" exec -T "$SVC_DB" sh -c \
  'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()" "${MYSQL_DATABASE:-ascend}"'); then
  die "No se pudo consultar la base de datos."
fi

if ! [[ "$TABLE_COUNT" =~ ^[0-9]+$ ]] || [ "$TABLE_COUNT" -eq 0 ]; then
  "${DC[@]}" logs --tail=40 "$SVC_DB" || true
  die "La base responde, pero no tiene tablas. Revisa las migraciones en base_de_datos/migraciones/."
fi
printf 'Base de datos lista (%s tablas). Las migraciones se aplican automáticamente al crear el volumen.\n' "$TABLE_COUNT"

printf '[5/5] Verificando que la aplicación responda...\n'
APP_READY=0
for ((attempt = 1; attempt <= 30; attempt++)); do
  if curl --fail --silent --output /dev/null --max-time 5 \
    "http://127.0.0.1:${PORT_APP}/"; then
    APP_READY=1
    break
  fi
  sleep 2
done

if [ "$APP_READY" -ne 1 ]; then
  "${DC[@]}" logs --tail=40 web || true
  die "La aplicación no respondió correctamente en http://127.0.0.1:${PORT_APP}/."
fi

"${DC[@]}" ps

printf '\n==============================================\n'
printf '¡ASCEND se ha desplegado!\n'
printf '==============================================\n'
printf 'Aplicación:  %s\n' "$APP_URL"
printf 'phpMyAdmin:  http://localhost:%s (tras abrir un túnel SSH)\n' "$PORT_PMA"
printf 'Grafana:     http://localhost:%s (tras abrir un túnel SSH)\n' "$PORT_GRAFANA"
printf 'Túnel:       ssh -L %s:127.0.0.1:%s -L %s:127.0.0.1:%s usuario@%s\n' \
  "$PORT_PMA" "$PORT_PMA" "$PORT_GRAFANA" "$PORT_GRAFANA" "$SERVER_HOST"
printf '==============================================\n'
