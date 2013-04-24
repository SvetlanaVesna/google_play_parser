<?php

header('Content-Type:text/html; charset=UTF-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: private, no-cache');
header('Pragma: no-cache');
date_default_timezone_set('Europe/Moscow');

require_once 'config/config.php';
//libs
require_once 'lib/db_lib.php';
require_once 'lib/simple_html_dom.php';

//models
require_once 'models/category_model.php';

class Application {

	public $vars;
	public $db, $db_lib;
	public $category_model;

	public function __construct($config) {
		$this->config = $config;
		$this->db = new Db_lib($this);
		$this->category_model = new Category_model($this);
	}

	/**
	 * Редирект
	 * @param string $url
	 */
	function redirect($url = '') {
		$url = '/admin' . $url;
		header('Location: ' . $url);
		die();
	}

	/**
	 * Подключает шаблон
	 * @param string $template
	 * @param string $ext
	 */
	function display($template, $ext = '.php') {
		require ('views/' . $template . $ext);
	}

	/**
	 * Подключает полный шаблон сайта
	 * @param type $template
	 */
	function display_page($template) {
		$this->template = $template;
		$this->display('main_template');
	}

}

$app = new Application($config);