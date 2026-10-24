<?php
/**
 * Title: Nuestra historia (imagen y texto)
 * Slug: tostela/historia
 * Categories: about
 */
$img = esc_url( get_theme_file_uri( 'assets/img/tostador.jpg' ) );
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"muted","layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull has-muted-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:media-text {"align":"wide","mediaPosition":"left","mediaType":"image","mediaWidth":52,"verticalAlignment":"center","imageFill":false,"className":"historia"} -->
<div class="wp-block-media-text alignwide is-stacked-on-mobile is-vertically-aligned-center historia" style="grid-template-columns:52% auto"><figure class="wp-block-media-text__media"><img src="<?php echo $img; ?>" alt="El tostador, con café recién tostado enfriándose"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"className":"etiqueta","textColor":"accent","fontSize":"x-small"} -->
<p class="etiqueta has-accent-color has-text-color has-x-small-font-size">Nuestra historia</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Tostar poco, tostar bien</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"contrast-2"} -->
<p class="has-contrast-2-color has-text-color">Tostamos en lotes de doce kilos y probamos cada uno antes de embolsarlo. Buscamos el punto en el que cada origen enseña lo suyo: la fruta de un Kenia, el caramelo de un Colombia, el chocolate de un Brasil.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"contrast-2"} -->
<p class="has-contrast-2-color has-text-color">La fecha de tueste va impresa en cada bolsa. Si no lo podemos contar, no lo vendemos.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/nuestra-historia/">Conócenos</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:group -->
