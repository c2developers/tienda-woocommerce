<?php
// Crea el catálogo desde contenido/productos.json: categorías, atributos y productos
// con sus variaciones (molienda × peso) y sus fotos. Si un SKU ya existe, lo salta.
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$d = json_decode( file_get_contents( '/contenido/productos.json' ), true );

// Categorías
$cat = [];
foreach ( $d['categorias'] as $c ) {
	$t = term_exists( $c['slug'], 'product_cat' ) ?: wp_insert_term( $c['nombre'], 'product_cat', [ 'slug' => $c['slug'], 'description' => $c['descripcion'] ] );
	$cat[ $c['slug'] ] = (int) $t['term_id'];
}
// La categoría «Sin categorizar» no pinta nada en una tienda bonita
if ( $sin = get_term_by( 'slug', 'sin-categorizar', 'product_cat' ) ) { update_option( 'default_product_cat', $cat['cafes'] ); wp_delete_term( $sin->term_id, 'product_cat' ); }

// Atributos globales: origen (para filtrar), molienda y peso (para las variaciones)
function atributo( $slug, $nombre, $valores, $archivo = false ) {
	// Con archivo (página /origen/kenia/), el atributo es público y se puede enseñar en las tarjetas.
	$id = wc_attribute_taxonomy_id_by_name( $slug );
	$datos = [ 'name' => $nombre, 'slug' => $slug, 'type' => 'select', 'order_by' => 'menu_order', 'has_archives' => $archivo ];
	$id ? wc_update_attribute( $id, $datos ) : wc_create_attribute( $datos );
	$tax = wc_attribute_taxonomy_name( $slug );
	if ( ! taxonomy_exists( $tax ) ) { register_taxonomy( $tax, 'product', [ 'hierarchical' => false ] ); }
	$ids = [];
	foreach ( $valores as $v ) {
		$t = term_exists( $v, $tax ) ?: wp_insert_term( $v, $tax );
		$ids[] = (int) $t['term_id'];
	}
	return [ $tax, $ids ];
}
function foto( $archivo, $titulo ) {
	$ruta = "/assets/img/$archivo";
	if ( ! file_exists( $ruta ) ) { WP_CLI::warning( "Falta la foto $archivo" ); return 0; }
	$existe = get_posts( [ 'post_type' => 'attachment', 'meta_key' => '_tienda_origen', 'meta_value' => $archivo, 'fields' => 'ids' ] );
	if ( $existe ) { return $existe[0]; }
	$tmp = wp_tempnam( $archivo ); copy( $ruta, $tmp );
	$id = media_handle_sideload( [ 'name' => $archivo, 'tmp_name' => $tmp ], 0, $titulo );
	if ( is_wp_error( $id ) ) { WP_CLI::warning( $id->get_error_message() ); return 0; }
	update_post_meta( $id, '_tienda_origen', $archivo );
	update_post_meta( $id, '_wp_attachment_image_alt', $titulo );
	return $id;
}
function descripcion( $p ) {
	$chips = implode( '', array_map( fn( $n ) => '<span class="nota">' . esc_html( $n ) . '</span>', $p['notas'] ) );
	$filas = '';
	foreach ( $p['ficha'] as $k => $v ) { $filas .= '<tr><th>' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>'; }
	return "<p class=\"notas\">$chips</p>\n<p>" . esc_html( $p['corto'] ) . "</p>\n<table class=\"ficha\"><tbody>$filas</tbody></table>\n<p>Tostado el martes y enviado en 24 h. La fecha de tueste va impresa en la bolsa.</p>";
}

[ $tax_origen ] = atributo( 'origen', 'Origen', array_unique( array_column( $d['cafes'], 'origen' ) ), true );
[ $tax_mol, $mol_ids ] = atributo( 'molienda', 'Molienda', $d['molienda'] );
[ $tax_peso, $peso_ids ] = atributo( 'peso', 'Peso', [ '250 g', '1 kg' ] );

$n = 0;
foreach ( $d['cafes'] as $orden => $p ) {
	if ( wc_get_product_id_by_sku( $p['sku'] ) ) { continue; }
	$prod = new WC_Product_Variable();
	$prod->set_name( $p['nombre'] );
	$prod->set_sku( $p['sku'] );
	$prod->set_short_description( $p['corto'] );
	$prod->set_description( descripcion( $p ) );
	$prod->set_category_ids( [ $cat['cafes'] ] );
	$prod->set_featured( $p['destacado'] );
	$prod->set_tax_class( 'reduced-rate' );
	$prod->set_menu_order( $orden );
	$prod->set_image_id( foto( $p['imagen'], $p['nombre'] ) );

	$a_origen = new WC_Product_Attribute();
	$a_origen->set_id( wc_attribute_taxonomy_id_by_name( 'origen' ) );
	$a_origen->set_name( $tax_origen );
	$a_origen->set_options( [ (int) term_exists( $p['origen'], $tax_origen )['term_id'] ] );
	$a_origen->set_visible( true );
	$a_mol = new WC_Product_Attribute();
	$a_mol->set_id( wc_attribute_taxonomy_id_by_name( 'molienda' ) );
	$a_mol->set_name( $tax_mol ); $a_mol->set_options( $mol_ids ); $a_mol->set_visible( true ); $a_mol->set_variation( true );
	$a_peso = new WC_Product_Attribute();
	$a_peso->set_id( wc_attribute_taxonomy_id_by_name( 'peso' ) );
	$a_peso->set_name( $tax_peso ); $a_peso->set_options( $peso_ids ); $a_peso->set_visible( true ); $a_peso->set_variation( true );
	$prod->set_attributes( [ $a_origen, $a_peso, $a_mol ] );
	$prod->set_default_attributes( [ $tax_peso => '250-g', $tax_mol => 'en-grano' ] );
	$id = $prod->save();

	foreach ( $p['precios'] as $peso => $precio ) {
		foreach ( $d['molienda'] as $m ) {
			$v = new WC_Product_Variation();
			$v->set_parent_id( $id );
			$v->set_attributes( [ $tax_peso => sanitize_title( $peso ), $tax_mol => sanitize_title( $m ) ] );
			$v->set_regular_price( (string) $precio );
			$v->set_tax_class( 'parent' );
			$v->set_weight( $peso === '1 kg' ? '1000' : '250' );
			$v->set_manage_stock( false );
			$v->set_stock_status( 'instock' );
			$v->save();
		}
	}
	WC_Product_Variable::sync( $id );
	$n++;
	WP_CLI::log( sprintf( '☕ %s %s € / %s €  (8 variaciones)', mb_str_pad( $p['nombre'], 26 ), number_format( $p['precios']['250 g'], 2, ',', '.' ), number_format( $p['precios']['1 kg'], 2, ',', '.' ) ) );
}
foreach ( $d['accesorios'] as $orden => $p ) {
	if ( wc_get_product_id_by_sku( $p['sku'] ) ) { continue; }
	$prod = new WC_Product_Simple();
	$prod->set_name( $p['nombre'] );
	$prod->set_sku( $p['sku'] );
	$prod->set_regular_price( (string) $p['precio'] );
	$prod->set_short_description( $p['corto'] );
	$prod->set_description( '<p>' . esc_html( $p['corto'] ) . '</p>' );
	$prod->set_category_ids( [ $cat['accesorios'] ] );
	$prod->set_menu_order( 100 + $orden );
	$prod->set_image_id( foto( $p['imagen'], $p['nombre'] ) );
	$prod->save();
	$n++;
	WP_CLI::log( sprintf( '🫖 %s %s €', mb_str_pad( $p['nombre'], 26 ), number_format( $p['precio'], 2, ',', '.' ) ) );
}
WP_CLI::success( "$n productos nuevos" );
