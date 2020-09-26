<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;

/**
 * Tweets Controller
 *
 * @property \App\Model\Table\TweetsTable $Tweets
 * @method \App\Model\Entity\Tweet[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TweetsController extends AppController
{

            public $paginate = [
        'limit' => 8,
                            ];

        public function initialize() : void
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadModel('Commentaires');
    }

    /**
     * méthode Index : récupération des tweets de l'utilisateur donné en URL
     *
     */
        public function index()
    {

        $current_user = $this->request->getParam('username'); // récupération de l'utilisateur en URL

        $this->set('title' , ''.$current_user.'| Twittux'); // titre de la page

        $this->viewBuilder()->setLayout('tweet'); // définition du layout

        // récupération des tweets

        $tweets = $this->paginate($this->Tweets->find()
                                                ->select(['id_tweet','user_tweet','contenu_tweet','created','nb_commentaire','nb_partage','nb_like'])
                                                ->where(['user_timeline' => $current_user])
                                                ->order(['created' => 'DESC'])

                                                        );

        $this->set(compact('tweets'));
    }

    /**
     * Méthode View : visualisation d'un tweet et de ses commentaires
     *
     * Paramètres : $id -> identifiant donné en URL
     * 
     */
        public function view()
    {

        $id = $this->request->getParam('id');

        $this->viewBuilder()->setLayout('comment'); // définition du layout

        $tweet = $this->Tweets->get($id); //récupération du tweet

        $this->set('tweet', $tweet);

        $this->set('title',''.$tweet->user_timeline.' : '.$tweet->contenu_tweet.''); // on reprend le début du tweet pour en faire un titre

        //récupération des commentaires par odre décroissant

        $commentaires = $this->paginate($this->Commentaires->find()->select(['id_comm','commentaire','username','created'])
                                    ->where(['Commentaires.id_tweet' => $id])
                                    ->order(['created' => 'DESC']));

        $this->set(compact('commentaires'));                         

    }

    /**
     * méthode add : ajout d'un nouveau tweet
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
        public function add()
    {
            if ($this->request->is('ajax')) // requête AJAX uniquement
        {
            
            $tweet = $this->Tweets->newEmptyEntity(); // création d'une nouvelle entité

            $contenu_tweet = strip_tags($this->request->getData('contenu_tweet')); // suppression des tags éventuels
        
            $idtweet = $this->idtweet(); // génération d'un nouvel identifiant de tweet

            $data = array(
                            'id_tweet' => $idtweet,
                            'user_tweet' => $this->Auth->user('username'),
                            'user_timeline' => $this->Auth->user('username'),
                            'contenu_tweet' => AppController::linkify_content($contenu_tweet),
                            'nb_commentaire' =>0,
                            'nb_partage' =>0,
                            'nb_like' =>0,
                            'private' =>0,
                            'allow_comment' => 0
                            );

            $tweet = $this->Tweets->patchEntity($tweet, $data); // sauvegarde de la nouvelle entité


                if ($this->Tweets->save($tweet)) 
            {

                // suppression des lignes du tableau data non nécessaires à l'affichage du tweet

                unset($tweet["user_timeline"], $tweet["nb_partage"] , $tweet["private"], $tweet["allow_comment"]);

                // renvoi d'une réponse JSON

                return $this->response->withType("application/json")->withStringBody(json_encode($tweet));
            }       
        }
     
            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }

    }

    /**
     * Méthode Delete : suppression d'un tweet
     *
     */
        public function delete($id = null)
    {
           if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            $statement = ConnectionManager::get('default')->prepare(
                        'DELETE FROM tweets WHERE id_tweet = :id_tweet AND user_tweet = :user_tweet');


            $statement->bindValue('id_tweet', $this->request->getParam('id'), 'integer');
            $statement->bindValue('user_tweet', $this->Auth->user('username'), 'string');
            $statement->execute();

            $rowCount = $statement->rowCount(); 

            if ($rowCount == 1) { // la ligne à était supprimée

                    return $this->response->withStringBody('tweetsupprime'); //renvoid'une réponse au format TEXT
            }
            elseif ($rowCount == 0) { // ligne non suppriméeou inexistante

                return $this->response->withStringBody('tweetnonsupprime'); // renvoi d'une réponse au format TEXT
            }
    }
                else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }

    }

    /**
     * Méthode actualités : affichage des tweets des gens que je suis
     *
     */

        public function actualites()
    {

        $this->viewBuilder()->setLayout('news');

        $this->set('title', 'Twittux | Actualités'); // titre de la page

        // Récupération des tweets de mes abonnements par odre décroissant

                $actu = $this->Tweets->find()
                                        ->select([
                                                    'Tweets.id_tweet',
                                                    'Tweets.user_tweet',
                                                    'Tweets.contenu_tweet',
                                                    'Tweets.created',
                                                    'Tweets.nb_commentaire',
                                                    'Tweets.nb_partage',
                                                    'Tweets.nb_like',      
                                                    ])

                                            ->where(['Abonnements.suiveur' =>  $this->Auth->user('username') ])
                                            ->contain(['Users'])
                                            ->contain(['Abonnements']);

            if($actu->isEmpty()) // aucun résultat
        {
            $this->set('no_actu', 0);
        }
            else // pagination des résultats  
        {
            $this->set('actu', $this->paginate($actu, ['limit' => 8]));
        }    

    }

    /**
     * Méthode Idtweet
     *
     * Calcul d'un id de tweet aléatoire
     *
     * Sortie : $idtweet -> id de tweet
     *
     *
*/
        private function idtweet()
    {

        $idtweet = rand();

        // on vérifie si il existe déjà

        $query = $this->Tweets->find()
                                ->select(['id_tweet'])
                                ->where(['id_tweet' => $idtweet]);

                if ($query->isEmpty())
            {
                return $idtweet;
            }
                else
            {
                idtweet(); // ou $this->idtweet();
            }
    }
}
