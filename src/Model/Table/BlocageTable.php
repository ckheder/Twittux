<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Blocage Model
 *
 * @method \App\Model\Entity\Blocage newEmptyEntity()
 * @method \App\Model\Entity\Blocage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Blocage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Blocage get($primaryKey, $options = [])
 * @method \App\Model\Entity\Blocage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Blocage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Blocage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Blocage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Blocage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Blocage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Blocage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Blocage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Blocage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class BlocageTable extends Table
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

        $this->setTable('blocage');
        $this->setDisplayField('id_blocage');
        $this->setPrimaryKey('id_blocage');
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
            ->integer('id_blocage')
            ->allowEmptyString('id_blocage', null, 'create');

        $validator
            ->scalar('bloqueur')
            ->maxLength('bloqueur', 50)
            ->requirePresence('bloqueur', 'create')
            ->notEmptyString('bloqueur');

        $validator
            ->scalar('bloque')
            ->maxLength('bloque', 50)
            ->requirePresence('bloque', 'create')
            ->notEmptyString('bloque');

        return $validator;
    }
}
