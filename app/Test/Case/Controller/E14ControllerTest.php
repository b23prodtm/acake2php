<?php

App::uses('E14Controller', 'Controller');

/**
 * E13Controller Test Case
 */
class E14ControllerTest extends ControllerTestCase {

        public function setUp() {
                parent::setUp();
                $E14Controller = new E14Controller();
                $HomeView = new View($E14Controller);
        }

        /**
         * testIndex method
         *
         * @return void
         */
        public function testIndex() {
                $result = $this->testAction('/e14/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('webmaster@b23prodtm.info', $result);
        }
        /**
         * testIndex method
         *
         * @return void
         */
        public function testAdmin() {
                $result = $this->testAction('/e13/admin/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('log_off.php', $result);
        }
        /**
         * testIndex method
         *
         * @return void
         */
        public function testBlog() {
                $result = $this->testAction('/e13/blog/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('Redirection', $result);
        }
        /**
         * testIndex method
         *
         * @return void
         */
        public function testShop() {
                $result = $this->testAction('/e13/shop/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('<p>&nbsp;</p>', $result);
        }
        
        /**
         * testIndex method
         *
         * @return void
         */
        public function testDvd() {
                $result = $this->testAction('/e13/dvd/bible.php', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('info_titre', $result);
        }

}
