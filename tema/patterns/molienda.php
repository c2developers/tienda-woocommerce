<?php
/**
 * Title: Guía de molienda
 * Slug: tostela/molienda
 * Categories: text
 */
$metodos = [
	[ 'Espresso', 'Fina', 'Para cafetera de espresso. 18 g de café, unos 36 g en taza en 25-30 segundos.' ],
	[ 'Filtro', 'Media', 'V60, Chemex o goteo. 15 g por cada 250 ml de agua a 93 °C.' ],
	[ 'Prensa francesa', 'Gruesa', '4 minutos de infusión y a la taza. Cuerpo redondo, nada de posos.' ],
	[ 'En grano', 'Tú decides', 'Lo mejor si tienes molinillo: el café molido pierde aroma en días.' ],
];
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"0.6rem","margin":{"bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"640px","justifyContent":"left"}} -->
<div class="wp-block-group alignwide" style="margin-bottom:var(--wp--preset--spacing--50)"><!-- wp:paragraph {"className":"etiqueta","textColor":"accent","fontSize":"x-small"} -->
<p class="etiqueta has-accent-color has-text-color has-x-small-font-size">Guía rápida</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">¿Cómo lo vas a preparar?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"contrast-2"} -->
<p class="has-contrast-2-color has-text-color">Elige la molienda en cada café y te lo enviamos listo para tu cafetera.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"metodos","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide metodos"><?php foreach ( $metodos as $m ) : ?><!-- wp:column {"className":"metodo","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"18px","color":"var:preset|color|line","width":"1px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-column metodo has-border-color has-base-2-background-color has-background" style="border-color:var(--wp--preset--color--line);border-width:1px;border-radius:18px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"className":"etiqueta","textColor":"accent-2","fontSize":"x-small"} -->
<p class="etiqueta has-accent-2-color has-text-color has-x-small-font-size">Molienda <?php echo esc_html( mb_strtolower( $m[1] ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size"><?php echo esc_html( $m[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"contrast-2","fontSize":"small"} -->
<p class="has-contrast-2-color has-text-color has-small-font-size"><?php echo esc_html( $m[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --><?php endforeach; ?></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
