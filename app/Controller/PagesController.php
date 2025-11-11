<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;

class PagesController extends AppController
{
    public function display(...$path)
    {
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    /**
     * Action pour accepter les cookies
     */
    public function acceptCookies()
    {
        $this->autoRender = false;
        $this->request->allowMethod(['post']);

        // Définir un cookie pour enregistrer le consentement (valable 30 jours)
        $this->response->cookie([
            'name' => 'cookie_consent',
            'value' => '1',
            'expire' => time() + (86400 * 30),
            'path' => '/',
            'httpOnly' => true,
            'secure' => env('HTTPS') ? true : false,
            'sameSite' => 'Lax',
        ]);

        // Enregistrer le consentement dans la session
        $this->request->session()->write('cookie_consent', true);

        // Retourner une réponse JSON
        $this->response->type('json');
        $this->response->body(json_encode(['success' => true]));
        return $this->response;
    }
}
