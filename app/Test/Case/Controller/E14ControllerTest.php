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
                $this->testAction('/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1))
                );
                $this->assertContains('<em>strong title</em>', $this->view);
        }
        /**
         * testIndex method
         *
         * @return void
         */
        public function testAdmin() {
                $result = $this->testAction('/admin/e14/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('log_off.php', $this->view);
        }
        /**
         * testIndex method
         *
         * @return void
         *
        public function testBlog() {
                $result = $this->testAction('/e14/blog/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('Redirection', $this->view);
        }*/
        /**
         * testIndex method
         *
         * @return void
         */
        public function testShop() {
                $result = $this->testAction('/e14/infos/1/10', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('Testing Markdown', $this->view);
        }
        
        /**
         * testIndex method
         *
         * @return void
         */
        public function testDvd() {
                $result = $this->testAction('/e14/dvd/', array('method' => 'get', 'return' => 'contents', "named" => array("local" => 1)));
                $this->assertContains('< --', $this->view);
        }

}
