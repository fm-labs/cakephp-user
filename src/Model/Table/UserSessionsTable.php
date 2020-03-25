<?php
declare(strict_types=1);

namespace User\Model\Table;

use Cake\Database\Schema\TableSchema as Schema;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserSessions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \User\Model\Entity\UserSession get($primaryKey, $options = [])
 * @method \User\Model\Entity\UserSession newEntity($data = null, array $options = [])
 * @method \User\Model\Entity\UserSession[] newEntities(array $data, array $options = [])
 * @method \User\Model\Entity\UserSession|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \User\Model\Entity\UserSession patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \User\Model\Entity\UserSession[] patchEntities($entities, array $data, array $options = [])
 * @method \User\Model\Entity\UserSession findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserSessionsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('user_sessions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'User.Users',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function _initializeSchema(Schema $table)
    {
        $table->setColumnType('geo_location', 'json');

        return $table;
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
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->allowEmptyString('login_provider');

        $validator
            ->allowEmptyString('client_ip');

        $validator
            ->allowEmptyString('geo_location');

        $validator
            ->allowEmptyString('sessionid');

        $validator
            ->allowEmptyString('sessiontoken');

        $validator
            //->dateTime('timestamp')
            ->requirePresence('timestamp', 'create')
            ->notEmptyString('timestamp');

        $validator
            //->dateTime('expires')
            ->allowEmptyString('expires');

        return $validator;
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
