<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */
App::uses('AppController', 'Controller');

class ClientController extends AppController {
    public function __construct($request = null, $response = null) {
  		  parent::__construct($request, $response);
  	}

    public function beforeFilter() {
        parent::beforeFilter();
        /* Permet aux utilisateurs de s'enregistrer et de se déconnecter */
        $this->Auth->allow('add', 'logout');
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(__("Nom d'user ou mot de passe invalide, réessayer"));
            }
        }
        $this->set('pIndex', 'users__login');
        $this->render(null, 'default-e14');
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }
    public function index() {
      /* TODO : affichage page profile courant */
        $this->Client->recursive = 0;
        $this->set('Clients', $this->paginate());
        $this->set('pIndex', 'users__index');
        $this->render(null, 'default-e14');
    }

    public function view($id = null) {
        if (!$this->Client->exists($id)) {
            throw new NotFoundException(__('Client invalide'));
        }
        $this->set('client', $this->Client->findById($id));
        $this->set('pIndex', 'users__view');
        $this->render(null, 'default-e14');
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Client->create();
            if ($this->Client->save($this->request->data)) {
                $this->Flash->success(__('Le client a été sauvegardé'));
                return $this->redirect(array('controller' => 'MotDePasse', 'action' => 'add', $this->Client->identifiant));
            } else {
                $this->Flash->error(__('Le client n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        }
        $this->set('pIndex', 'users__add');
        $this->render(null, 'default-e14');
    }

    public function edit($id = null, $passwordId = null) {
        $this->Client->id = $id;
        if (!$this->Client->exists()) {
            throw new NotFoundException(__('Client Invalide'));
        }
        if(isset($passwordId)) {
            $this->Client->fk_motdepasse = $passwordId;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Client->save($this->request->data)) {
                $this->Flash->success(__('Le client a été sauvegardé'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Le client n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        } else {
            $this->request->data = $this->Client->findById($id);
            unset($this->request->data['Client']['password']);
        }
        $this->set('pIndex', 'users__edit');
        $this->render(null, 'default-e14');
    }

    public function delete($id = null) {
        // Avant 2.5, utilisez
        // $this->request->onlyAllow('post');

        $this->request->allowMethod('post');

        $this->Client->id = $id;
        if (!$this->Client->exists()) {
            throw new NotFoundException(__('Client invalide'));
        }
        if ($this->Client->delete()) {
            $this->Flash->success(__('Client supprimé'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('Le client n\'a pas été supprimé'));
        return $this->redirect(array('action' => 'index'));
    }

}
?>
