<?php
App::uses('MotDePasse', 'Model');

/**
 * MotDePasse Test Case
 */
class MotDePasseTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.motdepasse'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MotDePasse = ClassRegistry::init('MotDePasse');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MotDePasse);

		parent::tearDown();
	}

}
