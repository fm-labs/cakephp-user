<?php
declare(strict_types=1);

namespace User\Form;

use Cake\Core\Configure;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Exception;
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
            // if user not found or invalid, we fake success to prevent user scanning
            if (Configure::read('debug')) {
                $this->_errors = ['username' => [__d('user', 'User not found')]];

                return false;
            } else {
                $this->_errors = ['username' => [__d('user', 'Invalid user')]];

                return false;
                //return true;
            }
        }

        if ($user->is_deleted) {
            if (Configure::read('debug')) {
                $this->_errors = ['username' => [__d('user', 'Deleted user')]];

                return false;
            } else {
                $this->_errors = ['username' => [__d('user', 'Invalid user')]];

                return false;
                //return true;
            }
        }

        $user = $this->Users->updatePasswordResetCode($user);
        if (!$user) {
            throw new Exception('Failed to issue password reset code');
        }

        if ($user->getErrors()) {
            $this->_errors = $user->getErrors();

            return false;
        }

        $this->user = $user;

        return true;
    }
}
