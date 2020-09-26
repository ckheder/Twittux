<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Commentaires Model
 *
 * @method \App\Model\Entity\Commentaire newEmptyEntity()
 * @method \App\Model\Entity\Commentaire newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Commentaire[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Commentaire get($primaryKey, $options = [])
 * @method \App\Model\Entity\Commentaire findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Commentaire patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Commentaire[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Commentaire|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Commentaire saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Commentaire[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Commentaire[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Commentaire[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Commentaire[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommentairesTable extends Table
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

        $this->setTable('commentaires');
        $this->setDisplayField('id_comm');
        $this->setPrimaryKey('id_comm');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Tweets')
            ->setForeignKey('id_tweet');

        

        $this->addBehavior('CounterCache', [
            'Tweets' => ['nb_commentaire']]);
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
            ->integer('id_comm')
            ->allowEmptyString('id_comm', null, 'create');

        $validator
            ->scalar('commentaire')
            ->requirePresence('commentaire', 'create')
            ->notEmptyString('commentaire');

        $validator
            ->integer('id_tweet');


        $validator
            ->scalar('username')
            ->maxLength('username', 50)
            ->requirePresence('username', 'create')
            ->notEmptyString('username');

        return $validator;
    }

}
