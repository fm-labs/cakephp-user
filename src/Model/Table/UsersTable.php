<?php
namespace User\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use User\Model\Entity\User;

/**
 * Users Model
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->requirePresence('username', 'create')
            ->notEmpty('username')
            ->requirePresence('password', 'create')
            ->notEmpty('password')
            ->add('is_login_allowed', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_login_allowed', 'create')
            ->notEmpty('is_login_allowed');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));
        return $rules;
    }

    public function validationRegister(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->requirePresence('username', 'create')
            ->notEmpty('username')
            ->requirePresence('password1', 'create')
            ->notEmpty('password1')
            ->add('password1', 'password', [
                'rule' => 'validateNewPassword1',
                'provider' => 'table',
                'message' => __('Invalid password')
            ])
            ->requirePresence('password2', 'create')
            ->notEmpty('password2')
            ->add('password2', 'password', [
                'rule' => 'validateNewPassword2',
                'provider' => 'table',
                'message' => __('Passwords do not match')
            ])
            ->add('is_login_allowed', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_login_allowed', 'create')
            ->notEmpty('is_login_allowed');

        return $validator;
    }

    public function register($data)
    {
        $user = $this->newEntity(null, ['validate' => 'register']);
        $user->accessible('username', true);

        if ($data !== null) {
            //@TODO Configure automatic login enabling
            $data['is_login_allowed'] = true;

            $this->patchEntity($user, $data, ['validate' => 'register']);
            debug($user);
            if ($user->errors()) {
                return $user;
            }

            // If validation passes, assign password.
            // The entity should preferably use a PasswordHasher
            $user->accessible('password', true);
            $user->password = $user->password1;
            unset($user->password1);
            unset($user->password2);

            $this->save($user);
        }
        return $user;
    }

    /**
     * Password Validation Rule
     *
     * @param $value
     * @param $context
     * @return bool|string
     */
    public function validateNewPassword1($value, $context)
    {
        debug("validate new password1");
        $value = trim($value);

        // @TODO Configure min password length
        // Check password length
        if (strlen($value) < 8) {
            return __('USER_PASSWORD_ERROR_MIN_LENGTH {0}', 8);
        }

        // @TODO Configure allowed Chars
        // Check for illegal characters
        if (!preg_match('/^(\w)+$/', $value)) {
            return __('USER_PASSWORD_ERROR_ILLEGAL_CHARS');
        }

        // Check for weak password
        if (isset($context['data']['username']) && $value == $context['data']['username']) {
            return __('USER_PASSWORD_WEAK_SAME_AS_USERNAME');
        }

        return true;
    }

    /**
     * Password Verification Validation Rule
     * @param $value
     * @param $context
     * @return bool
     */
    public function validateNewPassword2($value, $context)
    {
        $value = trim($value);

        if (!isset($context['data']['password1'])) {
            return false;
        }

        if ($context['data']['password1'] === $value) {
            return true;
        }

        return false;
    }

}
