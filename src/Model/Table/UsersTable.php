<?php
namespace User\Model\Table;

use Banana\Model\TableInputSchema;
use Cake\Chronos\Chronos;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use User\Model\Entity\User;

/**
 * Users Model
 */
class UsersTable extends Table
{

    /**
     * @var bool Use email as username
     */
    public static $emailAsUsername = false;

    /**
     * @var int Minimum length of passwords
     */
    public static $passwordMinLength = 8;

    /**
     * @var int Minimum # of lowercase characters
     */
    public static $passwordMinLowercase = -1;

    /**
     * @var int Minimum # of uppercase characters
     */
    public static $passwordMinUppercase = -1;

    /**
     * @var int Minimum # of special characters
     */
    public static $passwordMinSpecialChars = -1;

    /**
     * @var int Minimum # of number characters
     */
    public static $passwordMinNumbers = -1;

    /**
     * @var string Allowd special chars
     */
    public static $passwordSpecialChars = "_-!?$%()=";

    /**
     * @var string Allowed password pattern
     * @deprecated
     * @TODO Refactor/Merge with passwordSpecialChars
     */
    public static $passwordRegex = '/^[A-Za-z0-9\_\-\!\?\$\%\(\)\=]+$/';

    /**
     * @var int Password reset expiration expiration in seconds
     */
    public static $passwordResetExpiry = 86400; // 24 h

    /**
     * @var int Password reset code length
     * @deprecated Use $verificationCodeLength instead
     */
    public static $passwordResetCodeLength = 8;

    /**
     * @var int Length of generated verification codes
     */
    public static $verificationCodeLength = 8;

    /**
     * @var array List of related models passed to the auth finder method
     */
    public static $contains = ['UserGroups'];


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
        $this->belongsTo('UserGroups', [
            'foreignKey' => 'group_id',
            'className' => 'User.UserGroups'
        ]);

