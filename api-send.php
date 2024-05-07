<?php

if (htmlspecialchars($_GET["token"])!='setanicetokenforsafety') die();
require_once(dirname(__FILE__).'/config/config.inc.php');
//include('../config/config.inc.php');
//include('../init.php');

$sql = "SELECT p.id_product AS 'item',
ROUND(od.unit_price_tax_incl,2) AS 'price',
o.id_order AS 'order',
CONCAT(REPLACE(o.date_add,' ','T'),'Z') AS 'timestamp',
c.email AS 'email',
SUM(od.product_quantity) AS 'quantity',
'FR' AS 's_market',
ROUND(od.original_product_price*1.2,2) AS 'f_originalPrice',
'EUR' AS 's_originalCurrency',
(SELECT al.name FROM `'._DB_PREFIX_.'attribute` a
LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON a.id_attribute = al.id_attribute
LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.id_attribute = a.id_attribute
WHERE al.id_lang=1 AND a.id_attribute_group=1 AND pac.id_product_attribute=od.product_attribute_id) AS 's_color',
REPLACE(
	COALESCE((SELECT al.name FROM `'._DB_PREFIX_.'attribute` a
LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON a.id_attribute = al.id_attribute
LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.id_attribute = a.id_attribute
WHERE al.id_lang=1 AND a.id_attribute_group=2 AND pac.id_product_attribute=od.product_attribute_id),
		(SELECT al.name FROM `'._DB_PREFIX_.'attribute` a
LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON a.id_attribute = al.id_attribute
LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.id_attribute = a.id_attribute
WHERE al.id_lang=1 AND a.id_attribute_group=4 AND pac.id_product_attribute=od.product_attribute_id)),',','.') AS 's_size'
FROM `'._DB_PREFIX_.'order_detail` od
	LEFT JOIN `'._DB_PREFIX_.'product` p ON od.product_id = p.id_product
	LEFT JOIN `'._DB_PREFIX_.'orders` o ON od.id_order = o.id_order
	LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.id_customer = c.id_customer
WHERE o.date_add 
	BETWEEN 
		DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d %H:00:00') 
		AND 
		DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d 23:59:59')
AND o.current_state NOT IN(1, 3, 6, 7, 8, 10, 12, 13)
AND od.id_shop = '1'
AND p.id_product IS NOT NULL

GROUP BY timestamp, item, o.id_order, email
ORDER BY o.id_order DESC";

$results = Db::getInstance()->executeS($sql);
//var_dump($results); die();

$csv_filename_full = _'._DB_PREFIX_.'ROOT_DIR_ . '/itemsold/sales_items_'.date('Ymd_H').'.csv';
$csv_filename = 'sales_items_'.date('Ymd_H').'.csv';
$fp = fopen($csv_filename, 'w');

$i = 0;
$placed_header = false;
foreach ($results as $result) {
	if(!$placed_header) {
        fputcsv($fp, array_keys($result));
        $placed_header = true;
    }
    fputcsv($fp, $result,',',chr(0));
    $i++;
}
fclose($fp);

if ($i>0) {
	$output = shell_exec('bash curl.sh '.$csv_filename);
	echo "<pre>$output</pre>";
	
/*
	$cfile = new CurlFile($csv_filename, 'text/plain');

	$data = array('data-binary' => $cfile);
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, "https://xxxx");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/csv','Accept: text/plain','Authorization: Bearer Hxxxxxxx-mxxxxxxxxxxQ'));

	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($ch);
	var_dump($result);
	curl_close($ch);
	die();
*/
}
