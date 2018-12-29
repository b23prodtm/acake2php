<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */
App::uses('AppModel', 'Model');

class Client extends AppModel {
    public $name = 'Client';
    public $validate = array(
        'identifiant' => array(
            'required' => array(
                'rule' => 'alphaNumericDashUnderscore',
                'message' => "Un nom d'utilisateur est requis",
                'allowEmpty' => false
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'visiteur')),
                'message' => "Merci d'entrer un rôle valide",
                'allowEmpty' => false
            )
        )
    );
    public function alphaNumericDashUnderscore($check) {
            $valeur = array_values($check);
            return !preg_match('/^[0-9a-zA-Z_-\@\.]*$/', $valeur[0]);
    }
}
?>
