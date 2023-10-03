<?php
declare(strict_types=1);

namespace User\Model\Table;

use Cake\Chronos\Chronos;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\I18n\FrozenTime;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use User\Exception\PasswordResetException;
use User\Model\Entity\User;

/**
 * Users Model
 */
class UsersTable extends UserBaseTable
{
    /**
     * @var bool Use email as username
     */
    public static bool $emailAsUsername = false;

    /**
     * @var int Minimum length of passwords
     */
    public static int $passwordMinLength = 8;

    /**
     * @var int Minimum # of lowercase characters
     */
    public static int $passwordMinLowercase = -1;

    /**
     * @var int Minimum # of uppercase characters
     */
    public static int $passwordMinUppercase = -1;

    /**
     * @var int Minimum # of special characters
     */
    public static int $passwordMinSpecialChars = -1;

    /**
     * @var int Minimum # of number characters
     */
    public static int $passwordMinNumbers = -1;

    /**
     * @var string Allowd special chars
     */
    public static string $passwordSpecialChars = '_-!?$%()=+[].,§';

    /**
     * @var int Password reset expiration expiration in seconds
     */
    public static int $passwordResetExpiry = 86400; // 24 h

    /**
     * @var int Password reset code length
     * @deprecated Use $verificationCodeLength instead
     */
    public static int $passwordResetCodeLength = 8;

    /**
     * @var int Length of generated verification codes
     */
    public static int $verificationCodeLength = 8;

    /**
     * @var array List of related models passed to the auth finder method
     */
    public static array $contains = ['UserGroups'];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('user_users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('UserGroups', [
            'foreignKey' => 'group_id',
            'className' => 'User.UserGroups',
        ]);

