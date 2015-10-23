<?php
namespace User\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
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

    public static $emailAsUsername = true;

    public static $passwordRegex = '/^(\w)+$/';

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


    public function validationAdd(Validator $validator)
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
            //->requirePresence('is_login_allowed', 'create')
            ->notEmpty('is_login_allowed');


        if (static::$emailAsUsername) {
            $validator->add('username', 'email', [
                'rule' => ['email'],
                'message' => __('The provided email address is invalid')
            ]);
        }


        return $validator;
    }

    public function add(array $data)
    {
        $user = $this->newEntity(null);
        $user->accessible('username', true);
        $user->accessible('password1', true);
        $user->accessible('password2', true);
        $this->patchEntity($user, $data, ['validate' => 'add']);
        if ($user->errors()) {
            return $user;
        }

        $user->password = $user->password1;

        if ($this->save($user)) {
            Log::info('[plugin:backend] User added with ID ' . $user->id);
        }
        return $user;
    }

    public function createRootUser()
    {
        // check if there is already a root user
        if ($this->find()->where(['id' => 1])->first()) {
            return false;
        }

        $data = [
            'id' => 1,
            'name' => 'root',
            'username' => 'root',
            'email' => 'change_me@example.org',
            'password' => 'change_me',
            'login_enabled' => true,
            'email_verification_required' => false,
        ];

        $user = $this->newEntity();
        $user->accessible([
            'id', 'name', 'username', 'email', 'password', 'login_enabled', 'email_verification_required'
        ], true);
        $this->patchEntity($user, $data);

        if ($this->save($user)) {
            Log::info('[plugin:backend] ROOT User added with ID ' . $user->id);
        }

        return $user;
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
            ->add('login_enabled', 'valid', ['rule' => 'boolean'])
            ->requirePresence('login_enabled', 'create')
            ->notEmpty('login_enabled');

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
            $user->errors('password1', [
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
