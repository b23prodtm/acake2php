<?php

App::uses('PagesController', 'Controller');
App::uses('Home', 'View');

class PagesControllerTest extends ControllerTestCase {

        public function setUp() {
                parent::setUp();
                $PagesController = new PagesController();
                $HomeView = new View($PagesController);
        }

        public function testHomePageContents() {
                $result = $this->testAction('/', array('method' => 'get', 'return' => 'contents')
                );
                /** Look AT app/View/Pages/home.ctp */ 
                if (filter_input(INPUT_SERVER, "SERVER_NAME") !== 'localhost') {
                        $this->assertContains('the rapid development', $result);
                } else {
                        $this->assertContains('Release Notes for CakePHP&copy; ', $result);
                }
        }

}
