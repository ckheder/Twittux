<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Abonnements Model
 *
 * @method \App\Model\Entity\Abonnement newEmptyEntity()
 * @method \App\Model\Entity\Abonnement newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Abonnement[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Abonnement get($primaryKey, $options = [])
 * @method \App\Model\Entity\Abonnement findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Abonnement patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Abonnement[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Abonnement|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Abonnement saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Abonnement[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Abonnement[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Abonnement[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Abonnement[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AbonnementsTable extends Table
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

        $this->setTable('abonnements');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        
       $this->belongsTo('Users', [
            'foreignKey' => 'suivi',
            'bindingKey' => 'username'
            
        ]);
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('suiveur')
            ->maxLength('suiveur', 255)
            ->requirePresence('suiveur', 'create')
            ->notEmptyString('suiveur');

        $validator
            ->scalar('suivi')
            ->maxLength('suivi', 255)
            ->requirePresence('suivi', 'create')
            ->notEmptyString('suivi');

        $validator
            ->requirePresence('etat', 'create')
            ->notEmptyString('etat');

        return $validator;
    }
}
