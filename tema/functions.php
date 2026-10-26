<?php
/**
 * Tostela: lo poco que no cabe en theme.json.
 */

// La hoja de estilos del tema hijo (detalles de las tarjetas, la etiqueta y la cabecera fija).
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'tostela', get_stylesheet_uri(), [], wp_get_theme()->get( 'Version' ) );
} );
add_action( 'after_setup_theme', function () {
	add_editor_style( 'style.css' );
} );

// En el pie y en los correos, la tienda dice que es de ejemplo.
add_filter( 'woocommerce_email_footer_text', function ( $texto ) {
	return 'Tostela es una tienda de ejemplo del tutorial de C2 Prompt & Code: no se envía ningún pedido.';
} );

/*
 * Meta (píxel y API de conversiones) solo con permiso. El plugin de Meta sabe «retener» sus señales, pero no
 * lee el aviso de cookies: este puente lo hace. Sin «Publicidad» aceptada en el aviso (cookie de CookieYes),
 * Meta no recibe nada, ni desde el navegador ni desde el servidor. Al aceptar, se liberan las señales.
 */
function tostela_meta_permitido() {
	$c = isset( $_COOKIE['cookieyes-consent'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['cookieyes-consent'] ) ) : '';
	return str_contains( $c, 'advertisement:yes' );
}
add_filter( 'facebook_signals_held', fn() => ! tostela_meta_permitido() );

// El script de Meta en el navegador lee su propia cookie: la ponemos en «held» mientras no haya permiso,
// así ni siquiera inicia el píxel (ni crea la cookie _fbp) hasta que el visitante acepta.
add_action( 'init', function () {
	if ( is_admin() || wp_doing_ajax() || headers_sent() ) { return; }
	$quiero = tostela_meta_permitido() ? 'active' : 'held';
	if ( ( $_COOKIE['wc_facebook_signals_state'] ?? '' ) !== $quiero ) {
		setcookie( 'wc_facebook_signals_state', $quiero, [ 'expires' => time() + YEAR_IN_SECONDS, 'path' => COOKIEPATH ?: '/', 'secure' => is_ssl(), 'samesite' => 'Lax' ] );
		$_COOKIE['wc_facebook_signals_state'] = $quiero;
	}
}, 1 );

add_action( 'wp_footer', function () {
	?>
	<script id="tostela-consentimiento-meta">
	document.addEventListener('cookieyes_consent_update', function (e) {
		var si = e.detail && Array.isArray(e.detail.accepted) && e.detail.accepted.indexOf('advertisement') !== -1;
		document.cookie = 'wc_facebook_signals_state=' + (si ? 'active' : 'held') + ';path=/;max-age=31536000;samesite=lax' + (location.protocol === 'https:' ? ';secure' : '');
		if (si && window.FacebookSignals && typeof window.FacebookSignals.release === 'function') { window.FacebookSignals.release(); }
	});
	</script>
	<?php
}, 100 );

// Si el pedido ya tiene envío gratis, no ofrecemos el de pago.
add_filter( 'woocommerce_package_rates', function ( $tarifas ) {
	$gratis = array_filter( $tarifas, fn( $t ) => 'free_shipping' === $t->get_method_id() );
	return $gratis ?: $tarifas;
}, 100 );