//        if (Plugin::isLoaded('Cupcake')) {
//            $this->addBehavior('Cupcake.Attributes');
//        }

        if (Plugin::isLoaded('Search')) {
            $this->addBehavior('Search.Search');
            $this->searchManager()
                ->add('name', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['title'],
                ])
                ->add('username', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['title'],
                ])
                ->add('email', 'Search.Like', [
                    'before' => true,
                    'after' => true,
                    'fieldMode' => 'OR',
                    'comparison' => 'LIKE',
                    'wildcardAny' => '*',
                    'wildcardOne' => '?',
                    'field' => ['title'],
                ])
                ->value('login_enabled', [
                    'filterEmpty' => true,
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
    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker
    {
        $rules->add($rules->isUnique(['username'], __d('user', 'This username is already in use')));
        $rules->add($rules->isUnique(['email'], __d('user', 'This email address is already in use')));
        $rules->add($rules->existsIn(['group_id'], 'UserGroups'));

        return $rules;
    }

    /**
     * Finder method for user authentication
     *
     * @param \Cake\ORM\Query $query The query object
     * @param array $options Finder options
     * @return \Cake\ORM\Query
     * @todo Exclude superusers from frontend user authentication (or make it optional)
     */
    public function findAuthUser(Query $query, array $options)
    {
        $query
            //->where(['Users.login_enabled' => true])
            ->contain(static::$contains);

        return $query;
    }

    /**
     * @param string $username Username to search for
     * @param array $options Finder options
     * @return \Cake\ORM\Query
     */
    public function findByUsername($username, array $options = [])
    {
        return $this->find('all', $options)->where(['username' => $username]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create')

            ->add('superuser', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('superuser')

            //->requirePresence('name', 'create')
            //->notEmptyString('name')

            ->requirePresence('username', 'create')
            ->notEmptyString('username')

            //->requirePresence('password', 'create')
            //->notEmptyString('password')

            ->add('email', 'valid', ['rule' => 'email'])
            //->allowEmptyString('email')

            ->add('email_verification_required', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('email_verification_required')

            ->allowEmptyString('email_verification_code')

            ->allowEmptyString('email_verification_expiry_timestamp')

            ->add('email_verified', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('email_verified')

            ->add('password_change_min_days', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('password_change_min_days')

            ->add('password_change_max_days', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('password_change_max_days')

            ->add('password_change_warning_days', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('password_change_warning_days')

            ->allowEmptyString('password_change_timestamp')

            ->allowEmptyString('password_expiry_timestamp')

            ->add('password_force_change', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('password_force_change')

            ->allowEmptyString('password_reset_code')

            ->allowEmptyString('password_reset_expiry_timestamp')

            ->add('login_enabled', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('login_enabled')

            ->allowEmptyString('login_last_login_ip')

            ->allowEmptyString('login_last_login_host')

            ->add('login_last_login_datetime', 'valid', ['rule' => 'datetime'])
            ->allowEmptyDateTime('login_last_login_datetime')

            ->add('login_failure_count', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('login_failure_count')

            ->add('login_failure_datetime', 'valid', ['rule' => 'datetime'])
            ->allowEmptyDateTime('login_failure_datetime')

            ->add('block_enabled', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('block_enabled')

            ->allowEmptyString('block_reason')

            ->add('block_datetime', 'valid', ['rule' => 'datetime'])
            ->allowEmptyDateTime('block_datetime');

        return $validator;
    }

    /**
     * Validation rules for form login
     *
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationLogin(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('username')
            ->notEmptyString('username')
            ->requirePresence('password')
            ->notEmptyString('password');

        return $validator;
    }

    /**
     * Add new user
     *
     * @param array $data User data
     * @return \User\Model\Entity\User
     */
    public function add(array $data)
    {
        $user = $this->newEmptyEntity();
        $user->setAccess('*', true);
        $user->setAccess(['password1', 'password2'], true);
        $user->setAccess('password', false);

        $this->patchEntity($user, $data, ['validate' => 'add']);
        if ($user->getErrors()) {
            return $user;
        }

        $user->password = $user->password1;

        if ($this->save($user)) {
            Log::info('User added with ID ' . $user->id);
        }

        return $user;
    }

    /**
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    protected function validationNewPassword(Validator $validator)
    {
        $validator
            ->requirePresence('password1', 'create')
            ->notEmptyString('password1')
            ->add('password1', 'password', [
                'rule' => 'checkNewPassword1',
                'provider' => 'table',
                'message' => __d('user', 'Invalid password'),
            ])
            ->add('password1', 'password_strength', [
                'rule' => ['checkPasswordComplexity'],
                'provider' => 'table',
                'message' => __d('user', 'Weak password'),
            ])

            ->requirePresence('password2', 'create')
            ->notEmptyString('password2')
            ->add('password2', 'password', [
                'rule' => 'checkNewPassword2',
                'provider' => 'table',
                'message' => __d('user', 'Passwords do not match'),
            ]);

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    protected function validationEmail(Validator $validator)
    {
        $validator
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'email', [
                'rule' => ['email', true],
                'message' => __d('user', 'The provided email address is invalid'),
            ])
            ->add('email', 'email_blacklist', [
                'rule' => 'checkEmailBlacklist',
                'provider' => 'table',
                'last' => true,
            ]);

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    protected function validationUsername(Validator $validator)
    {
        $validator
            ->requirePresence('username', 'create')
            ->notEmptyString('username');

        if (static::$emailAsUsername) {
            // email validation for 'username'
            $validator
                ->add('username', 'email', [
                    'rule' => ['email', true],
                    'message' => __d('user', 'The provided email address is invalid'),
                ])
                ->add('email', 'email_blacklist', [
                    'rule' => 'checkEmailBlacklist',
                    'provider' => 'table',
                    'last' => true,
                ]);
        }

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationAdd(Validator $validator)
    {
        $validator = $this->validationDefault($validator);
        $validator = $this->validationNewPassword($validator);
        $validator = $this->validationEmail($validator);
        $validator = $this->validationUsername($validator);

        return $validator;
    }

    /**
     * @param \User\Model\Entity\User $user The user entity
     * @param null|string $secretKey Secret key for Google authenticator
     * @return \User\Model\Entity\User
     */
    public function setGoogleAuthSecret(User $user, $secretKey = null)
    {
        if ($secretKey === null) {
            $secretFactory = new \Dolondro\GoogleAuthenticator\SecretFactory();
            $secret = $secretFactory->create(Configure::read('GoogleAuthenticator.issuer'), $user->username);
            $secretKey = $secret->getSecretKey();
        }

        $user->gauth_secret = $secretKey;

        return $this->save($user);
    }

    /**
     * @param \User\Model\Entity\User $user The user entity
     * @return \Dolondro\GoogleAuthenticator\Secret
     */
    public function getGoogleAuthSecret(User $user)
    {
        if (!$user->gauth_secret) {
            return null;
        }

        $secret = new \Dolondro\GoogleAuthenticator\Secret(
            Configure::read('GoogleAuthenticator.issuer'),
            $user->username,
            $user->gauth_secret
        );

        return $secret;
    }

    /**
     * @param \User\Model\Entity\User $user The user entity
     * @return \User\Model\Entity\User
     */
    public function enableGoogleAuth(User $user)
    {
        /*
        $secret = $this->getGoogleAuthSecret($user);
        if (!$secret) {
            return false;
        }
        $secretKey = $secret->getSecretKey();
        $user->gauth_secret = $secretKey;
        */
        $user->gauth_enabled = true;

        return $this->save($user);
    }

    /**
     * @param \User\Model\Entity\User $user The user entity
     * @return \User\Model\Entity\User
     */
    public function disableGoogleAuth(User $user)
    {
        $user->gauth_enabled = false;
        //$user->gauth_secret = "";

        return $this->save($user);
    }

    /**
     * Create root user with default credentials
     *
     * @param string $email User email address
     * @param string $password User password
     * @return bool|\User\Model\Entity\User
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

        $user = $this->newEmptyEntity();
        $user->setAccess(array_keys($data), true);
        $this->patchEntity($user, $data);

        //@TODO Add validation
        if ($this->save($user)) {
            Log::info('User \'root\' added with ID ' . $user->id, ['admin', 'user']);
        }

        return $user;
    }

    /**
     * Register new user with form data array
     *
     * @param array $data User data
     * @param bool $dispatchEvent If True, trigger custom events (Default: True)
     * @return \User\Model\Entity\User
     */
    public function register(array $data, $dispatchEvent = true)
    {
        /** @var \User\Model\Entity\User $user */
        $user = $this->newEmptyEntity();
        $user->setAccess('*', false);
        $user->setAccess(['username', 'name', 'first_name', 'last_name', 'email', 'locale', 'timezone', 'currency'], true);
        $user->setAccess(['password1', 'password2'], true);
        $user->setAccess(['group_id'], true);

        // Login
        // By default registered users are allowed to log in
        $user->login_enabled = true; //@TODO Read from config
        $user->block_enabled = false;
        $user->password_force_change = false; //@TODO Read from config

        // No-Login
        // Creates a user with no password and login disabled
        $noLogin = isset($data['_nologin']) ? (bool)$data['_nologin'] : false; //@TODO Read from config
        if ($noLogin) {
            $user->login_enabled = false;

            $this->getValidator('register')
                ->allowEmptyString('password1')
                ->requirePresence('password1', false)
                ->allowEmptyString('password2')
                ->requirePresence('password2', false);

            $user->setAccess(['password1', 'password2'], false);
        }

        // Email-As-Username
        if (self::$emailAsUsername && isset($data['email']) /*&& !isset($data['username'])*/) {
            $data['username'] = $data['email'];
        } elseif (self::$emailAsUsername && isset($data['username']) && !isset($data['email'])) {
            $data['email'] = $data['username'];
        }

        // Name
        // @TODO first_name and last_name properties are deprecated
        if (isset($data['first_name']) && isset($data['last_name'])) {
            $data['name'] = sprintf('%s %s', $data['first_name'], $data['last_name']);
        }
        if (!isset($data['name']) && isset($data['username'])) {
            $data['name'] = $data['username'];
        }

        // Email verification
        $user->email_verified = false;
        $user->email_verification_required = !(bool)Configure::read('User.Signup.disableEmailVerification');
        $user->email_verification_code = self::generateRandomVerificationCode(self::$verificationCodeLength);
        $user->email_verification_expiry_timestamp = time() + DAY; // @TODO Read expiry offset from config

        // Locale
        if (!isset($data['locale'])) {
            $data['locale'] = I18n::getLocale();
        }
        if (!isset($data['timezone'])) {
            $data['timezone'] = date_default_timezone_get();
        }
        if (!isset($data['currency'])) {
            $data['currency'] = 'EUR';
        }

        // Event 'User.Model.User.beforeRegister'
        if ($dispatchEvent === true) {
            $this->getEventManager()->dispatch(new Event('User.Model.User.beforeRegister', $this, compact('user', 'data')));
        }

        // User data validation
        $this->patchEntity($user, $data, ['validate' => 'register']);
        if ($user->getErrors()) {
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
                $this->getEventManager()->dispatch(new Event('User.Model.User.register', $this, compact('user', 'data')));
            }
        }

        return $user;
    }

    /**
     * Validation rules for the register method
     *
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationRegister(Validator $validator)
    {
        $this->validationAdd($validator);
        $validator
            ->add('login_enabled', 'valid', ['rule' => 'boolean']);

        return $validator;
    }

    /**
     * Change user password
     * - Requires the current user password
     * - The new password MUST NOT match the current user password
     *
     * @param \User\Model\Entity\User|\User\Model\Table\EntityInterface $user The user entity
     * @param array $data User data
     * @return bool
     */
    public function changePassword(User &$user, array $data)
    {
        $user->setAccess('password0', true);
        $user->setAccess('password1', true);
        $user->setAccess('password2', true);

        /** @var \User\Model\Entity\User $user */
        $user = $this->patchEntity($user, $data, ['validate' => 'changePassword']);
        if ($user->getErrors()) {
            return false;
        }

        // validate current password
        if (!$user->getPasswordHasher()->check($data['password0'], $user->password)) {
            $user->setError('password0', ['password' => __d('user', 'This is not your current password')]);
            unset($user->password0);
            unset($user->password1);
            unset($user->password2);

            return false;
        }

        // new password should not match current password
        if (strcmp($user->password0, $user->password1) === 0) {
            $user->setError('password1', [
                'password' => __d('user', 'This is your current password. Please create a new one!'),
            ]);
            unset($user->password1);
            unset($user->password2);

            return false;
        }

        // apply new password
        $user->setAccess('password', true);
        $user->password = $data['password1'];
        $saved = $this->save($user);

        // cleanup
        unset($user->password0);
        unset($user->password1);
        unset($user->password2);
        #unset($user->password); // hide password

        return $saved ? true : false;
    }

    /**
     * Validation rules to change password
     *
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationChangePassword(Validator $validator)
    {
        $validator = $this->validationNewPassword($validator);
        $validator
            ->requirePresence('password0')
            ->notEmptyString('password0');

        return $validator;
    }

    /**
     * Reset user password
     *
     * @param \User\Model\Entity\User $user The user entity
     * @param array $data User data
     * @return \Cake\Datasource\EntityInterface
     * @throws \User\Exception\PasswordResetException
     */
    public function resetPassword(User $user, array $data)
    {
        $resetCode = $data['password_reset_code'] ?? null;
        if (!$resetCode) {
            throw new PasswordResetException(__d('user', 'Password reset code missing'));
        }

        if ($user->password_reset_expiry_timestamp && Chronos::now()->gt($user->password_reset_expiry_timestamp)) {
            throw new PasswordResetException(__d('user', 'Password reset code has expired'));
        }

        if ($user->password_reset_code != $resetCode) {
            throw new PasswordResetException(__d('user', 'Password reset code is invalid'));
        }

        $user->setAccess('*', false);
        $user->setAccess('password1', true);
        $user->setAccess('password2', true);
        $user = $this->patchEntity($user, $data, ['validate' => 'resetPassword']);
        if ($user->getErrors()) {
            return $user;
        }

        // apply new password
        $user->setAccess('password', true);
        $user->password = $data['password1'];

        // clean the reset codes
        $user->password_reset_code = null;
        $user->password_reset_expiry_timestamp = null;

        if (!$this->save($user)) {
            throw new \RuntimeException('Record UPDATE failed: User:' . $user->id);
        }

        // cleanup
        unset($user->password1);
        unset($user->password2);
        unset($user->password); // hide password

        return $user;
    }

    /**
     * Set user password
     *
     * @param \User\Model\Entity\User $user The user entity
     * @param array $data User data
     * @return bool
     * @throws \User\Exception\PasswordResetException
     */
    public function setPassword(User $user, array $data)
    {
        $user->setAccess('*', false);
        $user->setAccess('password1', true);
        $user->setAccess('password2', true);
        $user = $this->patchEntity($user, $data, ['validate' => 'newPassword']);
        if ($user->getErrors()) {
            return $user;
        }

        $user->password = $data['password1'];
        if (!$this->save($user)) {
            throw new \RuntimeException('Record UPDATE failed: User:' . $user->id);
        }

        return $user;
    }

    /**
     * Validation rules to reset password
     *
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationResetPassword(Validator $validator)
    {
        $validator = $this->validationNewPassword($validator);

        return $validator;
    }

    /**
     * Password Validation Rule
     *
     * @param mixed $value Check value
     * @param mixed $context Check context
     * @return bool|string
     */
    public function checkNewPassword1($value, $context)
    {
        $value = trim($value);

        // Check for weak password
        if (isset($context['data']['username']) && $value == $context['data']['username']) {
            return __d('user', 'Password can not be the same as your username');
        }

        return true;
    }

    /**
     * Password Complexity validation Rule
     *
     * @param mixed $value Check value
     * @param array $options Check options
     * @param mixed $context Check context
     * @return bool|string
     */
    public function checkPasswordComplexity($value, $options = [], $context = null)
    {
        if (func_num_args() == 2) {
            $context = $options;
            $options = [];
        }

        $defaults = [
            'allowedPattern' => '/^[A-Za-z0-9' . preg_quote(self::$passwordSpecialChars, '/') . ']+$/',
            'allowedSpecialChars' => self::$passwordSpecialChars,
            'minLength' => self::$passwordMinLength,
            'lowercase' => self::$passwordMinLowercase,
            'uppercase' => self::$passwordMinUppercase,
            'special' => self::$passwordMinSpecialChars,
            'numbers' => self::$passwordMinNumbers,
        ];
        $options = array_merge($defaults, $options);

        // Check password length
        if ($options['minLength'] > 0 && strlen($value) < $options['minLength']) {
            return __d('user', 'Password too short. Minimum {0} characters', $options['minLength']);
        }

        // Check for illegal characters
        if ($options['allowedPattern'] && !preg_match($options['allowedPattern'], $value)) {
            return __d('user', 'Only letters, numbers and {0} are valid', $options['allowedSpecialChars']);
        }

        if ($options['numbers'] > 0) {
            if (preg_match_all('#([0-9])#', $value) < $options['numbers']) {
                return __dn(
                    'user',
                    'Password must include at least one number!',
                    'Password must include at least {0} numbers!',
                    $options['numbers'],
                    $options['numbers']
                );
            }
        }

        if ($options['lowercase'] > 0) {
            if (preg_match_all('#([a-z])#', $value) < $options['lowercase']) {
                //return __d('user', "Password must include at least {0} lowercase letters!", $options['lowercase']);
                return __dn(
                    'user',
                    'Password must include at least one lowercase letter!',
                    'Password must include at least {0} lowercase letters!',
                    $options['lowercase'],
                    $options['lowercase']
                );
            }
        }

        if ($options['uppercase'] > 0) {
            if (preg_match_all('#([A-Z])#', $value) < $options['uppercase']) {
                //return __d('user', "Password must include at least {0} UPPERCASE letters!", $options['uppercase']);
                return __dn(
                    'user',
                    'Password must include at least one UPPERCASE letter!',
                    'Password must include at least {0} UPPERCASE letters!',
                    $options['uppercase'],
                    $options['uppercase']
                );
            }
        }

        if ($options['special'] > 0) {
            $pattern = '#([' . preg_quote($options['allowedSpecialChars'], '#') . '])#';
            if (preg_match_all($pattern, $value) < $options['special']) {
                //return __d('user', "Password must include at least {0} special characters! ({1})", $options['special'], $options['allowedSpecialChars']);
                return __dn(
                    'user',
                    'Password must include at least one special character! Allowed characters: {1}',
                    'Password must include at least {0} special characters! Allowed characters: {1}',
                    $options['special'],
                    $options['special'],
                    $options['allowedSpecialChars']
                );
            }
        }

        return true;
    }

    /**
     * Email blacklist validation Rule
     *
     * @param mixed $value Check value
     * @param array $options Check options
     * @param mixed $context Check context
     * @return bool|string
     */
    public function checkEmailBlacklist($value, $options = [], $context = null)
    {
        if (func_num_args() == 2) {
            $context = $options;
            $options = [];
        }

        $defaults = [
            'enabled' => false,
            'domainList' => false,
        ];
        $config = (array)Configure::read('User.Blacklist');
        $options = array_merge($defaults, $config, $options);

        if ($options['enabled'] === false) {
            return true;
        }

        if ($options['domainList'] === true) {
            $options['domainList'] = CONFIG . 'user/domain.blacklist.txt';
        }

        if (is_string($options['domainList'])) {
            $File = new File($options['domainList'], false);
            if (!$File->exists()) {
                return true;
            }

            $content = $File->read();

            $options['domainList'] = array_filter(explode("\n", $content), function ($row) {
                if (preg_match('/^\#/', $row) || strlen(trim($row)) == 0) {
                    return false;
                }

                return true;
            });
        }

        $check = [];
        if (is_array($options['domainList'])) {
            $check = array_filter(array_unique($options['domainList']), function ($row) use ($value) {
                if (preg_match('/\@' . $row . '$/', $value)) {
                    return true;
                }

                return false;
            });
        }

        if (!empty($check)) {
            return __d('user', 'Email or Domain not allowed');
        }

        return true;
    }

    /**
     * Password Verification Validation Rule
     *
     * @param mixed $value Check value
     * @param mixed $context Check context
     * @return bool
     */
    public function checkNewPassword2($value, $context)
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
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationActivate(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('email')
            ->notEmptyString('email')
            ->requirePresence('email_verification_code')
            ->notEmptyString('email_verification_code');

        return $validator;
    }

    /**
     * Activate user
     *
     * @param array $data User data
     * @return \User\Model\Entity\User|\Cake\Datasource\EntityInterface|bool
     * @todo Refactor with Form
     */
    public function activate(array $data = [])
    {
        $email = isset($data['email']) ? strtolower(trim($data['email'])) : null;
        $code = isset($data['email_verification_code']) ? trim($data['email_verification_code']) : null;
        $user = $this->find()->where(['email' => $email])->contain([])->first();

        if (!$user || strcmp(strtoupper($user->get('email_verification_code')), strtoupper($code)) !== 0) {
            return false;
        }

        $user->email_verified = true;
        if ($this->save($user)) {
            return $user;
        }

        return false;
    }

    /**
     * Update password reset code
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return \User\Model\Entity\User
     * @throws \RuntimeException
     */
    public function updatePasswordResetCode(User $user): User
    {
        // generate new reset codes
        $user->password_reset_code = self::generateRandomVerificationCode(self::$verificationCodeLength);
        $user->password_reset_expiry_timestamp = time() + self::$passwordResetExpiry; // 24h

        if (!$this->save($user)) {
            throw new \RuntimeException('UsersTable::updateResetCode FAILED for User with ID ' . $user->id);
        }

        return $user;
    }

    /**
     * Mark user deleted
     *
     * @param \User\Model\Entity\User $user The user entity
     * @param bool $dispatchEvent If True, trigger custom events (Default: True)
     * @return bool|mixed|\User\Model\Entity\User
     */
    public function markDeleted(User $user, $dispatchEvent = true)
    {
        $user->is_deleted = true;
        $user->login_enabled = false;
        $user->block_enabled = true;
        $user->block_reason = 'DELETED';
        $user->block_datetime = FrozenTime::now();

        if (!$this->save($user)) {
            throw new \RuntimeException('UsersTable::markDeleted: Record UPDATE failed: User:' . $user->id);
        }

        if ($dispatchEvent === true) {
            $this->getEventManager()->dispatch(new Event('User.Model.User.markedDeleted', $this, compact('user')));
        }

        return $user;
    }

    /**
     * Reset user marked as deleted
     *
     * @param \User\Model\Entity\User $user The user entity
     * @param bool $dispatchEvent If True, trigger custom events (Default: True)
     * @return bool|mixed|\User\Model\Entity\User
     */
    public function resetDeleted(User $user, $dispatchEvent = true)
    {
        $user->is_deleted = false;
        $user->login_enabled = true;
        $user->block_enabled = false;
        $user->block_reason = null;
        $user->block_datetime = null;

        if (!$this->save($user)) {
            throw new \RuntimeException('UsersTable::resetDeleted: Record UPDATE failed: User:' . $user->id);
        }

        if ($dispatchEvent === true) {
            $this->getEventManager()->dispatch(new Event('User.Model.User.resetDeleted', $this, compact('user')));
        }

        return $user;
    }

    /**
     * Resend email verification code
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return bool|mixed|\User\Model\Entity\User
     */
    public function updateEmailVerificationCode(User $user)
    {
        //@TODO Check if the verification code has expired. If so, create new verification code.
        return $user;
    }

    /**
     * Generate random string
     *
     * @param int $length Lenth of generated string
     * @return string
     */
    public static function generateRandomVerificationCode($length = 8)
    {
        //@TODO Make use of random_compat vendor lib
        return strtoupper(self::random_str($length));
    }

    /**
     * Generate email verification url from User entity
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return string Full URL
     */
    public static function buildEmailVerificationUrl(User $user)
    {
        return Router::url([
            'prefix' => false, 'plugin' => 'User', 'controller' => 'Signup', 'action' => 'activate',
            '?' => [
                'c' => base64_encode($user->email_verification_code),
                'm' => base64_encode($user->email),
            ]
        ], true);
    }

    /**
     * Generate password reset url from User entity
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return string Full URL
     */
    public static function buildPasswordResetUrl(User $user): string
    {
        return Router::url([
            'prefix' => false, 'plugin' => 'User', 'controller' => 'Password', 'action' => 'passwordReset',
            '?' => [
                'c' => base64_encode($user->password_reset_code),
                'u' => base64_encode($user->username),
            ]
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
     * @param int $length How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     * @throws \Exception
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
