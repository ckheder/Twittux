<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Messagerie Model
 *
 * @method \App\Model\Entity\Messagerie newEmptyEntity()
 * @method \App\Model\Entity\Messagerie newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Messagerie[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Messagerie get($primaryKey, $options = [])
 * @method \App\Model\Entity\Messagerie findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Messagerie patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Messagerie[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Messagerie|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Messagerie saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Messagerie[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Messagerie[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Messagerie[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Messagerie[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MessagerieTable extends Table
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

        $this->setTable('messagerie');
        $this->setDisplayField('id_message');
        $this->setPrimaryKey('id_message');

        $this->addBehavior('Timestamp');
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
            ->integer('id_message')
            ->allowEmptyString('id_message', null, 'create');

        $validator
            ->scalar('user_message')
            ->maxLength('user_message', 50)
            ->requirePresence('user_message', 'create')
            ->notEmptyString('user_message');

        $validator
            ->scalar('message')
            ->requirePresence('message', 'create')
            ->notEmptyString('message');

        $validator
            ->integer('conversation')
            ->requirePresence('conversation', 'create')
            ->notEmptyString('conversation');

        return $validator;
    }
}
