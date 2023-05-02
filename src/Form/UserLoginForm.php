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
class UserLoginForm extends UserForm
{
    use GoogleRecaptchaFormTrait;

    public function __construct(?EventManager $eventManager = null)
    {
        parent::__construct(null, $eventManager);
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
        $schema = $this->_buildRecaptchaSchema($schema);
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

        $validator = $this->validationRecaptcha($validator);
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
}
