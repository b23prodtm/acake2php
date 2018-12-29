<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */
App::uses('AppModel', 'Model');

class Motdepasse extends AppModel {
    public $name = 'Motdepasse';
    public $validate = array(
        'password' => array(
            'required' => array(
                'rule' => 'alphaNumericDashUnderscore',
                'message' => 'Un mot de passe est requis',
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
