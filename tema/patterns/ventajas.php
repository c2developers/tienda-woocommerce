<?php
/**
 * Title: Tres ventajas
 * Slug: tostela/ventajas
 * Categories: text
 */
$items = [
	[ 'Tostado cada semana', 'Tostamos los martes y enviamos en 24 h: el café te llega con días, no con meses.' ],
	[ 'Origen trazable', 'Cada bolsa dice la finca, la altura, la variedad y el proceso. Sin mezclas misteriosas.' ],
	[ 'Molido a tu manera', 'En grano o molido para espresso, filtro o prensa francesa. Tú eliges en cada café.' ],
];
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"border":{"bottom":{"color":"var:preset|color|line","width":"1px"}}},"backgroundColor":"base-2","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-base-2-background-color has-background" style="border-bottom-color:var(--wp--preset--color--line);border-bottom-width:1px;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide"><?php foreach ( $items as $n => $it ) : ?><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"numero","textColor":"accent","fontSize":"small"} -->
<p class="numero has-accent-color has-text-color has-small-font-size"><?php echo esc_html( sprintf( '0%d', $n + 1 ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size"><?php echo esc_html( $it[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"contrast-2"} -->
<p class="has-contrast-2-color has-text-color"><?php echo esc_html( $it[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --><?php endforeach; ?></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
