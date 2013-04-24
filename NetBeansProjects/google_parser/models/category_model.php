<?php

class Category_model {

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

	public function get_category($url) {
		$data = array();
		$html = file_get_html($url);
		$a = $html->find(".category-item ");
		foreach ($a as $item) {
			$item = (object) $item->children(0);
			if (preg_match('/category\/(.*)\?/', $item->href, $m)) {
				$data['id'] = $m[1];
			} else {
				$data['id'] = '';
			}
			$parse = parse_url($url);
			$data['url'] = $parse['scheme']."://".$parse['host'] . $item->href;
			$data['title'] = $item->plaintext;
			$this->db->insert('category', $data);
		}
		$res = $this->db->query('Select * from category')->result();
		return $res;
	}

}

?>
