# Tienda WooCommerce «gratis»: sin las comisiones de Shopify

Una tienda online completa con **WordPress + WooCommerce** que corre en tu ordenador con Docker y se publica
con HTTPS en tu dominio gracias a un **túnel de Cloudflare**. Es el proyecto del tutorial de
[C2 Prompt & Code](https://www.youtube.com/@c2developers): la tienda de ejemplo, **Tostela**, vende café de
especialidad (la marca y los productos son inventados y las fotos están generadas con IA).

Incluye: tema hijo de Twenty Twenty-Five hecho a medida (todo en `tema/theme.json`), catálogo con variaciones
(molienda × peso), IVA de España (10 % café, 21 % accesorios), envíos a península y Baleares, correos por
**Resend**, pagos con **Stripe**, **Google Site Kit** (Analytics y Search Console), **píxel de Meta** con la API de
conversiones, aviso de cookies con **Consent Mode v2** y plantillas de páginas legales.

> «Gratis» entre comillas: WordPress, WooCommerce y todos los plugins de aquí son gratuitos y no hay comisión de
> plataforma. Pagas el dominio, la comisión de la pasarela por cada venta (Stripe) y, cuando la pases a
> producción, un servidor pequeño.

## Requisitos

- Docker con Docker Compose
- Un dominio en Cloudflare y `cloudflared` con sesión (`cloudflared tunnel login`)
- Cuentas en Resend (correo), Stripe (pagos), Google (Analytics/Search Console) y Meta (píxel)

## Pasos

```bash
cp .env.example .env                 # y rellénalo
docker compose up -d                 # MariaDB + WordPress

# Publicarla en tu dominio
cloudflared tunnel create tienda
cloudflared tunnel route dns tienda tienda.tudominio.com
cp ~/.cloudflared/<ID>.json cloudflared/credenciales.json
cp cloudflared/config.yml.example cloudflared/config.yml   # pon el ID y tu dominio
docker compose --profile tunel up -d cloudflared

./scripts/instalar.sh                # WordPress + WooCommerce configurados para España
python3 scripts/imagenes.py          # las fotos con IA (necesita GEMINI_API_KEY)
./scripts/wp.sh eval-file /scripts/productos.php   # el catálogo
./scripts/wp.sh eval-file /scripts/paginas.php     # páginas y textos legales
./scripts/wp.sh eval-file /scripts/cookies.php     # aviso de cookies en español y Consent Mode v2
```

Después, en el escritorio de WordPress: conecta Stripe (WooCommerce → Ajustes → Pagos), Site Kit y Meta for
WooCommerce. El aviso de cookies (CookieYes) pasa la decisión del visitante a Site Kit por WP Consent API:
así Google Analytics funciona con Consent Mode v2 y no mide a nadie que no haya aceptado.

> Ojo con el IVA: con WooCommerce en español, la clase de «tarifa reducida» no se llama `reduced-rate`. Por eso el
> café usa una clase propia, «IVA reducido» (10 %), que crea `scripts/configurar.php`.

## Qué hay en cada sitio

| Ruta | Qué es |
|---|---|
| `docker-compose.yml` | Los servicios: base de datos, WordPress, wp-cli y el túnel |
| `scripts/instalar.sh` | Instala y configura WordPress y WooCommerce desde la terminal |
| `scripts/configurar.php`, `envios.php` | Moneda, IVA, envíos y correos de WooCommerce |
| `scripts/cookies.php` | Aviso de cookies en español, con los colores de la tienda, y Consent Mode v2 |
| `scripts/copia.sh` | Copia de seguridad de la base de datos y de las imágenes |
| `scripts/productos.php` + `contenido/productos.json` | El catálogo |
| `scripts/imagenes.py` + `contenido/imagenes.json` | Las fotos, generadas con Gemini |
| `tema/` | El tema hijo: `theme.json`, cabecera, pie, portada y patrones |
| `contenido/paginas/` | Nuestra historia, envíos, condiciones, legal y cookies (plantillas: revísalas con un abogado) |

## Licencia

GPL-2.0-or-later, como WordPress.
