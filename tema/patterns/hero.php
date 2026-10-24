<?php
/**
 * Title: Portada con foto
 * Slug: tostela/hero
 * Categories: banner
 */
$img = esc_url( get_theme_file_uri( 'assets/img/hero.jpg' ) );
?>
<!-- wp:cover {"url":"<?php echo $img; ?>","dimRatio":50,"overlayColor":"contrast","isUserOverlayColor":true,"minHeight":86,"minHeightUnit":"vh","contentPosition":"bottom left","isDark":true,"align":"full","className":"hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-bottom-left is-dark hero" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:86vh"><img class="wp-block-cover__image-background" alt="" src="<?php echo $img; ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"1.4rem"}},"layout":{"type":"constrained","contentSize":"760px","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"etiqueta","textColor":"base","fontSize":"x-small"} -->
<p class="etiqueta has-base-color has-text-color has-x-small-font-size">Café de especialidad · Tostado en Madrid</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"textColor":"base-2"} -->
<h1 class="wp-block-heading has-base-2-color has-text-color">El café que <em>sabe</em> de dónde viene</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"large"} -->
<p class="has-base-color has-text-color has-large-font-size">Lotes pequeños de fincas con nombre propio, tostados cada martes y en tu casa en 48 horas.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent","textColor":"base-2"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-base-2-color has-accent-background-color has-text-color has-background wp-element-button" href="/categoria-producto/cafes/">Ver los cafés</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline","textColor":"base-2"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-base-2-color has-text-color wp-element-button" href="/nuestra-historia/">Cómo tostamos</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
