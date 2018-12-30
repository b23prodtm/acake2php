<?php
App::uses('Motdepasse', 'Model');

/**
 * Motdepasse Test Case
 */
class MotdepasseTest extends CakeTestCase {

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
		$this->Motdepasse = ClassRegistry::init('Motdepasse');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Motdepasse);

		parent::tearDown();
	}

}
