<?php

namespace User\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;
use User\Model\Table\UsersTable;

class UserRegisterForm extends UserForm
{
    /**
     * {@inheritDoc}
     */
    protected function _buildSchema(Schema $schema)
    {
        $schema->addField('email', $this->Users->schema()->column('email'));
        $schema->addField('password1', [] /*$this->Users->schema()->column('password1')*/);
        $schema->addField('password2', [] /*$this->Users->schema()->column('password2')*/);

        return $schema;
    }

    /**
     * {@inheritDoc}
     */
    protected function _buildValidator(Validator $validator)
    {
        $validator = $this->Users->validator('register');

        return $validator;
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
        $user = $this->Users->register($data);
        if ($user && $user->errors()) {
            $this->_errors = $user->errors();
        }

        return $user;
    }
}
