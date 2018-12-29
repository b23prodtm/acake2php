<?php

/*
 * @copyrights www.b23prodtm.info - 2018 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppModel', 'Model');

/**
 * CakePHP Article
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class Message extends AppModel {
  public $validationDomain = 'formulaire';
  public $validate = array(
     'titre' => array(
         'rule2' => array(
           'rule' => 'notags',
           'message' => 'Ne peut pas contenir de tags HTML.',
           'last' => true),
      'Requis' => array('rule' => 'notBlank')
      ),
     'texte' => array(
        'rule3' => array(
           'rule' => 'notags',
           'message'=>'Ne peut pas contenir de tags HTML tags.',
           'last' => true),
       'Requis' => array('rule' => 'notBlank')
      ),
     'fk_identifiant' => array(
        'rule' => 'alphaNumericDashUnderscore',
        'message' => "L'identifiant ne peut contenir que des lettres, des nombres, des tirets ou des underscores."
        )
    );

    public function alphaNumericDashUnderscore($check) {
        $valeur = array_values($check);
        return !preg_match('/^[0-9a-zA-Z_-\@\.]*$/', $valeur[0]);
    }

    public function notags($check) {
        $texte = array_values($check);
        return !preg_match('/<[^>]+>?(.*)/', $texte[0]);
    }
}
