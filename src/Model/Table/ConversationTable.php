<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Conversation Model
 *
 * @method \App\Model\Entity\Conversation newEmptyEntity()
 * @method \App\Model\Entity\Conversation newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Conversation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Conversation get($primaryKey, $options = [])
 * @method \App\Model\Entity\Conversation findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Conversation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Conversation[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Conversation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Conversation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Conversation[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Conversation[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Conversation[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Conversation[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ConversationTable extends Table
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

        $this->setTable('conversation');
        $this->setDisplayField('id_conv');
        $this->setPrimaryKey('id_conv');
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
            ->integer('id_conv')
            ->allowEmptyString('id_conv', null, 'create');

            $validator
                ->integer('conversation')
                ->requirePresence('conversation', 'create');


        $validator
            ->scalar('user_conv')
            ->maxLength('user_conv', 50)
            ->requirePresence('user_conv', 'create')
            ->notEmptyString('user_conv');

        $validator
            ->scalar('visible')
            ->maxLength('visible', 3)
            ->requirePresence('visible', 'create')
            ->notEmptyString('visible');

            $validator
                ->scalar('type_conv')
                ->maxLength('type_conv', 8)
                ->requirePresence('type_conv', 'create')
                ->notEmptyString('type_conv');

        return $validator;
    }
}
