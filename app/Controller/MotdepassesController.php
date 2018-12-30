Motdepasse<?php
App::uses('AppController', 'Controller');
/**
 * Motdepasses Controller
 */
class MotdepassesController extends AppController {

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

	public function add() {
			if ($this->request->is('post')) {
					$this->Motdepasse->create();
					if ($this->Motdepasse->save($this->request->data)) {
							$this->Flash->success(__('Le mot de passe a été sauvegardé'));
							return $this->redirect(array('controller' => 'Motdepasse', 'action' => 'index'));
					} else {
							$this->Flash->error(__('Le mot de passe n\'a pas été sauvegardé. Merci de réessayer.'));
					}
			}
	}

	public function edit($id = null) {
			$this->Motdepasse->id = $id;
			if (!$this->Motdepasse->exists()) {
					throw new NotFoundException(__('Mot de passe Invalide'));
			}
			if ($this->request->is('post') || $this->request->is('put')) {
					if ($this->Motdepasse->save($this->request->data)) {
							$this->Flash->success(__('Le mot de passe a été sauvegardé'));
							return $this->redirect(array('action' => 'index'));
					} else {
							$this->Flash->error(__('Le mot de passe n\'a pas été sauvegardé. Merci de réessayer.'));
					}
			} else {
					$this->request->data = $this->Motdepasse->findById($id);
					unset($this->request->data['Motdepasse']['password']);
			}
	}

	public function delete($id = null) {
			// Avant 2.5, utilisez
			// $this->request->onlyAllow('post');

			$this->request->allowMethod('post');

			$this->Motdepasse->id = $id;
			if (!$this->Motdepasse->exists()) {
					throw new NotFoundException(__('Mot de passe invalide'));
			}
			if ($this->Motdepasse->delete()) {
					$this->Flash->success(__('Mot de passe supprimé'));
					return $this->redirect(array('action' => 'add'));
			}
			$this->Flash->error(__('Le mot de passe n\'a pas été supprimé'));
			return $this->redirect(array('action' => 'index'));
	}
}
