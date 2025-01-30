<?php
/**
 * Motdepasse Fixture
 */
namespace Test\Fixture;

class MotdepasseFixture extends TestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'password' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'password_confirm' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'cree' => ['type' => 'date', 'null' => false, 'default' => null],
		'modifie' => ['type' => 'date', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
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
			'password_confirm' => 'Lorem ipsum dolor sit amet',
			'cree' => '2018-12-29',
			'modifie' => '2018-12-29'
		),
	);

}
