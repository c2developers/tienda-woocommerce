#!/usr/bin/env bash
# wp-cli dentro de Docker, con el mismo volumen que WordPress: ./scripts/wp.sh plugin list
cd "$(dirname "$0")/.." && exec docker compose --progress quiet run --rm -T --no-deps wpcli wp "$@"
