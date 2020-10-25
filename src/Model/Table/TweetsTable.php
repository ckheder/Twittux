<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tweets Model
 *
 * @method \App\Model\Entity\Tweet newEmptyEntity()
 * @method \App\Model\Entity\Tweet newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Tweet[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tweet get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tweet findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Tweet patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tweet[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tweet|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tweet saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tweet[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tweet[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tweet[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tweet[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TweetsTable extends Table
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

        $this->setTable('tweets');
        $this->setDisplayField('id_tweet');
        $this->setPrimaryKey('id_tweet');

        $this->addBehavior('Timestamp');

        $this->hasMany('Commentaires');

        $this->hasMany('Aime');

        $this->hasMany('Partage');

        $this->belongsTo('Abonnements', [
                                          'foreignKey' => 'username',
                                          'bindingKey' => 'suivi'
                                        ]);

        $this->belongsTo('Users', [
                                    'foreignKey' => 'username',
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
            ->integer('id_tweet')
            ->requirePresence('id_tweet', 'create');

        $validator
            ->scalar('username')
            ->maxLength('username', 255)
            ->requirePresence('username', 'create')
            ->notEmptyString('username');

        $validator
            ->scalar('contenu_tweet')
            ->maxLength('contenu_tweet', 255)
            ->requirePresence('contenu_tweet', 'create')
            ->notEmptyString('contenu_tweet');

        $validator
            ->integer('nb_commentaire')
            ->notEmptyString('nb_commentaire');

        $validator
            ->integer('nb_partage')
            ->notEmptyString('nb_partage');

        $validator
            ->integer('nb_like')
            ->notEmptyString('nb_like');

        $validator
            ->boolean('private')
            ->notEmptyString('private');

        $validator
            ->boolean('allow_comment')
            ->notEmptyString('allow_comment');

        return $validator;
    }
}
