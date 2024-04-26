<?php
include(dirname(__FILE__).'/config/config.inc.php');
require_once(dirname(__FILE__).'/init.php');

$p=Product::getProducts(1, 0, 0, 'id_product', 'desc', false); // the first number is language id 
$products=Product::getProductsProperties(1, $p); // the first number is language id
$baseurl = Context::getContext()->shop->getBaseURL();


header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="utf-8"?>

<catalog>';
foreach ($products as $row) {
if ($row['active']){

$link = new Link;
$imagePath = $link->getImageLink($row['link_rewrite'], $row['cover_image_id'], 'home_default');

echo '
<products>
    <code>'.str_replace("&", "&amp;", $row['id_product']).'</code>
    <name>'.str_replace("&", "&amp;", $row['name']).'</name>
    <description>'.str_replace("&", "and", strip_tags($row['description_short'])).'</description>
    <price>'.($row['price']*1).'</price>
    <quantity>'.($row['quantity'] > 0 ? 'Available' : 'Out of Stock').'</quantity> 
    <link>'.$row['link'].'</link>
    <image>'.$imagePath.'</image>
    <reference>'.$row['reference'].'</reference>
    <manufacturer_name>'.$row['manufacturer_name'].'</manufacturer_name>
    <category>'.str_replace("&", "&amp;", $row['category_name']).'</category>
</products>';
}
}
echo '</catalog>';
?> 
