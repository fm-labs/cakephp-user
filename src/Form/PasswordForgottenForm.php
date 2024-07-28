<?php
declare(strict_types=1);

namespace User\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;
use User\Model\Table\UsersTable;

class PasswordForgottenForm extends UserForm
{
    /**
     * @inheritDoc
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        $schema->addField('username', []);

        return $schema;
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        if (UsersTable::$emailAsUsername) {
            $validator->add('username', 'username_as_email', [
                'rule' => ['email', false],
                'message' => __d('user', 'This is not a valid email address'),
            ]);
        }

        $validator->notEmptyString('username');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $data, array $options = []): bool
    {
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
        $this->user = $user = $this->Users->findByUsername($data['username'])->first();
        if (!$user) {
            // @todo if user not found we fake success to prevent user scanning
            //return true;

            $this->_errors = ['username' => [__d('user', 'User not found')]];

            return false;
        }

        if ($user->is_deleted) {
            $this->_errors = ['username' => [__d('user', 'Deleted user')]];

            return false;
        }

        $user = $this->Users->updatePasswordResetCode($user);
        if (!$user) {
            return false;
        }

        if ($user->getErrors()) {
            $this->_errors = $user->getErrors();

            return false;
        }

        $this->user = $user;

        return true;
    }
}
