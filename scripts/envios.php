<?php
// Zonas de envío de la tienda (se puede repetir: si ya existen, no hace nada).
// Península: todas las provincias menos Baleares, Canarias, Ceuta y Melilla.
// Canarias, Ceuta y Melilla no llevan IVA (IGIC / IPSI): para no complicar, no se envía allí.
// Los importes van sin IVA: 4,4545 + 10 % = 4,90 € con café (con solo accesorios, 21 %: 5,39 €).
$zonas = WC_Shipping_Zones::get_zones();
foreach ($zonas as $z) { if (in_array($z['zone_name'], ['Península', 'Baleares'], true)) { WP_CLI::log('Las zonas ya existen'); return; } }

$fuera = ['PM', 'GC', 'TF', 'CE', 'ML'];
$pen = new WC_Shipping_Zone();
$pen->set_zone_name('Península');
$pen->set_zone_order(1);
foreach (array_keys(WC()->countries->get_states('ES')) as $prov) {
    if (!in_array($prov, $fuera, true)) { $pen->add_location("ES:$prov", 'state'); }
}
$pen->save();
$id = $pen->add_shipping_method('flat_rate');
update_option("woocommerce_flat_rate_{$id}_settings", ['title' => 'Envío 24/48 h', 'cost' => '4.4545', 'tax_status' => 'taxable']);
$id = $pen->add_shipping_method('free_shipping');
update_option("woocommerce_free_shipping_{$id}_settings", ['title' => 'Envío gratis', 'requires' => 'min_amount', 'min_amount' => '35', 'ignore_discounts' => 'no']);

$bal = new WC_Shipping_Zone();
$bal->set_zone_name('Baleares');
$bal->set_zone_order(2);
$bal->add_location('ES:PM', 'state');
$bal->save();
$id = $bal->add_shipping_method('flat_rate');
update_option("woocommerce_flat_rate_{$id}_settings", ['title' => 'Envío a Baleares', 'cost' => '7.1818', 'tax_status' => 'taxable']);

WP_CLI::success('Península: 4,90 € (gratis desde 35 €) · Baleares: 7,90 €');
