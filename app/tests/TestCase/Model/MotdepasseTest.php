<?php
namespace Test\TestCase\Model;

App::uses('MotDePasse', 'Model');

/**
 * MotDePasse Test Case
 */
class MotDePasseTest extends TestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.motdepasses'
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
