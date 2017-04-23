<?php
namespace User\Model\Table;

use Cake\Chronos\Chronos;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use User\Model\Entity\User;
use Cake\Log\Log;
use Cake\ORM\Entity;

/**
 * Users Model
 */
class UsersTable extends Table
{

    /**
     * @var int Minimum length of passwords
     */
    public static $minPasswordLength = 8;

    /**
     * @var bool Use email as username
     */
    public static $emailAsUsername = false;

    /**
     * @var string Allowed password pattern
     */
    public static $passwordRegex = '/^(\w)+$/';

    /**
     * @var int Password reset expiration expiration in seconds
     */
    public static $passwordResetExpiry = 86400; // 24 h

    /**
     * @var int Password reset code length
     */
    public static $passwordResetCodeLength = 6;

    //public static $usersModel = 'Users.Users';
    //public static $groupsModel = 'User.Groups';
    //public static $rolesModel = 'User.Roles';
    //public static $permissionsModel = 'User.Permissions';

    
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('user_users');
        $this->displayField('username');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('PrimaryGroup', [
            'foreignKey' => 'group_id',
            'className' => 'User.Groups'
        ]);
        $this->belongsToMany('Groups', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'group_id',
            'joinTable' => 'user_groups_users',
            'className' => 'User.Groups'
        ]);
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
            ->add('superuser', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('superuser')
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->requirePresence('username', 'create')
            ->notEmpty('username')
            ->requirePresence('password', 'create')
            ->notEmpty('password')
            ->add('email', 'valid', ['rule' => 'email'])
            ->allowEmpty('email')
            ->add('email_verification_required', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('email_verification_required')
            ->allowEmpty('email_verification_code')
            ->allowEmpty('email_verification_expiry_timestamp')
            ->add('email_verified', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('email_verified')
            ->add('password_change_min_days', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('password_change_min_days')
            ->add('password_change_max_days', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('password_change_max_days')
            ->add('password_change_warning_days', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('password_change_warning_days')
            ->allowEmpty('password_change_timestamp')
            ->allowEmpty('password_expiry_timestamp')
            ->add('password_force_change', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('password_force_change')
            ->allowEmpty('password_reset_code')
            ->allowEmpty('password_reset_expiry_timestamp')
            ->add('login_enabled', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('login_enabled')
            ->allowEmpty('login_last_login_ip')
            ->allowEmpty('login_last_login_host')
            ->add('login_last_login_datetime', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('login_last_login_datetime')
            ->add('login_failure_count', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('login_failure_count')
            ->add('login_failure_datetime', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('login_failure_datetime')
            ->add('block_enabled', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('block_enabled')
            ->allowEmpty('block_reason')
            ->add('block_datetime', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('block_datetime');

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
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        return $rules;
    }

    /**
     * Finder method for user authentication
     *
     * @param Query $query
     * @param array $options
     * @return Query
     * @todo Exclude superusers from frontend user authentication (or make it optional)
     */
    public function findAuthUser(Query $query, array $options)
    {
        $query
            ->where(['Users.login_enabled' => true])
            ->contain(['Groups']);

        return $query;
    }

    /**
     * Validation rules for form login
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationLogin(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('username')
            ->notEmpty('username')
            ->requirePresence('password')
            ->notEmpty('password');

        return $validator;
    }

    /**
     * Add new user
     *
     * @param array $data
     * @return User
     */
    public function add(array $data)
    {
        $user = $this->newEntity(null);
        $user->accessible('*', true);
        $user->accessible(['password1', 'password2'], true);
        $user->accessible('password', false);

        $this->patchEntity($user, $data, ['validate' => 'add']);
        if ($user->errors()) {
            return $user;
        }

        $user->password = $user->password1;

        if ($this->save($user)) {
            Log::info('User added with ID ' . $user->id);
        }
        return $user;
    }

    /**
     * Validation rules for adding new users
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationAdd(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->add('superuser', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('superuser')
            ->requirePresence('username', 'create')
            ->notEmpty('username')
            ->requirePresence('password1', 'create')
            ->notEmpty('password1')
            ->add('password1', 'password', [
                'rule' => 'validateNewPassword1',
                'provider' => 'table',
                'message' => __d('user','Invalid password')
            ])
            ->requirePresence('password2', 'create')
            ->notEmpty('password2')
            ->add('password2', 'password', [
                'rule' => 'validateNewPassword2',
                'provider' => 'table',
                'message' => __d('user','Passwords do not match')
            ])
            ->add('is_login_allowed', 'valid', ['rule' => 'boolean'])
            //->requirePresence('is_login_allowed', 'create')
            ->notEmpty('is_login_allowed');


        if (static::$emailAsUsername) {
            $validator->add('username', 'email', [
                'rule' => ['email'],
                'message' => __d('user','The provided email address is invalid')
            ]);
        }


        return $validator;
    }


    /**
     * Create root user with default credentials
     *
     * @param $email
     * @param $password
     * @return bool|User
     */
    public function createRootUser($email, $password)
    {
        // check if there is already a root user
        if ($this->find()->where(['id' => 1])->first()) {
            return false;
        }

        $data = [
            'id' => 1,
            'superuser' => true,
            'name' => 'root',
            'username' => 'root',
            'email' => $email,
            'password' => $password,
            'login_enabled' => true,
            'email_verification_required' => false,
        ];

        $user = $this->newEntity();
        $user->accessible([
            'id', 'name', 'username', 'email', 'password', 'login_enabled', 'email_verification_required'
        ], true);
        $this->patchEntity($user, $data);

        //@TODO Add validation
        if ($this->save($user)) {
            Log::info('User \'root\' added with ID ' . $user->id, ['backend', 'user']);
        }

        return $user;
    }


    /**
     * Register new user with form data array
     *
     * @param array $data
     * @return User
     */
    public function register(array $data, $dispatchEvent = true)
    {
        $user = $this->newEntity(null, ['validate' => 'register']);
        $user->accessible('username', true);
        $user->accessible('password1', true);
        $user->accessible('password2', true);

        if ($data !== null) {
            // permit new registered users to login
            $data['login_enabled'] = true;

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
                Log::info('[user] New user \'' . $user->username . '\' registered with ID ' . $user->id, ['user']);
            }
        }

        if ($dispatchEvent === true) {
            $event = $this->eventManager()->dispatch(new Event('User.Model.User.register', $user));
        }
        return $user;
    }

    /**
     * Validation rules for the register method
     *
     * @param Validator $validator
     * @return Validator
     */
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
                'message' => __d('user','Invalid password')
            ])
            ->requirePresence('password2', 'create')
            ->notEmpty('password2')
            ->add('password2', 'password', [
                'rule' => 'validateNewPassword2',
                'provider' => 'table',
                'message' => __d('user','Passwords do not match')
            ])
            ->add('login_enabled', 'valid', ['rule' => 'boolean'])
            ->requirePresence('login_enabled', 'create')
            ->notEmpty('login_enabled');

        if (static::$emailAsUsername) {
            $validator->add('username', 'email', [
                'rule' => ['email'],
                'message' => __d('user','The provided email address is invalid')
            ]);
        }

        return $validator;
    }


    /**
     * Change user password
     * - Requires the current user password
     * - The new password MUST NOT match the current user password
     *
     * @param Entity|User $user
     * @param array $data
     * @return bool
     */
    public function changePassword(User &$user, array $data)
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
            $user->errors('password0', ['password' => __d('user','This is not your current password')]);
            unset($user->password0);
            unset($user->password1);
            unset($user->password2);
            return false;
        }

        // new password should not match current password
        if (strcmp($user->password0, $user->password1) === 0) {
            $user->errors('password1', [
                'password' => __d('user','This is your current password. Please create a new one!')
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
     * Validation rules to change password
     *
     * @param Validator $validator
     * @return Validator
     */
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
                'message' => __d('user','Invalid password')
            ])
            ->requirePresence('password2', 'create')
            ->notEmpty('password2')
            ->add('password2', 'password', [
                'rule' => 'validateNewPassword2',
                'provider' => 'table',
                'message' => __d('user','Passwords do not match')
            ]);

        return $validator;
    }

    /**
     * Reset user password
     *
     * @param Entity|User $user
     * @param array $data
     * @return bool
     */
    public function resetPassword(User &$user, array $data)
    {
        $username = (isset($data['username'])) ? $data['username'] : null;
        $resetCode = (isset($data['password_reset_code'])) ? $data['password_reset_code'] : null;
        if (!$username) {
            $user->errors('username', ['This is a required field']);
            return false;
        }
        if (!$resetCode) {
            $user->errors('password_reset_code', ['This is a required field']);
            return false;
        }

        $_user = $this->find()->where([
            'username' => $username,
            //'password_reset_code' => $resetCode
        ])->first();
        if (!$_user) {
            $user->errors('username', ['User not found']);
            return false;
        }
        if ($_user->password_reset_expiry_timestamp && Chronos::now()->gt($_user->password_reset_expiry_timestamp)) {
            $user->errors('password_reset_code', ['Password reset code has expired']);
            //throw new PasswordResetCodeExpiredException();
            return false;
        }
        if ($_user->password_reset_code != $resetCode) {
            $user->errors('password_reset_code', ['Password reset code is invalid']);
            //throw new PasswordResetCodeInvalidException();
            return false;
        }

        $user = $_user;
        $user->accessible('*', false);
        $user->accessible('password1', true);
        $user->accessible('password2', true);
        $user = $this->patchEntity($user, $data, ['validate' => 'resetPassword']);
        if ($user->errors()) {
            return false;
        }

        // apply new password
        $user->accessible('password', true);
        $user->password = $data['password1'];
        if (!$this->save($user)) {
            return false;
        }

        // cleanup
        unset($user->password1);
        unset($user->password2);
        unset($user->password); // hide password

        return $user;
    }

    /**
     * Validation rules to reset password
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationResetPassword(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('password1', 'create')
            ->notEmpty('password1')
            ->add('password1', 'password', [
                'rule' => 'validateNewPassword1',
                'provider' => 'table',
                'message' => __d('user','Invalid password')
            ])
            ->requirePresence('password2', 'create')
            ->notEmpty('password2')
            ->add('password2', 'password', [
                'rule' => 'validateNewPassword2',
                'provider' => 'table',
                'message' => __d('user','Passwords do not match')
            ]);

        return $validator;
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
            return __d('user','Password too short. Minimum {0} characters', static::$minPasswordLength);
        }

        // Check for illegal characters
        if (!preg_match(static::$passwordRegex, $value)) {
            return __d('user','Password contains illegal characters');
        }

        // Check for weak password
        if (isset($context['data']['username']) && $value == $context['data']['username']) {
            return __d('user','Password can not be the same as your username');
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

    public function forgotPassword(User &$user, $data = array(), $dispatchEvent = true)
    {
        $username = ($data['username']) ? $data['username'] : null;
        if (!$username) {
            $user->errors('username', ['required' => __d('user','This is a required field')]);
            return false;
        }

        $_user = $this->find()->where(['username' => $username])->first();
        if (!$_user) {
            $user->username = "";
            $user->errors('username', ['notfound' => __d('user','User "{0}" not found', h($username))]);
            return $user;
        }

        $user = $_user;
        $user->password_reset_code = $this->_generatePasswordResetCode();
        $user->password_reset_expiry_timestamp = time() + self::$passwordResetExpiry; // 24h
        if (!$this->save($user)) {
            return false;
        }

        if ($dispatchEvent === true) {
            $event = $this->eventManager()->dispatch(new Event('User.Model.User.passwordForgotten', $user));
        }
        return $user;
    }

    protected function _generatePasswordResetCode()
    {
        return strtoupper(self::random_str(self::$passwordResetCodeLength));
    }

    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @see http://stackoverflow.com/questions/4356289/php-random-string-generator
     *
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    static public function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

}
