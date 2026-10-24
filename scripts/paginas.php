<?php
// Crea (o actualiza) las páginas de contenido/paginas/*.html. El nombre del fichero es el slug;
// el título sale de la tabla de abajo.
$titulos = [
	'nuestra-historia'        => 'Nuestra historia',
	'envios-y-devoluciones'   => 'Envíos y devoluciones',
	'condiciones-de-venta'    => 'Condiciones de venta',
	'aviso-legal'             => 'Aviso legal',
	'politica-de-privacidad'  => 'Política de privacidad',
	'desistir'                => 'Desistir del contrato',
	'politica-de-cookies'     => 'Política de cookies',
	'contacto'                => 'Contacto',
];
foreach ( $titulos as $slug => $titulo ) {
	$f = "/contenido/paginas/$slug.html";
	if ( ! file_exists( $f ) ) { continue; }
	$p = get_page_by_path( $slug );
	$datos = [ 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $titulo, 'post_name' => $slug, 'post_content' => file_get_contents( $f ) ];
	if ( $p ) { $datos['ID'] = $p->ID; }
	$id = wp_insert_post( wp_slash( $datos ) );
	WP_CLI::log( ( $p ? '↻ ' : '+ ' ) . $titulo );
}
// La política de privacidad de WordPress apunta a la nuestra; la de ejemplo en inglés, fuera
if ( $priv = get_page_by_path( 'politica-de-privacidad' ) ) {
	update_option( 'wp_page_for_privacy_policy', $priv->ID );
	if ( $vieja = get_page_by_path( 'privacy-policy' ) ) { wp_delete_post( $vieja->ID, true ); }
}
// Las condiciones las acepta el cliente al pagar
if ( $cond = get_page_by_path( 'condiciones-de-venta' ) ) { update_option( 'woocommerce_terms_page_id', $cond->ID ); }
if ( $dev = get_page_by_path( 'envios-y-devoluciones' ) ) {
	update_option( 'woocommerce_refund_returns_page_id', $dev->ID );
	if ( $vieja = get_page_by_path( 'reembolso_devoluciones' ) ) { wp_delete_post( $vieja->ID, true ); }
}
WP_CLI::success( 'Páginas al día' );
