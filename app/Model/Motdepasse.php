<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

/**
 * Motdepasse Model
 *
 */
class Motdepasse extends AppModel {
  public $validationDomain = 'formulaire';
  public $hasOne = array('Client' => array(
      'foreignKey' => 'id_motdepasse'
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
            'message' => 'Pasword required',
            'allowEmpty' => false
        )
    ),
    'password_confirm' => array(
  			'confirme' => array(
          'rule' => 'fieldIsConfirmed',
          'message' => 'Confirm password'
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
    public function alphaNumericDashUnderscore($check) {
            $valeur = array_values($check);
            return !preg_match('/^[0-9a-zA-Z_-\@\.]*$/', $valeur[0]);
    }

  public function fieldIsConfirmed($check) {
    $valeur = array_values($check);
    return $this->data[$this->alias]['password'] === $valeur[0];
  }

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

}
