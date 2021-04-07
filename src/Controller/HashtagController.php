<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Hashtag Controller
 *
 * @property \App\Model\Table\HashtagTable $Hashtag
 * @method \App\Model\Entity\Hashtag[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
 class HashtagController extends AppController
{

  // 10 résultats avant de paginer

  public $paginate = [
                      'limit' => 10,
                      ];

  public function initialize() : void
{
    parent::initialize();

    $this->loadComponent('Paginator');

    $this->Authentication->allowUnauthenticated(['index']); // on autorise les gens non auth à voir les hashtags

}
    /**
     * Index method : récupération et pagination de tous les hashtags
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
     public function index()
    {

      $this->viewBuilder()->setLayout('hashtag');

      $this->set('title' , 'Tendances | Twittux'); // titre de la page

      // récupération des hashtags

      $hashtags = $this->paginate($this->Hashtag->find()
                                                ->order(['nb_post_hashtag' => 'DESC']));

      $this->set(compact('hashtags'));

    }

}
