<?php

namespace User\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use User\Model\Entity\User;

class GoogleAuthController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['test']);

        if (Configure::read('User.layout')) {
            $this->viewBuilder()->layout(Configure::read('User.layout'));
        }
    }

    public function index()
    {
        $user = $this->Auth->userModel()->get($this->Auth->user('id'));

        if (!$user->gauth_secret || !$user->gauth_enabled) {
            $this->redirect(['action' => 'setup']);
        }

        if ($user->gauth_enabled) {
            $qrImageGenerator = new \Dolondro\GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
            $secret = $this->Auth->userModel()->getGoogleAuthSecret($user);
            $imgUri = $qrImageGenerator->generateUri($secret);

            $this->set('imgUri', $imgUri);
        }

        $this->set(compact('user'));
    }

    protected function _checkGoogleAuth(User $user, $code)
    {
        $googleAuthenticator = new \Dolondro\GoogleAuthenticator\GoogleAuthenticator();
        $secretKey = $user->gauth_secret;
        $code = $this->request->data('code');
        return $googleAuthenticator->authenticate($secretKey, $code);
    }

    public function setup()
    {
        $user = $this->Auth->userModel()->get($this->Auth->user('id'));

        if ($user->gauth_enabled) {
            $this->redirect(['action' => 'index']);
        }

        if (!$user->gauth_secret) {
            $user = $this->Auth->userModel()->setGoogleAuthSecret($user, null);
            if (!$user) {
                throw new \RuntimeException("Setup failed");
            }
        }

        if ($this->request->is(['put', 'post'])) {
            $code = $this->request->data('code');
            if (!$code) {
                $this->Flash->error("Code missing");
            } elseif ($this->_checkGoogleAuth($user, $code)) {
                if ($this->Auth->userModel()->enableGoogleAuth($user)) {

                    // update user in session
                    //$authUser = $this->Auth->user();
                    //$authUser['gauth_enabled'] = true;
                    //$this->Auth->setUser($authUser);

                    $this->Flash->success("2-Factor-Auth has been enabled");
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error("Operation failed");
                }
            } else {
                $this->Flash->error("Verification failed");
            }
        }

        $qrImageGenerator = new \Dolondro\GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $secret = $this->Auth->userModel()->getGoogleAuthSecret($user);
        $imgUri = $qrImageGenerator->generateUri($secret);

        $this->set('imgUri', $imgUri);
        $this->set(compact('user'));
    }

    public function disable()
    {
        $user = $this->Auth->userModel()->get($this->Auth->user('id'));

        if (!$user->gauth_enabled) {
            $this->redirect(['action' => 'setup']);
        }

        if ($this->request->is(['put', 'post'])) {

            $code = $this->request->data('code');
            if (!$code) {
                $this->Flash->error("Code missing");
            } elseif ($this->_checkGoogleAuth($user, $code)) {

                if ($this->Auth->userModel()->disableGoogleAuth($user)) {
                    $this->Flash->success("2-Factor-Auth has been disabled");

                    // update user in session
                    //$authUser = $this->Auth->user();
                    //$authUser['gauth_enabled'] = false;
                    //$this->Auth->setUser($authUser);

                    //$this->request->session()->delete('Auth.GoogleAuth');
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error("Operation failed");
                }

            } else {
                $this->Flash->error("Verification failed");
            }

            $this->redirect($this->Auth->redirectUrl());
        }

        $this->set(compact('user'));
    }

    public function verify()
    {
        $user = $this->Auth->userModel()->get($this->Auth->user('id'));

        if (!$user->gauth_enabled) {
            $this->redirect(['action' => 'setup']);
        }

        if ($this->request->is(['put', 'post'])) {
            $code = $this->request->data('code');
            if (!$code) {
                $this->Flash->error("Code missing");
            } elseif ($this->_checkGoogleAuth($user, $code)) {
                //$this->Flash->success("Verification successful");
                $this->request->session()->write('Auth.GoogleAuth', [
                    'verified' => true,
                    'client_ip' => $this->request->clientIp(),
                    'time' => time()
                ]);

                $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error("Verification failed");
            }
        }

        $this->set(compact('user'));
    }

    /*
    public function test()
    {
        if ($this->request->is(['post'])) {
            $googleAuthenticator = new \Dolondro\GoogleAuthenticator\GoogleAuthenticator();
            $secretKey = $this->request->data('secretKey');
            $code = $this->request->data('code');
            if ($googleAuthenticator->authenticate($secretKey, $code)) {
                $this->Flash->success("Valid");
            } else {
                $this->Flash->error("Invalid");
            }
        } else {

        }

        $secretFactory = new \Dolondro\GoogleAuthenticator\SecretFactory();
        //$secret = $secretFactory->create("MyAwesomeWebCo", "fm-labs");
        $secret = new \Dolondro\GoogleAuthenticator\Secret("fm-labs", "Test Dude", "TESTSECRETTESTSECRETTESTSECRET34");
        $secretKey = $secret->getSecretKey();
        //$qrImageGenerator = new \Dolondro\GoogleAuthenticator\QrImageGenerator\EndroidQrImageGenerator();

        $qrImageGenerator = new \Dolondro\GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $imgUri = $qrImageGenerator->generateUri($secret);

        $this->set('secretKey', $secretKey);
        $this->set('imgUrl', $imgUri);
    }
    */
}
