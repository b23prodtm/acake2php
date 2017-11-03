<?php

App::uses('E14Controller', 'Controller');

/**
 * E13Controller Test Case
 */
class E14ControllerTest extends ControllerTestCase {

        var $controller;
        var $view;

        public function setUp() {
                parent::setUp();
                $this->controller = new E14Controller();
                $this->view = new View($this->controller);
        }

        var $data = array(
            'debug' => 1,
            'local' => 1
        );

        /**
         * testIndex method
         *
         * @return void
         * */
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
         */
          public function testRoute() {
                $result = $this->_testAction('/index/_image.php', array('method' => 'get', 'return' => 'contents', "'url'" => array(
                "local" => 1,
                "captcha" => 1,
                "debug" => 0)));
                $this->assertStringMatchesFormatFile('/img/test__image.php', $result);
          }

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

        public function testImage() {
                $this->setUp();
                $d = $this->controller->r->tempdir();
                $this->assertFileIsWritable($d, "Temp folder doesn't exist or isn't writeable.");
        }

}
