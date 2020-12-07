<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ServiceUnavailableException;
use User\Model\Entity\User;

/**
 * Class GoogleOtpController
 *
 * One-time-password authentication with Google Authenticator.
 *
 * @package User\Controller
 */
class GoogleOtpController extends AppController
{
    /**
     * @inheritDoc
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        if (!class_exists('\Dolondro\GoogleAuthenticator\GoogleAuthenticator')) {
            throw new ServiceUnavailableException();
        }

        //$this->Authentication->allowUnauthenticated(['test']);

        if (Configure::read('User.layout')) {
            $this->viewBuilder()->setLayout(Configure::read('User.layout'));
        }
    }

    /**
     * Index method
     *
     * @return void
     */
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

    /**
     * Setup method
     *
     * @return void
     */
    public function setup()
    {
        $user = $this->Auth->userModel()->get($this->Auth->user('id'));

        if ($user->gauth_enabled) {
            $this->redirect(['action' => 'index']);
        }

        if (!$user->gauth_secret) {
            $user = $this->Auth->userModel()->setGoogleAuthSecret($user, null);
            if (!$user) {
                throw new \RuntimeException('Setup failed');
            }
        }

        if ($this->request->is(['put', 'post'])) {
            $code = $this->request->getData('code');
            if (!$code) {
                $this->Flash->error('Code missing');
            } elseif ($this->_checkGoogleAuth($user, $code)) {
                if ($this->Auth->userModel()->enableGoogleAuth($user)) {
                    // update user in session
                    //$authUser = $this->Auth->user();
                    //$authUser['gauth_enabled'] = true;
                    //$this->Auth->setUser($authUser);

                    $this->Flash->success('2-Factor-Auth has been enabled');
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error('Operation failed');
                }
            } else {
                $this->Flash->error('Verification failed');
            }
        }

        $qrImageGenerator = new \Dolondro\GoogleAuthenticator\QrImageGenerator\GoogleQrImageGenerator();
        $secret = $this->Auth->userModel()->getGoogleAuthSecret($user);
        $imgUri = $qrImageGenerator->generateUri($secret);

        $this->set('imgUri', $imgUri);
        $this->set(compact('user'));
    }

    /**
     * Disable method
     *
     * @return void
     */
    public function disable()
    {
        $user = $this->Auth->userModel()->get($this->Auth->user('id'));

        if (!$user->gauth_enabled) {
            $this->redirect(['action' => 'setup']);
        }

        if ($this->request->is(['put', 'post'])) {
            $code = $this->request->getData('code');
            if (!$code) {
                $this->Flash->error('Code missing');
            } elseif ($this->_checkGoogleAuth($user, $code)) {
                if ($this->Auth->userModel()->disableGoogleAuth($user)) {
                    $this->Flash->success('2-Factor-Auth has been disabled');

                    // update user in session
                    //$authUser = $this->Auth->user();
                    //$authUser['gauth_enabled'] = false;
                    //$this->Auth->setUser($authUser);

                    //$this->request->getSession()->delete('Auth.GoogleAuth');
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error('Operation failed');
                }
            } else {
                $this->Flash->error('Verification failed');
            }

            $this->redirect($this->Auth->redirectUrl());
        }

        $this->set(compact('user'));
    }

    /**
     * Verify method
     *
     * @return void
     */
    public function verify()
    {
        $user = $this->Auth->userModel()->get($this->Auth->user('id'));

        if (!$user->gauth_enabled) {
            $this->redirect(['action' => 'setup']);
        }

        if ($this->request->is(['put', 'post'])) {
            $code = $this->request->getData('code');
            if (!$code) {
                $this->Flash->error('Code missing');
            } elseif ($this->_checkGoogleAuth($user, $code)) {
                //$this->Flash->success("Verification successful");
                $this->request->getSession()->write('Auth.GoogleAuth', [
                    'verified' => true,
                    'client_ip' => $this->request->clientIp(),
                    'time' => time(),
                ]);

                $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error('Verification failed');
            }
        }

        $this->set(compact('user'));
    }

    /**
     * Autheticate user with google authenticator code
     *
     * @param \User\Model\Entity\User $user The user entity
     * @param string $code Google authenticator code
     * @return bool|\User\Model\Entity\User
     */
    protected function _checkGoogleAuth(User $user, $code)
    {
        $googleAuthenticator = new \Dolondro\GoogleAuthenticator\GoogleAuthenticator();
        $secretKey = $user->gauth_secret;
        $code = $this->request->getData('code');

        return $googleAuthenticator->authenticate($secretKey, $code);
    }

    /*
    public function test()
    {
        if ($this->request->is(['post'])) {
            $googleAuthenticator = new \Dolondro\GoogleAuthenticator\GoogleAuthenticator();
            $secretKey = $this->request->getData('secretKey');
            $code = $this->request->getData('code');
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
