<?php
/*
* MessageController
* Created by www.b23prodtm.info on 08/11/17.*/

App::uses('AppController', 'Controller');
App::uses('SQL', 'Cms');
/**
 * CakePHP MessageController
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class MessageController extends AppController {

	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
	}

	public function index($fk_identifiant = null) {
		if ($fk_identifiant === null) {
			$this->set('messages', $this->Message->find('all'));
		} else {
			$this->set('messages', $this->Message->find($fk_identifiant));
		}
		$this->set("pIndex","contactus__index");
		$this->render(null, "default-e14");
	}

	/**
	 * @param String $p method name
	 */
	public function admin_index($p = null) {
		$this->set('pIndex', 'admin__contactus');
		$this->set('pMethod', $p);
		$this->render(null, "default-e14");
	}

	public function view($id) {
		if (!$id) {
			throw new NotFoundException(__('Message invalide'));
		}

		$message = $this->Message->findById($id);
		if (!$message) {
			throw new NotFoundException(__('Message invalide'));
		}
		$this->set("pIndex","contactus__view");
		$this->set('message', $message);
		$this->render(null, "default-e14");
	}

	public function add($id = null) {
		if (empty($this->request->data)) {
			$this->request->data = $this->Message->findById($id);
		} else if ($this->request->is('post')) {
	    $this->Message->create();
			if ($this->Message->save($this->request->data)) {
	        $this->Flash->success(__('Votre message est enregistré.'));
	        return $this->redirect(array('action' => 'index'));
	    }
	    $this->Flash->error(__('Impossible d\'ajouter votre message.'));
    }
		$this->set("pIndex","contactus__write");
		$this->render(null, "default-e14");
	}


	public function edit($id = null) {
	    if (!$id) {
/*	        throw new NotFoundException(__('Message invalide'));*/
			return $this->redirect(array('action' => 'add'));
	    }

	    $post = $this->Message->findById($id);
	    if (!$post) {
	        throw new NotFoundException(__('Message invalide'));
	    }

	    if ($this->request->is(array('post', 'put'))) {
	        $this->Message->id = $id;
	        if ($this->Message->save($this->request->data)) {
	            $this->Flash->success(__('Votre message a été mis à jour.'));
	            return $this->redirect(array('action' => 'index'));
	        }
	        $this->Flash->error(__('Impossible de mettre à jour votre message.'));
	    }

	    if (!$this->request->data) {
	        $this->request->data = $post;
	    }
			$this->set("pIndex","contactus__edit");
			$this->render(null, "default-e14");
}
	public function delete($id) {
			/* devier les requetes delete?id=<id> */
	    if ($this->request->is('get')) {
	        throw new MethodNotAllowedException();
	    }

	    if ($this->Message->delete($id)) {
	        $this->Flash->success(
	            __('Le message avec id : %s a été supprimé.', h($id))
	        );
	    } else {
	        $this->Flash->error(
	            __('Le message avec l\'id: %s n\'a pas pu être supprimé.', h($id))
	        );
	    }

	    return $this->redirect(array('action' => 'index'));
	}
}
?>
