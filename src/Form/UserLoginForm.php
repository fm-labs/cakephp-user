<?php
declare(strict_types=1);

namespace User\Form;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use GoogleRecaptcha\Lib\Recaptcha2;
use User\Exception\AuthException;

/**
 * UserLogin Form.
 */
class UserLoginForm extends Form
{
    /**
     * @var Controller
     */
    protected Controller $controller;

    public function __construct(Controller $controller, ?EventManager $eventManager = null)
    {
        parent::__construct($eventManager);
        $this->controller = $controller;
    }

    /**
     * Builds the schema for the modelless form
     *
     * @param \Cake\Form\Schema $schema From schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        $schema->addField('username', [
            'required' => true
        ]);

        $schema->addField('password', [
            'required' => true
        ]);

        $schema->addField('captcha', [
            'required' => false
        ]);
        $schema->addField('g-recaptcha-response', [
            'required' => false
        ]);
        return $schema;
    }

    /**
     * Form validation builder
     *
     * @param \Cake\Validation\Validator $validator to use against the form
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('username');

        $validator
            ->notEmptyString('password');

        $validator->add('captcha', 'valid_recaptcha2', [
            'rule' => function ($value, array $context) {
                $googleRecaptchaResponse = $context['data']['g-recaptcha-response'] ?? null;
                if (!$googleRecaptchaResponse) {
                    return __('Captcha validation failed');
                }

                $secretKey = Configure::read('GoogleRecaptcha.secretKey', '');
                if (!Recaptcha2::verify($secretKey, $googleRecaptchaResponse)) {
                    return __('Google Recaptcha validation failed');
                }

                return true;
            }
        ]);

        return $validator;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    protected function _execute(array $data): bool
    {
        //debug($data);

        /** @var \User\Controller\AuthController $controller */
        $controller =& $this->controller;
//        $result = $controller->Authentication->getResult();
//        if ($controller->getRequest()->is(['put', 'post']) && !$result->isValid()) {
//            throw new AuthException(__d('user', 'Invalid credentials'));
//        }
//
//        // If the user is logged in send them away.
//        if ($result->isValid()) {
//            //print_r($result->getData());
//            $defaultRedirect = $controller->config['loginRedirectUrl'] ?? '/';
//            $target = $controller->Authentication->getLoginRedirect() ?? $defaultRedirect;
//            $controller->Flash->success(__d('user', 'Login successful'), ['key' => 'auth']);
//            $controller->redirect($target);
//        }
        $controller->Auth->login();

        return true;
    }

    public function getResponse(): \Cake\Http\Response|null
    {
        return null;
    }
}
