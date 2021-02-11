<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserConversation Model
 *
 * @method \App\Model\Entity\UserConversation newEmptyEntity()
 * @method \App\Model\Entity\UserConversation newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\UserConversation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserConversation get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserConversation findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\UserConversation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserConversation[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserConversation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserConversation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserConversation[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserConversation[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserConversation[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserConversation[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class UserConversationTable extends Table
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

        $this->setTable('userconversation');
        $this->setDisplayField('id_user_conv');
        $this->setPrimaryKey('id_user_conv');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id_user_conv')
            ->allowEmptyString('id_user_conv', null, 'create');

        $validator
            ->scalar('user_conv')
            ->maxLength('user_conv', 50)
            ->requirePresence('user_conv', 'create')
            ->notEmptyString('user_conv');

        $validator
            ->integer('conversation')
            ->requirePresence('conversation', 'create')
            ->notEmptyString('conversation');

        $validator
            ->scalar('visible')
            ->maxLength('visible', 3)
            ->requirePresence('visible', 'create')
            ->notEmptyString('visible');

        return $validator;
    }
}
