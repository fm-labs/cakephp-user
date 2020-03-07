<?php

namespace User\Form;

use Cake\Core\Configure;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use GoogleRecaptcha\Lib\Recaptcha2;
use User\Model\Table\UsersTable;

class UserRegisterForm extends UserForm
{
    /**
     * {@inheritDoc}
     */
    protected function _buildSchema(Schema $schema)
    {
        $schema->addField('email', $this->Users->getSchema()->column('email'));
        $schema->addField('password1', [] /*$this->Users->getSchema()->column('password1')*/);
        $schema->addField('password2', [] /*$this->Users->getSchema()->column('password2')*/);

        if (Configure::read('User.Recaptcha.enabled')) {
            $schema->addField('g-recaptcha-response', []);
        }

        return $schema;
    }

    /**
     * {@inheritDoc}
     */
    protected function _buildValidator(Validator $validator)
    {
        $validator = $this->Users->getValidator('register');
        $validator->provider('form', $this);

        if (Configure::read('User.Recaptcha.enabled')) {
            $validator = $this->validationRecaptcha($validator);
        }

        return $validator;
    }

    /**
     * @param Validator $validator The validator instance
     * @return Validator
     */
    protected function validationRecaptcha(Validator $validator)
    {
        $validator
            ->requirePresence('g-recaptcha-response')
            ->notEmpty('g-recaptcha-response', __d('user', 'Are you human?'))
            ->add('g-recaptcha-response', 'recaptcha', [
                'rule' => 'checkRecaptcha',
                'provider' => 'form',
                'message' => __d('user', 'Invalid captcha')
            ]);

        return $validator;
    }

    /**
     * Google Recaptcha Validation Rule
     *
     * @param mixed $value Check value
     * @param mixed $context Check context
     * @return bool|string
     */
    public function checkRecaptcha($value, $context)
    {
        try {
            if (!Recaptcha2::verify(Configure::read('GoogleRecaptcha.secretKey'), $value)) {
                return __d('user', 'Captcha verification failed');
            }

        } catch (\Exception $ex) {
            return __d('user', 'Unable to verify reCAPTCHA. Please try again later');
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data)
    {
        if (UsersTable::$emailAsUsername && isset($data['email'])) {
            $data['username'] = $data['email'];
        }

        if (!$this->validate($data)) {
            return false;
        }

        return $this->_execute($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function _execute(array $data)
    {
        // reset the validator
        $this->Users->setValidator('register', $this->Users->validationRegister(new Validator()));

        $user = $this->Users->register($data);
        if ($user && $user->getErrors()) {
            $this->_errors = $user->getErrors();

            return false;
        }

        return $user;
    }
}
