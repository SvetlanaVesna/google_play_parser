<?php

class Product_model {

	private $app; // Приложение
	private $db; // Работа с базой данных

	/**
	 * Конструктор
	 * @param object $app Ссылка на Application
	 */

	function __construct($app) {
		$this->db = $app->db;
		$this->app = $app;
	}

	public function push_products($category_id, $url) {
		$data = array();
		$html = file_get_html($url);
		$li = $html->find("li.goog-inline-block");
		foreach ($li as $item) {
			$item = (object) $item;
			$data['image'] = $item->children(0)->children(0)->children(0)->children(0)->children(0)->src; //correct
			$data['title'] = $item->children(0)->children(1)->children(0)->children(0)->plaintext; //correct
			$href = $item->children(0)->children(1)->children(0)->children(0)->href; //correct
			$data['description'] = $item->children(0)->children(1)->children(2)->plaintext; //correct
			$id = $item->children(0)->children(1)->children(3)->children(0)->children(0)->children(0)->children(1)->id;
			if (preg_match('/bubble-(.*)-offer-1/', $id, $m)) {
				$data['id'] = $m[1];
			}

			$parse = parse_url($url);
			$data['url'] = $parse['scheme'] . "://" . $parse['host'] . $href;
			$this->db->insert('product', $data);
			$this->db->query('replace into xref_product_category (product_id,category_id) values ("' .
					mysql_real_escape_string($data['id']) . '","' . mysql_real_escape_string($category_id) . '")');
		}
	}

	public function get_products($category_id) {
		return $this->db->query_res('select product.* from xref_product_category
join product on product.id=xref_product_category.product_id
join category on category.id = xref_product_category.category_id
where xref_product_category.category_id="' . mysql_real_escape_string($category_id).'"');
	}

}

?>
