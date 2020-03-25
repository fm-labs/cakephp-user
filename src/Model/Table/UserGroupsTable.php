<?php
namespace User\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use User\Model\Entity\Group;

/**
 * UserGroups Model
 */
class UserGroupsTable extends UserBaseTable
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('user_groups');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->hasMany('Users', [
            'foreignKey' => 'group_id',
            'className' => 'User.Users',
        ]);
//        $this->belongsToMany('Users', [
//            'foreignKey' => 'group_id',
//            'targetForeignKey' => 'user_id',
//            'joinTable' => 'user_groups_users',
//            'className' => 'User.Users'
//        ]);
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
            ->allowEmptyString('id', 'create')
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->allowEmptyString('password');

        return $validator;
    }
}
