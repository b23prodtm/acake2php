<?php
App::uses('Mysql', 'Model/Datasource/Database');

class MyBinsql extends Mysql
{
	public function __construct($config = null, $autoConnect = true) 
	{
		$this->columns['mediumbinary'] = array('name' => 'mediumblob');
		parent::__construct($config, $autoConnect);
	}
}
?>