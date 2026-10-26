<?php
// WooCommerce listo para vender en España: país, moneda, IVA, envíos y correos.
// Lo ejecuta instalar.sh con `wp eval-file`; se puede repetir sin miedo.
$e = fn( $k ) => getenv( $k ) ?: '';

$opciones = [
	// La tienda
	'woocommerce_default_country'           => 'ES:M',
	'woocommerce_store_address'             => $e( 'TIENDA_DIRECCION' ),
	'woocommerce_store_city'                => $e( 'TIENDA_CIUDAD' ),
	'woocommerce_store_postcode'            => $e( 'TIENDA_CP' ),
	'woocommerce_allowed_countries'         => 'specific',
	'woocommerce_specific_allowed_countries' => [ 'ES' ],
	'woocommerce_ship_to_countries'         => 'specific',
	'woocommerce_specific_ship_to_countries' => [ 'ES' ],
	// Euros, con coma decimal y el símbolo detrás: 12,50 €
	'woocommerce_currency'                  => 'EUR',
	'woocommerce_currency_pos'              => 'right_space',
	'woocommerce_price_thousand_sep'        => '.',
	'woocommerce_price_decimal_sep'         => ',',
	'woocommerce_price_num_decimals'        => 2,
	'woocommerce_weight_unit'               => 'g',
	'woocommerce_dimension_unit'            => 'cm',
	// IVA incluido en el precio, como se vende en España
	'woocommerce_calc_taxes'                => 'yes',
	'woocommerce_prices_include_tax'        => 'yes',
	'woocommerce_tax_display_shop'          => 'incl',
	'woocommerce_tax_display_cart'          => 'incl',
	'woocommerce_tax_based_on'              => 'base',
	'woocommerce_tax_total_display'         => 'itemized',
	// El envío lleva el IVA de lo que se envía (10 % con café, 21 % con accesorios)
	'woocommerce_shipping_tax_class'        => 'inherit',
	// Comprar sin cuenta, y sin el asistente ni el modo «próximamente»
	'woocommerce_enable_guest_checkout'     => 'yes',
	'woocommerce_price_display_suffix'      => 'IVA incluido',
	// Sin valoraciones: una tienda nueva no tiene, y no se inventan
	'woocommerce_enable_reviews'            => 'no',
	'woocommerce_coming_soon'               => 'no',
	'woocommerce_allow_tracking'            => 'no',
	'woocommerce_show_marketplace_suggestions' => 'no',
	'woocommerce_onboarding_profile'        => [ 'skipped' => true ],
	// Los correos salen con nuestro nombre y por Resend
	'woocommerce_email_from_name'           => $e( 'TIENDA_NOMBRE' ),
	'woocommerce_email_from_address'        => $e( 'CORREO_REMITENTE' ),
	'woocommerce_email_base_color'          => '#C2562C',
	'woocommerce_email_background_color'    => '#F6F0E7',
	'woocommerce_email_body_background_color' => '#FCF9F4',
	'woocommerce_email_text_color'          => '#1E1612',
];
foreach ( $opciones as $k => $v ) { update_option( $k, $v ); }
WP_CLI::log( sprintf( '%d ajustes de WooCommerce', count( $opciones ) ) );

// IVA: 21 % general (accesorios y envío) y una clase propia, «IVA reducido», para el café.
// (No uso «reduced-rate»: con WooCommerce en español esa clase se llama «tarifa-reducida».)
$iva_cafe = (float) ( $e( 'IVA_CAFE' ) ?: 10 );
if ( ! in_array( 'iva-reducido', WC_Tax::get_tax_class_slugs(), true ) ) {
	WC_Tax::create_tax_class( 'IVA reducido', 'iva-reducido' );
}
global $wpdb;
$hay = fn( $clase ) => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_class = %s", $clase ) );
if ( ! $hay( '' ) ) {
	WC_Tax::_insert_tax_rate( [ 'tax_rate_country' => 'ES', 'tax_rate' => '21.0000', 'tax_rate_name' => 'IVA', 'tax_rate_priority' => 1, 'tax_rate_shipping' => 1, 'tax_rate_class' => '' ] );
}
if ( ! $hay( 'iva-reducido' ) ) {
	WC_Tax::_insert_tax_rate( [ 'tax_rate_country' => 'ES', 'tax_rate' => number_format( $iva_cafe, 4, '.', '' ), 'tax_rate_name' => 'IVA', 'tax_rate_priority' => 1, 'tax_rate_shipping' => 1, 'tax_rate_class' => 'iva-reducido' ] );
}
$wpdb->update( "{$wpdb->prefix}woocommerce_tax_rates", [ 'tax_rate_shipping' => 1 ], [ 'tax_rate_class' => 'iva-reducido' ] );
// Restos de una versión anterior: en la clase estándar solo va el 21 %.
foreach ( $wpdb->get_results( "SELECT tax_rate_id FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_class = '' AND tax_rate <> '21.0000'" ) as $r ) {
	WC_Tax::_delete_tax_rate( $r->tax_rate_id );
}
WP_CLI::log( "IVA: 21 % general y $iva_cafe % para el café" );
