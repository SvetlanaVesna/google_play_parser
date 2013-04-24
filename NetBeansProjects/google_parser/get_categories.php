<?php

require 'core/init.php';
$url = isset($_POST['url']) ? $_POST['url'] : '';
$res = $app->category_model->get_category($url);
if ($res) {
	$app->vars->categories = $res;
	$app->display('categories');
}
?>
