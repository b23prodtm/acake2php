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
                $this->Flash->error(__("Invalid username or password"));
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

    public function admin_view($id = 0) {
        return $this->redirect(array('action' => 'view', 'admin' => false, $id));
    }
    public function view($id = 0) {
        if (!$id) {
          return $this->redirect(array("action" => "index"));
        }
        if (!$this->Client->exists($id)) {
            throw new NotFoundException(__('Invalid username'));
        }
        $this->set('client', $this->Client->findById($id));
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
                $this->Flash->success(__('Subscription success'));
                $id = $this->Client->id;
                $this->request->data['Client'] = array_merge(
                    $this->request->data['Client'],
                    array('id' => $id)
                );
                /* Desaffectaction du 'password' en requete,
                pour éviter la sauvegarde en session en clair du mot de passe en appelant login.
                unset($this->request->data['Client']['id_motdepasse']);*/
                $this->Auth->login($this->request->data['Client']);
                /* Le mot de passe sera cree ensuite */
                return $this->redirect(array('controller' => 'MotDePasse', 'action' => 'add', $id));
            } else {
                $this->Flash->error(__('Failed to subscribe. Please try again'));
            }
        }
        $this->set('pIndex', 'users__add');
        $this->render(null, 'default-e14');
    }
    public function admin_edit($id = null, $id_motdepasse = null) {
        return $this->redirect(array('action' => 'edit', 'admin' => false, $id, $id_motdepasse));
    }
    public function edit($id = null, $id_motdepasse = null) {
        $this->Client->id = $id;
        if (!$this->Client->exists()) {
            throw new NotFoundException(__('Invalid username'));
        }
        if(isset($id_motdepasse)) {
            $this->Client->id_motdepasse = $id_motdepasse;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Client->save($this->request->data)) {
                $this->Flash->success(__('Saved changes success!'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Failed to save changes. Please try again'));
            }
        } else {
            $this->request->data = $this->Client->findById($id);
            /* Desaffectaction du 'password' en requete,
            pour éviter la sauvegarde en session en clair du mot de passe en appelant login. */
            unset($this->request->data['Client']['id_motdepasse']);
        }
        $this->set('pIndex', 'users__edit');
        $this->render(null, 'default-e14');
    }
    public function admin_delete($id = null) {
        return $this->redirect(array('action' => 'delete', 'admin' => false));
    }

    public function delete($id = null) {
        // Avant 2.5, utilisez
        // $this->request->onlyAllow('post');

        $this->request->allowMethod('post');

        $this->Client->id = $id;
        if (!$this->Client->exists()) {
            throw new NotFoundException(__('Invalid username'));
        }
        if ($this->Client->delete()) {
            $this->Flash->success(__('Subscription was removed'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('Subscription could NOT be removed'));
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
