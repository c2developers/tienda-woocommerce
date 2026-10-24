#!/usr/bin/env bash
# Deja WordPress + WooCommerce instalado y configurado para vender en España, sin tocar el ratón.
# Se puede repetir: lo que ya está hecho se salta.
set -euo pipefail
cd "$(dirname "$0")/.."
set -a; source .env; set +a
# wp-cli en un contenedor de usar y tirar, con el mismo volumen que WordPress
wp() { docker compose --progress quiet run --rm -T --no-deps wpcli wp "$@" 2>&1 | { grep -v -e '^ Container ' || true; }; }
paso() { printf '\n\033[38;5;209m▸ %s\033[0m\n' "$*"; }

paso "Esperando a WordPress"
until docker compose exec -T wordpress test -f /var/www/html/wp-config.php 2>/dev/null; do sleep 2; done

paso "Instalando WordPress en $SITIO_URL"
if ! docker compose --progress quiet run --rm -T --no-deps wpcli wp core is-installed >/dev/null 2>&1; then
  if [ -z "${WP_ADMIN_PASSWORD:-}" ]; then
    WP_ADMIN_PASSWORD=$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)
    echo "WP_ADMIN_PASSWORD=$WP_ADMIN_PASSWORD" >> .env
  fi
  wp core install --url="$SITIO_URL" --title="$TIENDA_NOMBRE" --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASSWORD" --admin_email="$WP_ADMIN_EMAIL" --skip-email
  echo "Administrador «$WP_ADMIN_USER» creado (su contraseña está en .env)"
fi

paso "En español, hora de Madrid y enlaces bonitos"
wp language core install es_ES --activate
wp option update timezone_string Europe/Madrid
wp option update date_format 'j \d\e F \d\e Y'
wp option update blogdescription "$TIENDA_LEMA"
wp rewrite structure '/%postname%/' --hard
wp post delete 1 2 --force 2>/dev/null || true
wp plugin delete akismet hello 2>/dev/null || true

paso "Plugins: todos gratuitos, del directorio oficial de WordPress"
PLUGINS="woocommerce woocommerce-gateway-stripe wp-mail-smtp google-site-kit facebook-for-woocommerce cookie-law-info"
wp plugin install $PLUGINS
wp language plugin install --all es_ES   # antes de activar: así WooCommerce crea sus páginas en español
wp plugin activate $PLUGINS

paso "El tema de la tienda"
wp theme install twentytwentyfive
wp theme activate "$TEMA"

paso "WooCommerce para España: euros, IVA incluido y correos por Resend"
wp eval-file /scripts/configurar.php

paso "Envíos: Península y Baleares (Canarias, Ceuta y Melilla llevan otros impuestos)"
wp eval-file /scripts/envios.php

paso "Listo"
echo "Tienda: $SITIO_URL   ·   Admin: $SITIO_URL/wp-admin"
