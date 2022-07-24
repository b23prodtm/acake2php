<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('CakeEmail', 'Network/Email');

class EmailConfig {
  public $default = array(
		'transport' => 'Mail',
		'from' => 'webmaster@localhost',
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);
  public $gmail = array(
        'host' => 'smtp.gmail.com',
        'port' => 465,
        'username' => 'my@gmail.com',
        'password' => 'secret',
        'transport' => 'Smtp',
        'tls' => true
    );
    public function __construct() {
      $this->default['from'] = getenv('SERVER_NAME') ? 'no-reply@' . getenv('SERVER_NAME') : $this->default['from'];
    }
}
 ?>
