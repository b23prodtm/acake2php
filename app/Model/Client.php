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
       'motdepasse' => array(
           'foreignKey' => 'id_motdepasse'
       )
   );
    public $displayField = 'email';
    public $validate = array(
        'id' => array(
            'required' => array(
                'rule' => 'alphaNumericDashUnderscore',
                'message' => 'Username required',
                'allowEmpty' => false
            ),
            'unique' => array(
              'rule' => 'isUnique',
              'message' => 'Name already exists'
            )
        ),
        'nom' => array(
            'required' => array(
                'rule' => 'alphaDash',
                'message' => 'Last name required',
                'allowEmpty' => false
            )
        ),
        'prenom' => array(
            'required' => array(
                'rule' => 'alphaDash',
                'message' => 'First name required',
                'allowEmpty' => false
            )
        ),
        'ville' => array(
            'required' => array(
                'rule' => 'alphaDash',
                'message' => 'City required',
                'allowEmpty' => false
            )
        ),
        'codepostal' => array(
            'required' => array(
                'rule' => array('postal', '/^[0-9]+$/', 'eu'),
                'message' => 'City code required',
                'allowEmpty' => false
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'visiteur')),
                'message' => 'Please choose a valid role',
                'allowEmpty' => false
            )
        ),
        'telephone' => array(
            'valid' => array(
              'rule' => 'numeric',
              'message' => 'e.g. +999 8811 2022'
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

    public function numeric($check) {
            $valeur = array_values($check);
            return !preg_match('/^[0-9\+\(\)\s]*$/', $valeur[0]);
    }

    public function isOwnedBy($client, $motdepasse) {
        return $this->field('id', array('id' => $client, 'id_motdepasse' => $motdepasse)) !== false;
    }
}
?>
