<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Aime Model
 *
 * @method \App\Model\Entity\Aime newEmptyEntity()
 * @method \App\Model\Entity\Aime newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Aime[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Aime get($primaryKey, $options = [])
 * @method \App\Model\Entity\Aime findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Aime patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Aime[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Aime|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Aime saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Aime[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Aime[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Aime[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Aime[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AimeTable extends Table
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

        $this->setTable('aime');
        $this->setDisplayField('id_like');
        $this->setPrimaryKey('id_like');

        $this->addBehavior('CounterCache', [
            'Tweets' => ['nb_like']]);

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
            ->integer('id_like')
            ->allowEmptyString('id_like', null, 'create');

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
