<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Partage Model
 *
 * @method \App\Model\Entity\Partage newEmptyEntity()
 * @method \App\Model\Entity\Partage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Partage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Partage get($primaryKey, $options = [])
 * @method \App\Model\Entity\Partage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Partage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Partage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Partage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Partage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Partage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Partage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Partage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Partage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class PartageTable extends Table
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

        $this->setTable('partage');
        $this->setDisplayField('id_partage');
        $this->setPrimaryKey('id_partage');

                $this->addBehavior('CounterCache', [
            'Tweets' => ['nb_partage']]);

        $this->belongsTo('Tweets', [
            'foreignKey' => 'id_tweet',
            'dependent' => true

        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'username',
            'dependent' => true

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
            ->integer('id_partage')
            ->allowEmptyString('id_partage', null, 'create');

        $validator
            ->scalar('username')
            ->maxLength('username', 50)
            ->requirePresence('username', 'create')
            ->notEmptyString('username');

        $validator
            ->integer('id_tweet')
            ->requirePresence('id_tweet', 'create')
            ->notEmptyString('id_tweet');

        return $validator;
    }


}
