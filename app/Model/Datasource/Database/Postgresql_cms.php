<?php
App::uses('Postgresql', 'Model/Datasource/Database');

class Postgresql_cms extends Postgresql
{
	public function __construct()
	{
		parent::__construct();
		$this->columns['mediumbinary'] = array('name' => 'bytea');
	}
}
?>
