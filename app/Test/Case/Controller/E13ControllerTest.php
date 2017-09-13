<?php

App::uses('E13Controller', 'Controller');

/**
 * E13Controller Test Case
 */
class E13ControllerTest extends ControllerTestCase {

        public function setUp() {
                parent::setUp();
                $E13Controller = new E13Controller();
                $HomeView = new View($E13Controller);
        }

        /**
         * testIndex method
         *
         * @return void
         */
        public function testIndex() {
                $result = $this->testAction('/e13', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                if (filter_input(INPUT_SERVER, "SERVER_NAME") !== 'localhost') {
                        $this->assertContains('&copy; ', $result);
                } else {
                        $this->assertContains('Release Notes for CakePHP&copy; ', $result);
                }
        }

}
