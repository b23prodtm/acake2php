<?php
App::uses('AppController', 'Controller');
/**
 * MotDePasse Controller
 */
class MotDePasseController extends AppController {

/**
 * Scaffold
 *
 * @var mixed
 */
	public $scaffold;

	public function __construct($request = null, $response = null) {
			parent::__construct($request, $response);
	}

	public function beforeFilter() {
			parent::beforeFilter();
			/* Permet aux utilisateurs de creer un mot de passe */
			$this->Auth->allow('add', 'delete');
	}

	public function index() {
			$this->set('pIndex', 'users__index');
			$this->render(null, 'default-e14');
	}
	public function add($identifiant = null) {
		if ($this->request->is('post')) {
					$this->MotDePasse->create();
					if ($this->MotDePasse->save($this->request->data)) {
							$this->Flash->success(__('Le mot de passe a été sauvegardé'));
							if(!isset($identifiant)) {
									$identifiant = $this->Auth->user('identifiant');
							}
							$client = Client::findById($identifiant); 
							$this->Flash->message(__('Enregistrement du profil %s...', $client));
							/* Desaffectaction du 'password' en requete,
							pour éviter la sauvegarde en session en clair du mot de passe en appelant login. */
							unset($this->request->data['MotDePasse']['password']);
							unset($this->request->data['MotDePasse']['password_confirm']);
							if($client !== false) {
										return $this->redirect(array('controller' => 'Client', 'action' => 'edit', $identifiant, $this->MotDePasse->id));
							} else {
										return $this->redirect(array('controller' => 'MotDePasse', 'action' => 'index'));
							}
					} else {
							$this->Flash->error(__('Le mot de passe n\'a pas été sauvegardé. Merci de réessayer.'));
					}
			}
			$this->set('pIndex', 'users__add');
			$this->render(null, 'default-e14');
	}

	public function edit($id = null, $identifiant = null) {
			$this->MotDePasse->id = $id;
			if (!$this->MotDePasse->exists()) {
					throw new NotFoundException(__('Mot de passe Invalide'));
			}
			if ($this->request->is('post') || $this->request->is('put')) {
				if(!isset($identifiant)) {
						$identifiant = $this->Auth->user('identifiant');
				}
				$client = Client::findById($identifiant);
				if ($client !== false && $client->isOwnedBy($this->MotDePasse->id, $identifiant) && $this->MotDePasse->save($this->request->data)) {
							$this->Flash->success(__('Le mot de passe a été sauvegardé'));
							return $this->redirect(array('action' => 'index'));
					} else {
							$this->Flash->error(__('Le mot de passe n\'a pas été sauvegardé. Merci de réessayer.'));
					}
			} else {
					$this->request->data = $this->MotDePasse->findById($id);
					unset($this->request->data['MotDePasse']['password']);
					unset($this->request->data['MotDePasse']['password_confirm']);
			}
			$this->set('pIndex', 'users__edit');
			$this->render(null, 'default-e14');
	}

	public function delete($id = null, $identifiant = null) {
			// Avant 2.5, utilisez
			// $this->request->onlyAllow('post');

			$this->request->allowMethod('post', 'put');

			$this->MotDePasse->id = $id;
			if (!$this->MotDePasse->exists()) {
					throw new NotFoundException(__('Mot de passe invalide'));
			}
			if(!isset($identifiant)) {
					$identifiant = $this->Auth->user('identifiant');
			}
			$client = Client::findById($identifiant);
			if ($client !== false && $client->isOwnedBy($this->MotDePasse->id, $identifiant) && $this->MotDePasse->delete()) {
					$this->Flash->success(__('Mot de passe supprimé'));
					return $this->redirect(array('action' => 'add', $identifiant));
			}
			if(!$client) {
					$this->Flash->error(__("L'identifiant client '%s' est invalide.", $identifiant));
			} else {
				$this->Flash->error(__("Le client '%s' n'est pas l'auteur du mot de passe.", $identifiant));
			}
			$this->Flash->error(__('Le mot de passe n\'a pas été supprimé'));
			return $this->redirect(array('action' => 'index'));
	}
}