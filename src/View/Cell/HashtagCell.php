<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Hashtag cell
 */
class HashtagCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
     public function initialize(): void
    {
      $this->loadModel('Hashtag');
    }

    /**
     * Affichage des 5 hashtags les plus populaire sur les profils et la page d'actualité
     *
     * @return void
     */
     public function display()
    {
      $list_hashtag = $this->Hashtag->find()->select(['hashtag','nb_post_hashtag'])
                                          ->order(['nb_post_hashtag' => 'DESC'])
                                          ->limit(5);

      $this->set('list_hashtag', $list_hashtag);
    }
}