        if (Plugin::loaded('Search')) {
            $this->addBehavior('Search.Search');
            $this->searchManager()
                ->add('name', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['title']
                ])
                ->add('username', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['title']
                ])
                ->add('email', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['title']
                ])
                ->value('login_enabled', [
                    'filterEmpty' => true
                ]);
        }
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
        $rules->add($rules->isUnique(['username'], __d('user','This username is already in use')));
        $rules->add($rules->isUnique(['email'], __d('user','This email address is already in use')));
        $rules->add($rules->existsIn(['group_id'], 'UserGroups'));

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
            ->contain(static::$contains);

        return $query;
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

            //->requirePresence('name', 'create')
            //->notEmpty('name')

            ->requirePresence('username', 'create')
            ->notEmpty('username')

            //->requirePresence('password', 'create')
            //->notEmpty('password')

            ->add('email', 'valid', ['rule' => 'email'])
            //->allowEmpty('email')

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

    protected function validationNewPassword(Validator $validator)
    {
        $validator
            ->requirePresence('password1', 'create')
            ->notEmpty('password1')
            ->add('password1', 'password', [
                'rule' => 'validateNewPassword1',
                'provider' => 'table',
                'message' => __d('user', 'Invalid password')
            ])
            ->add('password1', 'password_strength', [
                'rule' => ['validatePasswordComplexity'],
                'provider' => 'table',
                'message' => __d('user', 'Weak password')
            ])

            ->requirePresence('password2', 'create')
            ->notEmpty('password2')
            ->add('password2', 'password', [
                'rule' => 'validateNewPassword2',
                'provider' => 'table',
                'message' => __d('user', 'Passwords do not match')
            ]);

        return $validator;
    }

    /**
     * Validation rules for adding new users
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationAdd(Validator $validator)
    {
        $validator = $this->validationDefault($validator);
        $validator = $this->validationNewPassword($validator);
        $validator
            ->requirePresence('username', 'create')
            ->notEmpty('username');

        if (static::$emailAsUsername) {
            $validator->add('username', 'email', [
                'rule' => ['email'],
                'message' => __d('user', 'The provided email address is invalid')
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
        $user->accessible(array_keys($data), true);
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
     * @param bool $dispatchEvent
     * @return User
     */
    public function register(array $data, $dispatchEvent = true)
    {
        $user = $this->newEntity();
        $user->accessible('*', false);
        $user->accessible(['username', 'name', 'first_name', 'last_name', 'email', 'locale', 'timezone', 'currency'], true);
        $user->accessible(['password1', 'password2'], true);
        $user->accessible(['group_id'], true);

        // Login
        // By default registered users are allowed to log in
        $user->login_enabled = true; //@TODO Read from config
        $user->block_enabled = false;
        $user->password_force_change = false; //@TODO Read from config

        // No-Login
        // Creates a user with no password and login disabled
        $noLogin = (isset($data['_nologin'])) ? (bool)$data['_nologin'] : false; //@TODO Read from config
        if ($noLogin) {
            $user->login_enabled = false;

            $this->validator('register')
                ->allowEmpty('password1')
                ->requirePresence('password1', false)
                ->allowEmpty('password2')
                ->requirePresence('password2', false);

            $user->accessible(['password1', 'password2'], false);
        }

        // Email-As-Username
        // email has been entered as username, so copy value to email field
        if (self::$emailAsUsername && isset($data['email'])) {
            $data['username'] = $data['email'];
        }

        // Name
        // @TODO first_name and last_name properties are deprecated
        if (isset($data['first_name']) && isset($data['last_name'])) {
            $data['name'] = sprintf("%s %s", $data['first_name'], $data['last_name']);
        }
        if (!isset($data['name']) && isset($data['username'])) {
            $data['name'] = $data['username'];
        }

        // Email verification
        $user->email_verified = false;
        $user->email_verification_required = !(bool) Configure::read('User.Signup.disableEmailVerification');
        $user->email_verification_code = self::generateRandomVerificationCode(self::$verificationCodeLength);
        $user->email_verification_expiry_timestamp = time() + DAY; // @TODO Read expiry offset from config

        // Locale
        if (!isset($data['locale'])) {
            $data['locale'] = I18n::locale();
        }
        if (!isset($data['timezone'])) {
            $data['timezone'] = date_default_timezone_get();
        }
        if (!isset($data['currency'])) {
            $data['currency'] = 'EUR';
        }

        // Event 'User.Model.User.beforeRegister'
        if ($dispatchEvent === true) {
            $this->eventManager()->dispatch(new Event('User.Model.User.beforeRegister', $user, $data));
        }

        // User data validation
        $this->patchEntity($user, $data, ['validate' => 'register']);
        if ($user->errors()) {
            return $user;
        }

        // Password
        // If validation passes, assign password.
        // The entity should preferably use a PasswordHasher
        $user->password = $user->password1;
        unset($user->password1);
        unset($user->password2);

        // Save
        if ($this->save($user)) {
            // Event 'User.Model.User.register'
            if ($dispatchEvent === true) {
                $this->eventManager()->dispatch(new Event('User.Model.User.register', $user, $data));
            }
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
        $validator = $this->validationDefault($validator);
        $validator = $this->validationNewPassword($validator);
        $validator
            //->requirePresence('name', 'create')
            //->notEmpty('name')
            //->notEmpty('first_name')
            //->notEmpty('last_name')

            ->requirePresence('username', 'create')
            ->notEmpty('username')

            ->add('email', 'email', [
                'rule' => ['email'],
                'message' => __d('user', 'The provided email address is invalid')
            ])
            ->add('login_enabled', 'valid', ['rule' => 'boolean'])
        ;

        if (static::$emailAsUsername) {
            // email validation for 'username'
            $validator->add('username', 'email', [
                'rule' => ['email'],
                'message' => __d('user', 'The provided email address is invalid')
            ]);
            // require 'email'
            $validator->requirePresence('email', 'create')
                ->notEmpty('email');
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
            $user->errors('password0', ['password' => __d('user', 'This is not your current password')]);
            unset($user->password0);
            unset($user->password1);
            unset($user->password2);

            return false;
        }

        // new password should not match current password
        if (strcmp($user->password0, $user->password1) === 0) {
            $user->errors('password1', [
                'password' => __d('user', 'This is your current password. Please create a new one!')
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
        $validator = $this->validationNewPassword($validator);
        $validator
            ->requirePresence('password0')
            ->notEmpty('password0')
        ;

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
            $user->errors('username', [__d('user', 'This is a required field')]);

            return false;
        }
        if (!$resetCode) {
            $user->errors('password_reset_code', [__d('user', 'This is a required field')]);

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
            $user->errors('password_reset_code', [__d('user', 'Password reset code has expired')]);
            //throw new PasswordResetCodeExpiredException();
            return false;
        }
        if ($_user->password_reset_code != $resetCode) {
            $user->errors('password_reset_code', [__d('user', 'Password reset code is invalid')]);
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

        // clean the reset codes
        $user->password_reset_code = null;
        $user->password_reset_expiry_timestamp = null;

        if (!$this->save($user)) {
            return false;
        }

        // cleanup
        unset($user->password1);
        unset($user->password2);
        unset($user->password); // hide password

        $event = $this->eventManager()->dispatch(new Event('User.Model.User.passwordReset', $user));

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
        $validator = $this->validationNewPassword($validator);
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('password1', 'create')
            ->notEmpty('password1')
            ->add('password1', 'password1', [
                'rule' => 'validateNewPassword1',
                'provider' => 'table',
                'message' => __d('user', 'Invalid password')
            ])
            ->requirePresence('password2', 'create')
            ->notEmpty('password2')
            ->add('password2', 'password2', [
                'rule' => 'validateNewPassword2',
                'provider' => 'table',
                'message' => __d('user', 'Passwords do not match')
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
        //if (strlen($value) < static::$passwordMinLength) {
        //    return __d('user', 'Password too short. Minimum {0} characters', static::$passwordMinLength);
        //}

        // Check for illegal characters
        //if (!preg_match(static::$passwordRegex, $value)) {
        //    return __d('user', 'Password contains illegal characters');
        //}

        // Check for weak password
        if (isset($context['data']['username']) && $value == $context['data']['username']) {
            return __d('user', 'Password can not be the same as your username');
        }

        return true;
    }

    public function validatePasswordComplexity($value, $options = [], $context = null)
    {
        if (func_num_args() == 2) {
            $context = $options;
            $options = [];
        }

        $defaults = [
            'allowedPattern'        => self::$passwordRegex,

            'allowedSpecialChars'   => self::$passwordSpecialChars,
            'minLength'             => self::$passwordMinLength,
            'lowercase'             => self::$passwordMinLowercase,
            'uppercase'             => self::$passwordMinUppercase,
            'special'               => self::$passwordMinSpecialChars,
            'numbers'               => self::$passwordMinNumbers,
        ];
        $options = array_merge($defaults, $options);

        // Check password length
        if (strlen($value) < $options['minLength']) {
            return __d('user', 'Password too short. Minimum {0} characters', $options['minLength']);
        }

        // Check for illegal characters
        if ($options['allowedPattern'] && !preg_match($options['allowedPattern'], $value)) {
            return __d('user', 'Password contains illegal characters');
        }

        if ($options['numbers'] > 0 && !preg_match("#[0-9]+#", $value)) {
            return __d('user', "Password must include at least one number!");
        }

        if ($options['lowercase'] > 0 && !preg_match("#[a-z]+#", $value)) {
            return __d('user', "Password must include at least one lowercase letter!");
        }

        if ($options['uppercase'] > 0 && !preg_match("#[A-Z]+#", $value)) {
            return __d('user', "Password must include at least one UPPERCASE letter!");
        }

        if ($options['special'] > 0 && !preg_match("#[".preg_quote($options['allowedSpecialChars'], "#") . "]+#", $value)) {
            return __d('user', "Password must include at least one special character!");
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

    /**
     * Validation rules to reset password
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationActivate(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('email')
            ->notEmpty('email')
            ->requirePresence('email_verification_code')
            ->notEmpty('email_verification_code');

        return $validator;
    }

    /**
     * Activate user
     *
     * @param array $data
     * @param bool $dispatchEvent
     * @return bool
     * @todo Refactor with Form
     */
    public function activate(array $data = [], $dispatchEvent = true)
    {
        $email = (isset($data['email'])) ? strtolower(trim($data['email'])) : null;
        $code = (isset($data['email_verification_code'])) ? trim($data['email_verification_code']) : null;
        $user = $this->find()->where(['email' => $email])->contain([])->first();

        if (!$user || strcmp(strtoupper($user->get('email_verification_code')),strtoupper($code)) !== 0) {
            return false;
        }

        $user->email_verified = true;
        if ($this->save($user)) {
            if ($dispatchEvent === true) {
                $this->eventManager()->dispatch(new Event('User.Model.User.activate', $user));
            }
            return $user;
        }

        return false;
    }

    /**
     * Forgot password
     *
     * @param User $user
     * @param array $data
     * @param bool|true $dispatchEvent
     * @return bool|mixed|User
     */
    public function forgotPassword(User &$user, array $data = [], $dispatchEvent = true)
    {
        $username = ($data['username']) ? $data['username'] : null;
        if (!$username) {
            $user->errors('username', ['required' => __d('user', 'This is a required field')]);

            return false;
        }

        $_user = $this->find()->where(['username' => $username])->first();
        if (!$_user) {
            $user->username = "";
            $user->errors('username', ['notfound' => __d('user', 'User not found', h($username))]);

            return $user;
        }

        $user = $_user;
        $user->password_reset_code = self::generateRandomVerificationCode(self::$verificationCodeLength);
        $user->password_reset_expiry_timestamp = time() + self::$passwordResetExpiry; // 24h
        if ($this->save($user)) {
            if ($dispatchEvent === true) {
                $this->eventManager()->dispatch(new Event('User.Model.User.passwordForgotten', $user));
            }
            return $user;
        }

        return false;
    }

    public function markDeleted(User $user)
    {
        $user->is_deleted = true;
        $user->login_enabled = false;
        $user->block_enabled = true;
        $user->block_reason = 'DELETED';
        $user->block_datetime = new Time();

        return $this->save($user);
    }

    public function resetDeleted(User $user)
    {
        $user->is_deleted = false;
        $user->login_enabled = true;
        $user->block_enabled = false;
        $user->block_reason = null;
        $user->block_datetime = null;

        return $this->save($user);
    }

    public function resendVerificationCode(User $user)
    {
        //@TODO Check if the verification code has expired. If so, create new verification code.
        $event = $this->eventManager()->dispatch(new Event('User.Model.User.activationResend', $user));

        if ($event->result == false) {
            debug("failed");
            return false;
        }

        return $user;
    }


    public static function generateRandomVerificationCode($length = 8)
    {
        //@TODO Make use of random_compat vendor lib
        return strtoupper(self::random_str($length));
    }

    /**
     * Generate email verification url from User entity
     * @return string Full URL
     */
    public static function buildEmailVerificationUrl(User $user)
    {
        return Router::url([
            'prefix' => false, 'plugin' => 'User', 'controller' => 'User', 'action'=>'activate',
            'c' => base64_encode($user->email_verification_code),
            'm' => base64_encode($user->email)
        ], true);
    }

    /**
     * Generate password reset url from User entity
     * @return string Full URL
     */
    public static function buildPasswordResetUrl(User $user)
    {
        return Router::url([
            'prefix' => false, 'plugin' => 'User', 'controller' => 'User', 'action' => 'passwordReset',
            'c' => base64_encode($user->password_reset_code),
            'u' => base64_encode($user->username)
        ], true);
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
    public static function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }

        return $str;
    }

}
