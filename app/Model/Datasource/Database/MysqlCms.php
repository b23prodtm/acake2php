<?php
App::uses('MysqlLog', 'Datasources.Model/Datasource/Database');

class MysqlCms extends MysqlLog
{
	/**
	 * Datasource Description
	 *
	 * @var string
	 */
	public $description = 'MariaDB/MySQL Logging DBO Driver';

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

		/**
		 * Connects to the database using options in the given configuration array.
		 *
		 * MySQL supports a few additional options that other drivers do not:
		 *
		 * - `unix_socket` Set to the path of the MySQL sock file. Can be used in place
		 *   of host + port.
		 * - `ssl_key` SSL key file for connecting via SSL. Must be combined with `ssl_cert`.
		 * - `ssl_cert` The SSL certificate to use when connecting via SSL. Must be
		 *   combined with `ssl_key`.
		 * - `ssl_ca` The certificate authority for SSL connections.
		 *
		 * @return bool True if the database could be connected, else false
		 * @throws MissingConnectionException
		 */
			public function connect() {
				if (Configure::read('debug') > 2) {
					debug($this->config);
				}
				try {
						parent::connect();
				} catch(MissingConnectionException $e) {
						if (getenv('TRAVIS_BUILD_NUMBER')) {
							$this->showError(getenv('MYPHPCMS_LOG').'/migrate-getenv'.('TRAVIS_BUILD_NUMBER').'.log');
						}
						$this->showError($e->getAttributes());
						throw $e;
				}
			}
			/**
			 * Shows an error message and outputs the MYSQL result if CAKEPHP_DEBUG_LEVEL > 0
			 *
			 * @param string $result A MYSQL result
			 */
				public function showError($error = null) {
					if (Configure::read('debug') > 0) {
							trigger_error('<span style = "color:Red;text-align:left"><b>MYSQL Error:</b> '
							. print_r($error, TRUE) . '</span>', E_USER_WARNING);
					}
				}
}
?>
