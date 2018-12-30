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
			$this->Auth->allow('add');
	}

	public function index() {
			$this->set('pIndex', 'users__index');
			$this->render(null, 'default-e14');
	}
	public function add($clientId = null) {
		if ($this->request->is('post')) {
					$this->MotDePasse->create();
					if ($this->MotDePasse->save($this->request->data)) {
							$this->Flash->success(__('Le mot de passe a été sauvegardé'));
							$client = isset($clientId) ? Client::findById($clientId) : false;
							if($client)
										return $this->redirect(array('controller' => 'Client', 'action' => 'edit', $clientId, $this->MotDePasse->id));
							return $this->redirect(array('controller' => 'MotDePasse', 'action' => 'index'));
					} else {
							$this->Flash->error(__('Le mot de passe n\'a pas été sauvegardé. Merci de réessayer.'));
					}
			}
			$this->set('pIndex', 'users__add');
			$this->render(null, 'default-e14');
	}

	public function edit($id = null) {
			$this->MotDePasse->id = $id;
			if (!$this->MotDePasse->exists()) {
					throw new NotFoundException(__('Mot de passe Invalide'));
			}
			if ($this->request->is('post') || $this->request->is('put')) {
					if ($this->MotDePasse->save($this->request->data)) {
							$this->Flash->success(__('Le mot de passe a été sauvegardé'));
							return $this->redirect(array('action' => 'index'));
					} else {
							$this->Flash->error(__('Le mot de passe n\'a pas été sauvegardé. Merci de réessayer.'));
					}
			} else {
					$this->request->data = $this->MotDePasse->findById($id);
					unset($this->request->data['MotDePasse']['password']);
			}
			$this->set('pIndex', 'users__edit');
			$this->render(null, 'default-e14');
	}

	public function delete($id = null) {
			// Avant 2.5, utilisez
			// $this->request->onlyAllow('post');

			$this->request->allowMethod('post');

			$this->MotDePasse->id = $id;
			if (!$this->MotDePasse->exists()) {
					throw new NotFoundException(__('Mot de passe invalide'));
			}
			if ($this->MotDePasse->delete()) {
					$this->Flash->success(__('Mot de passe supprimé'));
					return $this->redirect(array('action' => 'add'));
			}
			$this->Flash->error(__('Le mot de passe n\'a pas été supprimé'));
			return $this->redirect(array('action' => 'index'));
	}
}
