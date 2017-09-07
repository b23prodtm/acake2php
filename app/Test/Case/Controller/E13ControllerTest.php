<?php
App::uses('E13Controller', 'Controller');

/**
 * E13Controller Test Case
 */
class E13ControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.e13'
	);

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
                $this->testAction('/', array('method' => 'get', 'return' => 'contents')
                );
                $this->assertContains('&copy; ', $result);       
	}

}
