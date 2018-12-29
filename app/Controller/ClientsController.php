<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */
App::uses('AppController', 'Controller');

class ClientsController extends AppController {
    public function __construct($request = null, $response = null) {
  		parent::__construct($request, $response);
  	}

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'logout');
    }

    public function index() {
        $this->Client->recursive = 0;
        $this->set('Clients', $this->paginate());
    }

    public function view($id = null) {
        if (!$this->Client->exists($id)) {
            throw new NotFoundException(__('Client invalide'));
        }
        $this->set('client', $this->Client->findById($id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Client->create();
            if ($this->Client->save($this->request->data)) {
                $this->Flash->success(__('Le client a été sauvegardé'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Le client n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        }
    }

    public function edit($id = null) {
        $this->Client->id = $id;
        if (!$this->Client->exists()) {
            throw new NotFoundException(__('Client Invalide'));
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
