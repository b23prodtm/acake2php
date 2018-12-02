<?php
App::uses('Postgres', 'Model/Datasource/Database');

class Postgres_cms extends Postgres
{
	public function __construct()
	{
		parent::__construct();
		$this->columns['mediumbinary'] = array('name' => 'bytea');
	}
}
?>
