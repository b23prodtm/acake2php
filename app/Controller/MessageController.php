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

	public function index($id = null) {
		if ($id === null) {
			$this->set('messages', $this->Message->find('all'));
		} else {
			$this->set('messages', $this->Message->find($id));
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
			throw new NotFoundException(__('Invalid message'));
		}

		$message = $this->Message->findById($id);
		if (!$message) {
			throw new NotFoundException(__('Invalid message'));
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
			$this->request->data['Message']['id'] = $this->Auth->user('id');
			if ($this->Message->save($this->request->data)) {
	        $this->Flash->success(__('Message posted'));
	        return $this->redirect(array('action' => 'index'));
	    }
	    $this->Flash->error(__('Unable to post the message'));
    }
		$this->set("pIndex","contactus__write");
		$this->render(null, "default-e14");
	}


	public function edit($id = null) {
	    if (!$id) {
/*	        throw new NotFoundException(__('Invalid message'));*/
			return $this->redirect(array('action' => 'add'));
	    }

	    $post = $this->Message->findById($id);
	    if (!$post) {
	        throw new NotFoundException(__('Invalid message'));
	    }

	    if ($this->request->is(array('post', 'put'))) {
	        $this->Message->id = $id;
	        if ($this->Message->save($this->request->data)) {
	            $this->Flash->success(__('Message was successfully updated'));
	            return $this->redirect(array('action' => 'index'));
	        }
	        $this->Flash->error(__('Impossible to modify the message'));
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
	            __('Message %s was successfully removed.', h($id))
	        );
	    } else {
	        $this->Flash->error(
	            __('Message %s could NOT be removed', h($id))
	        );
	    }

	    return $this->redirect(array('action' => 'index'));
	}

	public function isAuthorized($client) {
    /* Tous les users inscrits peuvent ajouter les posts */
    if ($this->action === 'add') {
        return true;
    }

    /* Le propriétaire du post peut l'éditer et le supprimer */
    if (in_array($this->action, array('edit', 'delete'))) {
        $messageId = (int) $this->request->params['pass'][0];
        if ($this->Message->isOwnedBy($messageId, $client['id'])) {
            return true;
        }
    }

    return parent::isAuthorized($client);
	}
}
?>
