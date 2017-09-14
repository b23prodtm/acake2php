<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Errors
 * @since         CakePHP(tm) v 2.2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
echo $message = "" .
 "<h2>" . __d('cake_dev', 'XXX Fatal Error XXX') . "</h2>" .
 '<p class="error">' .
 '<strong>' . __d('cake_dev', 'Error') . ': </strong>' .
 '' . h($error->getMessage()) . '' .
 '<br>' .
 '<strong>' . __d('cake_dev', 'File') . '</strong>' .
 '' . h($error->getFile()) . '' .
 '<br>' .
 '<strong>' . __d('cake_dev', 'Line') . '</strong>' .
 '' . h($error->getLine()) . '';
//add this
$post_array = array('message' => $message, 'email' => 'webmaster@' . filter_input(INPUT_SERVER, 'SERVER_NAME'), 'subject' => "Fatal Error : " . $error->getFile());
echo $this->requestAction(array("Controller" => 'Emails', 'action' => 'send', 'Email' => $post_array), array('return' => true));
?>
</p>
