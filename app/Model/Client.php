<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */
App::uses('AppModel', 'Model');

class Client extends AppModel {
    public $name = 'Client';
    public $belongsTo = array(
       'Motdepasse' => array(
           'foreignKey' => 'fk_motdepasse'
       )
   );
    public $displayField = 'email';
    public $validate = array(
        'identifiant' => array(
            'required' => array(
                'rule' => 'alphaNumericDashUnderscore',
                'message' => "Un nom d'utilisateur est requis",
                'allowEmpty' => false
            ),
            'unique' => array(
              'rule' => 'isUnique',
              'message' => "Le nom existe déjà et n'est pas disponible."
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
