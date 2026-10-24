<?php
// El aviso de cookies (CookieYes) en español: idioma por defecto, textos del banner y nombres de las categorías.
// Usa las traducciones que trae el propio plugin, pone los colores de la tienda y activa Consent Mode v2.
use CookieYes\Lite\Admin\Modules\Banners\Includes\Banner;

$s = get_option( 'cky_settings' );
$s['languages'] = [ 'default' => 'es', 'selected' => [ 'es' ] ];
update_option( 'cky_settings', $s );

$base = WP_PLUGIN_DIR . '/cookie-law-info/lite/admin/modules';
$es   = json_decode( file_get_contents( "$base/banners/includes/contents/es.json" ), true );
global $wpdb;
foreach ( $wpdb->get_col( "SELECT banner_id FROM {$wpdb->prefix}cky_banners" ) as $id ) {
	$b   = new Banner( (int) $id );
	$set = $b->get_settings();
	$set['settings']['languages'] = [ 'default' => 'es', 'selected' => [ 'es' ] ];
	$b->set_settings( $set );
	$b->set_contents( [ 'es' => $es[ $b->get_law() ] ?? $es['gdpr'] ] );
	$b->update( $b );
}
// Categorías de cookies (Necesarias, Analíticas…) en español
$cat = json_decode( file_get_contents( "$base/cookies/includes/contents/categories/es.json" ), true );
foreach ( $wpdb->get_results( "SELECT category_id, slug, name, description FROM {$wpdb->prefix}cky_cookie_categories" ) as $c ) {
	if ( empty( $cat[ $c->slug ] ) ) { continue; }
	$name = json_decode( $c->name, true ) ?: []; $desc = json_decode( $c->description, true ) ?: [];
	$name['es'] = $cat[ $c->slug ]['name'] ?? ( $name['en'] ?? '' );
	$desc['es'] = $cat[ $c->slug ]['description'] ?? ( $desc['en'] ?? '' );
	$wpdb->update( "{$wpdb->prefix}cky_cookie_categories", [ 'name' => wp_json_encode( $name ), 'description' => wp_json_encode( $desc ) ], [ 'category_id' => $c->category_id ] );
}
do_action( 'cky_after_update_banner' );
do_action( 'cky_after_update_cookie_category' );
wp_cache_flush();
WP_CLI::success( 'Aviso de cookies en español' );

// Los colores de la tienda en el aviso: el azul de CookieYes pasa a espresso, con letra crema.
foreach ( $wpdb->get_results( "SELECT banner_id, settings FROM {$wpdb->prefix}cky_banners" ) as $r ) {
	$json = str_ireplace( [ '#1863DC', '#0056A7' ], '#1E1612', $r->settings );   // botones y botón flotante
	$wpdb->update( "{$wpdb->prefix}cky_banners", [ 'settings' => $json ], [ 'banner_id' => $r->banner_id ] );
}
do_action( 'cky_after_update_banner' );
wp_cache_flush();
WP_CLI::success( 'Colores de la tienda en el aviso' );

// Google Consent Mode v2, por la vía oficial: WP Consent API recoge lo que el visitante elige en el aviso y
// Site Kit se lo pasa a Google. Analytics y anuncios arrancan en «denied» en la UE hasta que el visitante acepta.
// (En CookieYes sin cuenta, su propio Consent Mode no se carga: solo funciona conectado a su web.)
if ( ! is_plugin_active( 'wp-consent-api/wp-consent-api.php' ) ) {
	WP_CLI::runcommand( 'plugin install wp-consent-api --activate', [ 'return' => false ] );
}
$cm = get_option( 'googlesitekit_consent_mode' ) ?: [];
$cm['enabled'] = true;
if ( empty( $cm['regions'] ) && class_exists( '\Google\Site_Kit\Core\Consent_Mode\Regions' ) ) {
	$cm['regions'] = \Google\Site_Kit\Core\Consent_Mode\Regions::get_regions();
}
update_option( 'googlesitekit_consent_mode', $cm );
WP_CLI::success( 'Consent Mode v2 activado en Site Kit (con WP Consent API)' );
