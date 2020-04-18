<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');

App::uses('CakeEmail', 'Network/Email');

/**
 * CakePHP EmailsController
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class EmailsController extends AppController {

        var $helpers = array('html');

        public function index($id) {
                return $this->redirect("send");
        }

        public function send() {
                if (empty($this->request->data) || $this->request->params['requested']) {
                        $post_email = $this->request->data('Email');
                        if (!$post_email) {
                                $post_email = array(
                                    'email' => '',
                                    'subject' => 'Message from : ' . filter_input(INPUT_SERVER, 'SCRIPT_NAME'),
                                    'message' => date('D M Y at H:i:s')
                                );
                        };
                        $this->set($post_email);
                        /** continues to render send.ctp ... */
                        if ($this->request->params['requested']) {
                                return $this->render()->body();
                        }
                } else {
                        if ($this->request->params['requested']) {
                                trigger_error("Illegal request");
                        } else {
                                $post_email = $this->request->data('Email');
                                debug($post_email);
                                $this->set($post_email);
                                $email = new CakeEmail();
                                $email->emailFormat('html')
                                        ->viewVars(array('message' => $post_email['message']))
                                        ->config(array('from' => 'no-reply@' . filter_input(INPUT_SERVER, 'SERVER_NAME'), 'to' => $post_email['email']))
                                        ->subject($post_email['subject'])
                                        ->send();
                                echo "<p>A mail was sent to " . $post_email['email'] . '.</p>';
                        }
                }
        }

}
