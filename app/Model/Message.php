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
  public $belongsTo = array(
     'Client' => array(
         'foreignKey' => 'id_client'
     )
 );
  public $validate = array(
     'titre' => array(
         'rule2' => array(
           'rule' => 'notags',
           'message' => 'No HTML tags',
           'last' => true),
      'Requis' => array('rule' => 'notBlank')
      ),
     'texte' => array(
        'rule3' => array(
           'rule' => 'notags',
           'message'=>'No HTML tags',
           'last' => true),
       'Requis' => array('rule' => 'notBlank')
      ),
     'id_client' => array(
        'rule' => 'alphaNumericDashUnderscore',
        'message' => "Only alphabetic or numerical characters or dash or underscores."
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

    public function isOwnedBy($message, $client) {
        return $this->field('id', array('id' => $message, 'id_client' => $client)) !== false;
    }
}
