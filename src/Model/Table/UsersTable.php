<?php
namespace User\Model\Table;

use Cake\Log\Log;
use Cake\ORM\Entity;
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
     * @var int Minimum length of passwords
     */
    public static $minPasswordLength = 8;

    public static $emailAsUsername = true;

    public static $passwordRegex = '/^(\w)+$/';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('users');
        $this->displayField('username');
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

        if (static::$emailAsUsername) {
            $validator->add('username', 'email', [
                'rule' => ['email'],
                'message' => __('The provided email address is invalid')
            ]);
        }

        return $validator;
    }

    /**
     * Register new user with form data array
     *
     * @param $data
     * @return \Cake\Datasource\EntityInterface|Entity
     */
    public function register($data)
    {
        $user = $this->newEntity(null, ['validate' => 'register']);
        $user->accessible('username', true);
        $user->accessible('password1', true);
        $user->accessible('password2', true);

        if ($data !== null) {
            // permit new registered users to login
            $data['is_login_allowed'] = true;

            $this->patchEntity($user, $data, ['validate' => 'register']);
            if ($user->errors()) {
                return $user;
            }

            // If validation passes, assign password.
            // The entity should preferably use a PasswordHasher
            $user->accessible('password', true);
            $user->password = $user->password1;
            unset($user->password1);
            unset($user->password2);

            if ($this->save($user)) {
                Log::info('[plugin:user] New user registered with ID ' . $user->id);
            }
        }
        return $user;
    }

    public function validationChangePassword(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('password0')
            ->notEmpty('password0')
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
            ]);

        return $validator;
    }

    public function changePassword(Entity &$user, array $data)
    {
        $user->accessible('password0', true);
        $user->accessible('password1', true);
        $user->accessible('password2', true);

        $user = $this->patchEntity($user, $data, ['validate' => 'changePassword']);
        if ($user->errors()) {
            return false;
        }

        // validate current password
        if (!$user->getPasswordHasher()->check($data['password0'], $user->password)) {
            $user->errors('password0', ['password' => __('This is not your current password')]);
            unset($user->password0);
            unset($user->password1);
            unset($user->password2);
            return false;
        }

        // new password should not match current password
        if (strcmp($user->password0, $user->password1) === 0) {
            $user->errors('password0', [
                'password' => __('This is your current password. Please create a new one!')
            ]);
            unset($user->password1);
            unset($user->password2);
            return false;
        }

        // apply new password
        $user->accessible('password', true);
        $user->password = $data['password1'];
        $saved = $this->save($user);

        // cleanup
        unset($user->password0);
        unset($user->password1);
        unset($user->password2);
        #unset($user->password); // hide password

        return ($saved) ? true : false;
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
        $value = trim($value);

        // Check password length
        if (strlen($value) < static::$minPasswordLength) {
            return __('Password too short. Minimum {0} characters', static::$minPasswordLength);
        }

        // Check for illegal characters
        if (!preg_match(static::$passwordRegex, $value)) {
            return __('Password contains illegal characters');
        }

        // Check for weak password
        if (isset($context['data']['username']) && $value == $context['data']['username']) {
            return __('Password can not be the same as ');
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
