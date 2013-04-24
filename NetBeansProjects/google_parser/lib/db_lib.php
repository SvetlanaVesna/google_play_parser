<?

class Db_lib {

	private $link = NULL;
	public $log = array();
	private $debug = true;
	private $connectParams = Array();
	private $res;

	/**
	 * 	@todo добавить проверку на продакшен/локал и автоматическое присваивание debug
	 */
	function __construct($app) {
		$sqlconfig = $app->config->database;

		if (isset($sqlconfig->debug))
			$this->debug = $sqlconfig->debug;
		$this->connectParams = $sqlconfig->mysql;
		$this->connect();
	}

	/**
	 * 	@todo добавить распаралеливание по серверам
	 */
	function connect() {
		$this->link = mysql_connect($this->connectParams->host, $this->connectParams->user, $this->connectParams->password) or die(mysql_error());
		mysql_select_db($this->connectParams->database, $this->link) or die(mysql_error());
		mysql_query('SET NAMES UTF8', $this->link);
		return $this->link;
	}

	function disconnect() {
		mysql_close($this->link);
	}

	/**
	 * 	execute sql query
	 * 	@param string $qstring sql query string
	 * 	@return mysql_result
	 */
	function query($qstring) {
		if ($this->debug)
			list($sm, $ss) = explode(' ', microtime());

		$res = mysql_query($qstring, $this->link) or die($this->log_error($qstring));

		if ($this->debug) {
			list($em, $es) = explode(' ', microtime());
			$this->log[] = (object) array(
						'query' => $qstring,
						'time' => ($em + $es) - ($sm + $ss),
			);
		}

		$this->res = $res;
		return $this;
	}

	function query_res($qstring) {
		$this->query($qstring);
		return $this->_result($this->res);
	}

	function result() {
		return $this->_result($this->res);
	}

	function first_row() {
		return mysql_num_rows($this->res) ? mysql_fetch_object($this->res) : false;
	}

	private function _result($res) {
		$tmp = array();
		while ($x = mysql_fetch_object($res)) {
			$tmp[] = $x;
		}
		return $tmp;
	}

	/**
	 * 	last insert id
	 * 	@return int
	 */
	function insert_id() {
		return mysql_insert_id();
	}

	/**
	 * 	last error
	 * 	@param string $qstring sql query string
	 * 	@return string html formatted
	 */
	private function log_error($qstring) {
		return '<pre style="border:1px solid red">' . mysql_errno() . ': ' . mysql_error() . '<br>' . $qstring . '</pre>';
	}

	/**
	 * 	print sql log
	 * 	@param double $bad_time max sql query time
	 * 	@return false
	 */
	function print_log($bad_time = 0.1) {
		$total_time = 0;
		echo '<table style="font-face:courier new; font-size:11px">';
		foreach ($this->log as $item) {
			echo '<tr><td>', $item->query, '</td><td>', (($item->time >= $bad_time) ? '<b style="color:red">' . $item->time . '</b>' : $item->time), '</td></tr>';
			$total_time += $item->time;
		}
		echo '<tr><td><b>Total: ', count($this->log), ' queries</b></td><td><b>' . $total_time . '</b></td></tr>';
		echo '<table>';
		return false;
	}

	function insert($table, $data) {
		if (count($data)) {
			$columns = "";
			$values = "";
			foreach ($data as $column => $value) {
				$columns .= '`' . mysql_real_escape_string($column) . '`,';
				$values .= '"' . mysql_real_escape_string($value) . '",';
			}

			$this->query('
                REPLACE INTO
                    `' . mysql_real_escape_string($table) . '`
                    (' . substr($columns, 0, -1) . ')
                VALUES
                    (' . substr($values, 0, -1) . ')
            ');
			return $this->insert_id();
		}
		return false;
	}

	function update($table, $data, $where) {
		if (count($data) && trim($where) !== "") {
			$set = "";
			foreach ($data as $column => $value) {
				$set .= '`' . mysql_real_escape_string($column) . '` = "' . mysql_real_escape_string($value) . '",';
			}
			$this->query('
                UPDATE
                    `' . mysql_real_escape_string($table) . '`
                SET
                    ' . substr($set, 0, -1) . '
                WHERE ' . $where);
			return true;
		}
		return false;
	}

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	/**
	 * Получение количества рядов в результате 
	 * при использовании SQL_CALC_FOUND_ROWS без учета LIMIT
	 * @return type 
	 */
	function sql_calc_found_rows() {
		$result = $this->query('SELECT FOUND_ROWS() AS count')->first_row();
		return $result->count;
	}

}

?>