<?php

namespace User\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;
use User\Model\Table\UsersTable;

class PasswordForgottenForm extends UserForm
{
    protected function _buildSchema(Schema $schema)
    {
        $schema->addField('username', []);
        return $schema;
    }

    protected function _buildValidator(Validator $validator)
    {
        if (UsersTable::$emailAsUsername) {
            $validator->add('username', 'email', [
                'rule' => ['email', false],
                'message' => __d('user', 'This is not a valid email address')
            ]);
        }

        $validator->notEmpty('username');
        return $validator;
    }

    public function execute(array $data)
    {
        if (!$this->validate($data)) {
            return false;
        }

        return $this->_execute($data);
    }

    protected function _execute(array $data)
    {
        $user = $this->Users->findByUsername($data['username'])->first();
        if (!$user) {
            // if user not found we fake success to prevent user scanning
            //return true;

            $this->_errors = ['username' => [__d('user', 'User not found')]];
            return false;
        }

        if ($user->is_deleted) {
            $this->_errors = ['username' => [__d('user', 'Deleted user')]];
            return false;
        }

        $user = $this->Users->forgotPassword($user);
        if (!$user) {
            return false;
        }

        if ($user->errors()) {
            $this->_errors = $user->errors();
            return false;
        }

        return $user;
    }
}
