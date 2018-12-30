<?php
/**
 * Motdepasse Fixture
 */
class MotdepasseFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cree' => array('type' => 'date', 'null' => false, 'default' => null),
		'modifie' => array('type' => 'date', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id_mdp', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'password' => 'Lorem ipsum dolor sit amet',
			'cree' => '2018-12-29',
			'modifie' => '2018-12-29'
		),
	);

}
