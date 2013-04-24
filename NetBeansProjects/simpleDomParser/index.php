<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
		<?php
		include_once('simple_html_dom.php');
		$html = file_get_html('https://play.google.com/store/apps');
		$li = $html->find(".category-item ");
		echo '<pre>';
		print_r($li);
		?>
    </body>
</html>
