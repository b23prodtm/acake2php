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

        var $data = array('local' => '1'
                        );
        /**
         * testIndex method
         *
         * @return void
         **/
        public function testIndex() {
                $result = $this->_testAction('/', array('method' => 'get', 'return' => 'view', 'data' => $this->data)
                );
                $this->assertContains('mailto:', $result);
        }
        /**
         * testIndex method
         *
         * @return void
         */
        public function testAdmin_index() {
                $result = $this->_testAction('/admin/e14/index', array('method' => 'get', 'return' => 'view', 'data' => $this->data)
                );
                $this->assertContains('log_off.php', $result);
        }
        /**
         * testIndex method
         *
         * @return void
         *
        public function testBlog() {
                $result = $this->_testAction('/e14/blog/', array('method' => 'get', 'return' => 'contents', "'url'" => array("local" => 1)));
                $this->assertContains('Redirection', $this->view);
        }CONTAINS REDIRECTION CANNOT TEST RETURN*/
        /**
         * testIndex method
         *
         * @return void
         */
        public function testInfos() {
                $result = $this->_testAction('/e14/infos/1/10', array('method' => 'get', 'return' => 'view', 'data' => $this->data)
                );
                $this->assertContains('Testing Markdown', $result);
        }
        
        /**
         * testIndex method
         *
         * @return void
         */
        public function testDvd() {
                $result = $this->_testAction('/e14/dvd/', array('method' => 'get', 'return' => 'view', 'data' => $this->data)
                );
                $this->assertContains('< --', $result);
        }

}
