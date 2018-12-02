<?php
App::uses('Mysql', 'Model/Datasource/Database');

class MysqlCms extends Mysql
{
	public function __construct($config = null, $autoConnect = true) {
		parent::__construct($config, $autoConnect);
		$this->columns['mediumbinary'] = array('name' => 'mediumblob');
	}

	/**
	 * Converts database-layer column types to basic types
	 *
	 * @param string $real Real database-layer column type (i.e. "varchar(255)")
	 * @return string Abstract column type (i.e. "string")
	 */
		public function column($real) {
			$s = parent::column($real);
			if($s === "text") {
				$col = str_replace(')', '', $real);
				$limit = $this->length($real);
				if (strpos($col, '(') !== false) {
					list($col, $vals) = explode('(', $col);
				}
				if (strpos($col, 'mediumblob') !== false || $col === 'mediumbinary') {
					return 'mediumbinary';
				}
			}
			return $s;
		}
}
?>
