<?php

require 'core/init.php';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
if (count($_POST)) {
$start = isset($_POST['start']) ? $_POST['start'] : 0;

	$url = 'https://play.google.com/store/apps/category/' . $category_id . '/collection/topselling_free?start=' . $start . '&num=24';
	$app->product_model->push_products($category_id, $url);
}
$res = $app->product_model->get_products($category_id);
if ($res) {
	$app->vars->products = $res;
	$app->display('products');
}
?>
