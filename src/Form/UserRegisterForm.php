<?php
declare(strict_types=1);

namespace User\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;
use User\Model\Table\UsersTable;

class UserRegisterForm extends UserForm
{
    use GoogleRecaptchaFormTrait;

    /**
     * @inheritDoc
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        $schema->addField('email', $this->Users->getSchema()->getColumn('email'));
        $schema->addField('password1', [] /*$this->Users->getSchema()->getColumn('password1')*/);
        $schema->addField('password2', [] /*$this->Users->getSchema()->getColumn('password2')*/);
        $schema = $this->_buildRecaptchaSchema($schema);

        return $schema;
    }

    /**
     * @inheritDoc
     */
    protected function _buildValidator(Validator $validator)
    {
        $validator = $this->Users->getValidator('register');
        $validator->setProvider('form', $this);
        $validator = $this->validationRecaptcha($validator);

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $data, array $options = []): bool
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
     * @inheritDoc
     */
    protected function _execute(array $data): bool
    {
        // reset the validator
        $this->Users->setValidator('register', $this->Users->validationRegister(new Validator()));

        $user = $this->Users->register($data);
        if ($user && $user->getErrors()) {
            $this->_errors = $user->getErrors();

            return false;
        }
        $this->user = $user;

        return true;
    }
}
