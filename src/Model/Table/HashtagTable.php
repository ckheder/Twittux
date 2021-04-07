<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Hashtag Model
 *
 * @method \App\Model\Entity\Hashtag newEmptyEntity()
 * @method \App\Model\Entity\Hashtag newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Hashtag[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Hashtag get($primaryKey, $options = [])
 * @method \App\Model\Entity\Hashtag findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Hashtag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Hashtag[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Hashtag|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Hashtag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Hashtag[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hashtag[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hashtag[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hashtag[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class HashtagTable extends Table
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

        $this->setTable('hashtag');
        $this->setDisplayField('id_hashtag');
        $this->setPrimaryKey('id_hashtag');
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
            ->integer('id_hashtag')
            ->allowEmptyString('id_hashtag', null, 'create');

        $validator
            ->scalar('hashtag')
            ->requirePresence('hashtag', 'create')
            ->notEmptyString('hashtag');

        $validator
            ->integer('nb_post_hashtag')
            ->maxLength('nb_post_hashtag', 50)
            ->notEmptyString('nb_post_hashtag');

        return $validator;
    }
}
