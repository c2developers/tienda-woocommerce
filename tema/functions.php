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
