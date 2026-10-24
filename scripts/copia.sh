#!/usr/bin/env bash
# Copia de seguridad: la base de datos y las imágenes subidas, en copias/AAAA-MM-DD-HHMM/ (fuera de git).
set -euo pipefail
cd "$(dirname "$0")/.."
set -a; source .env; set +a
d="copias/$(date +%F-%H%M)"; mkdir -p "$d"
docker compose exec -T db sh -c 'exec mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction wordpress' | gzip > "$d/base.sql.gz"
docker compose exec -T wordpress tar -C /var/www/html/wp-content -czf - uploads > "$d/uploads.tar.gz"
ls -lh "$d" | tail -n +2
echo "Copia hecha en $d. Llévala fuera de este ordenador (otro disco, la nube)."
