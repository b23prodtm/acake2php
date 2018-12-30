<?php
App::uses('AppModel', 'Model');
/**
 * Motdepasse Model
 *
 */
class Motdepasse extends AppModel {
  public $hasOne = array('Client' => array(
      'foreignKey' => 'fk_motdepasse'
  ));
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
    'password' => array(
        'required' => array(
            'rule' => 'alphaNumericDashUnderscore',
            'message' => 'Un mot de passe est requis',
            'allowEmpty' => false
        )
    ),
    'password_confirm' => array(
  			'confirme' => array(
          'rule' => 'fieldIsConfirmed',
          'message' => 'Veuillez confirmer votre mot de passe'
        )
  	),
		'cree' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'modifie' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

  public function beforeSave($options = array()) {
        foreach (array_keys($this->data[$this->alias]) as $key) {
              if (preg_match('/^password.*/', $key) && !empty($this->data[$this->alias][$key])) {
                  $passwordHasher = new BlowfishPasswordHasher();
                  $this->data[$this->alias][$key] = $passwordHasher->hash(
                      $this->data[$this->alias][$key]
                  );
              }
        }
        return true;
    }

  public function fieldIsConfirmed($check) {
    $valeur = array_values($check);
    return $this->field('motdepasse', array('motdepasse' => $valeur)) !== false;
  }

}
