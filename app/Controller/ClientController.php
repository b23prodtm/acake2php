<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class ClientController extends AppController {
    public function __construct($request = null, $response = null) {
  		  parent::__construct($request, $response);
  	}

    public function beforeFilter() {
        parent::beforeFilter();
        /* Permet aux utilisateurs de s'enregistrer et de se déconnecter */
        $this->Auth->allow('add', 'logout');
    }

    public function admin_login() {
        $this->Auth->redirectUrl(array('action' => 'index', 'admin' => true));
        return $this->redirect(array('action' => 'login', 'admin' => false));
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

    public function admin_logout() {
        return $this->redirect(array('action' => 'logout', 'admin' => false));
    }
    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function index() {
      $this->set('pIndex', 'users__index');
      $this->render(null, 'default-e14');
    }

    public function admin_index() {
        $this->Client->recursive = 0;
        $this->set('clients', $this->paginate());
        $this->set('pIndex', 'users__adminindex');
        $this->render(null, 'admin_default-e14');
    }

    public function admin_view($identifiant = null) {
        return $this->redirect(array('action' => 'view', 'admin' => false));
    }
    public function view($identifiant = null) {
        if (!$this->Client->exists($identifiant)) {
            throw new NotFoundException(__('Client invalide'));
        }
        $this->set('client', $this->Client->findById($identifiant));
        $this->set('pIndex', 'users__view');
        $this->render(null, 'default-e14');
    }
    public function admin_add() {
        return $this->redirect(array('action' => 'add', 'admin' => false));
    }
    public function add() {
        if ($this->request->is('post')) {
            $this->Client->create();
            if ($this->Client->save($this->request->data)) {
                $this->Flash->success(__('Le client a été sauvegardé'));
                $id = $this->Client->identifiant;
                $this->request->data['Client'] = array_merge(
                    $this->request->data['Client'],
                    array('id' => $id)
                );
                /* Desaffectaction du 'password' en requete,
                pour éviter la sauvegarde en session en clair du mot de passe en appelant login.
                unset($this->request->data['Client']['fk_motdepasse']);*/
                $this->Auth->login($this->request->data['Client']);
                /* Le mot de passe sera cree ensuite */
                return $this->redirect(array('controller' => 'MotDePasse', 'action' => 'add', $id));
            } else {
                $this->Flash->error(__('Le client n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        }
        $this->set('pIndex', 'users__add');
        $this->render(null, 'default-e14');
    }
    public function admin_edit($identifiant = null, $fk_motdepasse = null) {
        return $this->redirect(array('action' => 'edit', 'admin' => false));
    }
    public function edit($identifiant = null, $fk_motdepasse = null) {
        $this->Client->identifiant = $identifiant;
        if (!$this->Client->exists()) {
            throw new NotFoundException(__('Client Invalide'));
        }
        if(isset($fk_motdepasse)) {
            $this->Client->fk_motdepasse = $fk_motdepasse;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Client->save($this->request->data)) {
                $this->Flash->success(__('Le client a été sauvegardé'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Le client n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        } else {
            $this->request->data = $this->Client->findById($identifiant);
            /* Desaffectaction du 'password' en requete,
            pour éviter la sauvegarde en session en clair du mot de passe en appelant login. */
            unset($this->request->data['Client']['fk_motdepasse']);
        }
        $this->set('pIndex', 'users__edit');
        $this->render(null, 'default-e14');
    }
    public function admin_delete($identifiant = null) {
        return $this->redirect(array('action' => 'delete', 'admin' => false));
    }

    public function delete($identifiant = null) {
        // Avant 2.5, utilisez
        // $this->request->onlyAllow('post');

        $this->request->allowMethod('post');

        $this->Client->identifiant = $identifiant;
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

    public function admin_recovery() {
        return $this->redirect(array('action' => 'recovery', 'admin' => false));
    }
    public function recovery() {
        $this->set('client', $this->Client);
        $Email = new CakeEmail();
        $Email->helpers(array('Html', 'Text'));
        /* app/view/Emails*/
        $Email->template('recovery', 'default')
          ->emailFormat('html')
          ->to($this->Client->email)
          ->send();
    }

}
?>
