<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */
App::uses('AppModel', 'Model');

class Client extends AppModel {
    public $validationDomain = 'formulaire';
    public $name = 'Client';
    public $belongsTo = array(
       'MotDePasse' => array(
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
        'nom' => array(
            'required' => array(
                'rule' => 'alphaDash',
                'message' => "Un nom de famille est requis",
                'allowEmpty' => false
            )
        ),
        'prenom' => array(
            'required' => array(
                'rule' => 'alphaDash',
                'message' => "Un prénom est requis",
                'allowEmpty' => false
            )
        ),
        'ville' => array(
            'required' => array(
                'rule' => 'alphaDash',
                'message' => "Un nom de ville est requis",
                'allowEmpty' => false
            )
        ),
        'codepostal' => array(
            'required' => array(
                'rule' => array('postal', '/^[0-9]+$/', 'eu'),
                'message' => "Un code postal est requis",
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
    public function alphaDash($check) {
            $valeur = array_values($check);
            return !preg_match('/^[a-zA-Z-\',\s]*$/', $valeur[0]);
    }

    public function isOwnedBy($client, $motdepasse) {
        return $this->field('identifiant', array('identifiant' => $client, 'fk_motdepasse' => $motdepasse)) !== false;
    }
}
?>
