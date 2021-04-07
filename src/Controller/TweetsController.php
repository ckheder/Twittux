<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use App\Event\TweetsListener;
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
        $this->loadModel('Partage');
        $this->loadModel('Abonnements');
        $this->loadModel('Users');
        $this->loadModel('Settings');

        //listener qui va écouté la création d'un nouveau tweet

        $TweetsListener = new TweetsListener();

        $this->getEventManager()->on($TweetsListener);

        $this->Authentication->allowUnauthenticated(['view', 'index','mediatweet']); // on autorise les gens non auth à voir les profil public
    }

    /**
     * méthode Index : récupération des tweets de l'utilisateur donné en URL
     *
     */
        public function index()
    {

      $current_user = $this->request->getParam('username'); // récupération de l'utilisateur en URL

      if ($this->request->is('ajax')) // si la requête est de type AJAX, on charge la layout spécifique
  {
      $this->viewBuilder()->setLayout('ajax');
  }
      else
  {
      $this->viewBuilder()->setLayout('tweet'); // sinon le layout 'tweet'
      $this->set('title' , ''.$current_user.' | Twittux'); // titre de la page
  }

  // si je suis connecter

    if($this->Authentication->getIdentity())
  {

      if($current_user != $this->Authentication->getIdentity()->username) // si je ne suis pas sur mon profil
    {

          if($this->verif_user($current_user) == 0) // on vérifie si l'utilisateur existe
        {
          throw new NotFoundException();
        }

    // on vérifie si je peux voir le profil

      if(AppController::get_type_profil($current_user) == 'prive' AND $this->check_abo($current_user) == 0)
    {
      $no_see = 1; // interdiction de voir

      $this->set('no_see', $no_see);
    }

  }
}

// si je ne suis pas authentifié et que le profil est privé

  elseif (!$this->Authentication->getIdentity() AND AppController::get_type_profil($current_user) == 'prive')
{
  $no_see = 1; // interdiction de voir

  $this->set('no_see', $no_see);
}
    if(!isset($no_see)) // si je suis abonné ou profil public , on récupère la liste des tweets
  {
        // récupération des tweets

        $tweets = $this->paginate($this->Tweets->find()
                                                ->select(['id_tweet','username','contenu_tweet','created','nb_commentaire','nb_partage','nb_like'])
                                                            ->leftjoin(
                    ['Partage'=>'partage'],
                    ['Tweets.id_tweet = (Partage.id_tweet)']
                    )
                                                ->where([
                                                    'OR' => ['Tweets.username' => $current_user,'Partage.username' => $current_user]])
                                                ->order(['created' => 'DESC'])


                                                        );

        $this->set(compact('tweets'));
    }
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

        $tweet = $this->Tweets->get($id); // on récupère les infos du tweet

        if($this->Authentication->getIdentity())
        {

          if($tweet->username != $this->Authentication->getIdentity()->username) // si je ne suis pas l'auteur du tweet
        {

            if(AppController::get_type_profil($tweet->username) == 'prive' AND $this->check_abo($tweet->username) == 0) // sur profil prive et non abonné
          {

            $no_see = 1; // interdiction de voir
            $this->set('title','Tweet privé'); // nouveau titre
            $this->set('no_see', $no_see); // envoi d'une varibale d'information
            $this->set('user_tweet', $tweet->username); // renvoi du nom de l'auteur pour message personnalisé

          }
    }
}

// si je ne suis pas authentifié et que le profil est privé

  elseif (!$this->Authentication->getIdentity() AND AppController::get_type_profil($tweet->username) == 'prive')
{
  $no_see = 1; // interdiction de voir

  $this->set('no_see', $no_see);

  $this->set('title','Tweet privé'); // nouveau titre

  $this->set('user_tweet', $tweet->username); // renvoi du nom de l'auteur pour message personnalisé

}
        if(!isset($no_see)) // si je suis abonné ou profil public , on récupère la liste des tweets
      {

        $this->set('tweet', $tweet);

        $titre = strip_tags($tweet->contenu_tweet);

        $this->set('title',''.$tweet->username.' : '.$titre.''); // on reprend le début du tweet pour en faire un titre

        //récupération des commentaires par odre décroissant

        $commentaires = $this->paginate($this->Commentaires->find()->select(['id_comm','commentaire','username','created'])
                                    ->where(['Commentaires.id_tweet' => $id])
                                    ->order(['created' => 'DESC']));

        $this->set(compact('commentaires'));

    }
  }

  /**
   * Méthode média : afficher les tweets contenant un média uploadé
   *
   * Paramètres : $id -> identifiant donné en URL
   *
   */
      public function mediatweet()
  {

        $current_user = $this->request->getParam('username');

        if ($this->request->is('ajax')) // si la requête est de type AJAX, on charge la layout spécifique
    {
      $this->viewBuilder()->setLayout('ajax');
    }
      else
    {

    $this->viewBuilder()->setLayout('tweet'); // sinon le layout 'tweet'

    $this->set('title' , ''.$current_user.' | Media'); // titre de la page

    }

// si je suis authentifié

    if($this->Authentication->getIdentity())
{

    if($current_user != $this->Authentication->getIdentity()->username) // si je ne suis pas sur mon profil
  {

  if($this->verif_user($current_user) == 0) // on vérifie si l'utilisateur existe
{
  throw new NotFoundException();
}
// on vérifie si je peux voir le profil

  if(AppController::get_type_profil($current_user) == 'prive' AND $this->check_abo($current_user) == 0)
{

  $no_see = 1; // interdiction de voir

  $this->set('no_see', $no_see);

}

}
}

// si je ne suis pas authentifié et que le profil est privé

  elseif (!$this->Authentication->getIdentity() AND AppController::get_type_profil($current_user) == 'prive')
{
  $no_see = 1; // interdiction de voir

  $this->set('no_see', $no_see);

}
  if(!isset($no_see)) // si je suis abonné ou profil public , on récupère la liste des tweets
{

    // on récupère toutes les informations du tweets contenant #mot-clé

    $this->set('media_tweet', $this->paginate($this->Tweets->find()->select([
                                                                                    'Tweets.id_tweet',
                                                                                    'Tweets.username',
                                                                                    'Tweets.contenu_tweet',
                                                                                    'Tweets.created',
                                                                                    'Tweets.nb_commentaire',
                                                                                    'Tweets.nb_partage',
                                                                                    'Tweets.nb_like',
                                                    ])
                                            ->where(['Tweets.username' => $this->request->getParam('username'),'Tweets.contenu_tweet REGEXP' => '<img.+?class=".*?media_tweet.*?"'])
                                            ->order(['created' => 'DESC'])
                                          ));


    }

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

            if(AppController::get_type_profil($this->Authentication->getIdentity()->username) == 'prive') // su profil prive et non abonné
          {

            $private = 1; // tweet prive
          }
            else
          {
            $private = 0;
          }
            $idtweet = $this->idtweet(); // génération d'un nouvel identifiant de tweet

            // si présence d'un média , on le traite

            if($this->request->getData('tweetmedia')->getError() != 4)
          {

            $contenu_tweet = AppController::uploadfiletweet($this->request->getData('tweetmedia'), $contenu_tweet,$idtweet); // traitement de l'envoi du fichier et mise à jour du contenu du tweets

          }

            $data = array(
                            'id_tweet' => $idtweet,
                            'username' => $this->Authentication->getIdentity()->username,
                            'contenu_tweet' => AppController::linkify_content($contenu_tweet),
                            'nb_commentaire' =>0,
                            'nb_partage' =>0,
                            'nb_like' =>0,
                            'private' =>$private,
                            'allow_comment' => 0
                            );



            $tweet = $this->Tweets->patchEntity($tweet, $data); // sauvegarde de la nouvelle entité


                if ($this->Tweets->save($tweet))
            {

                // suppression des lignes du tableau data non nécessaires à l'affichage du tweet

                unset($tweet["private"], $tweet["allow_comment"]);

                // déclenchement d'un évènement destiné à voir si des utilisateurs sont mentionnés

                $event = new Event('Model.Tweets.afteradd', $this, ['data' => $data]);

                $this->getEventManager()->dispatch($event);

                // renvoi d'une réponse JSON

                return $this->response->withType("application/json")->withStringBody(json_encode($tweet));
            }
              else
            {
              return $this->response->withType('application/json')
                                      ->withStringBody(json_encode(['result' => 'notweet']));
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

            $idtweet = $this->request->input('json_decode');

            $statement = ConnectionManager::get('default')->prepare(
                        'DELETE FROM tweets WHERE id_tweet = :id_tweet AND username = :username');


            $statement->bindValue('id_tweet', $idtweet, 'integer');
            $statement->bindValue('username', $this->Authentication->getIdentity()->username, 'string');
            $statement->execute();

            $rowCount = $statement->rowCount();

            if ($rowCount == 1) // la ligne à était supprimée
          {

              // suppression d'un éventuel média associé

              $name = WWW_ROOT . 'img/media/'.$this->Authentication->getIdentity()->username.'/'.$idtweet.''; // media contenant l'id du tweet supprimé

              $files = glob($name . '*');

                if($files) // si ce fichier existe, on le supprime
              {

                unlink($files[0]);

              }
                    return $this->response->withStringBody('tweetsupprime'); //renvoid'une réponse au format TEXT
          }
            elseif ($rowCount == 0) // ligne non supprimée ou inexistante
          {
                return $this->response->withStringBody('tweetnonsupprime'); // renvoi d'une réponse au format TEXT
          }
    }
                else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }

    }

    /**
     * Méthode actualités : affichage des tweets des gens que je suis ainsi que les éventuels post qu'ils auraient partagé
     *
     */

        public function actualites()
    {

      if ($this->request->is('ajax')) // si la requête est de type AJAX, on charge la layout spécifique
  {
      $this->viewBuilder()->setLayout('ajax');
  }
      else
  {
    $this->viewBuilder()->setLayout('news');

    $this->set('title', 'Twittux | Actualités'); // titre de la page

  }

        // Récupération de mes abonnements

       $abonnement_valide = $this->Abonnements->find()

                                                ->select(['suivi'])

                                                ->where(['Abonnements.suiveur' =>  $this->Authentication->getIdentity()->username, 'etat' => 1]);

        // Récupération des posts ddes personnes se trouvant dans les résultats de la requête précédente

            $actu = $this->Tweets->find()
                                        ->select([
                                                    'Tweets.id_tweet',
                                                    'Tweets.username',
                                                    'Tweets.contenu_tweet',
                                                    'Tweets.created',
                                                    'Tweets.nb_commentaire',
                                                    'Tweets.nb_partage',
                                                    'Tweets.nb_like',
                                                    'Partage.username',
                                                    ])

                                        ->leftjoin(
                                                    ['Partage'=>'partage'],
                                                    ['Tweets.id_tweet = (Partage.id_tweet)']
                                                    )

                                        ->where([
                                                'OR' => ['Tweets.username IN' => $abonnement_valide,'Partage.username IN' => $abonnement_valide]]);

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

    /**
         * Méthode Verifuser
         *
         * Vérifie si l'utilisateur existe
         *
         * Paramètre : $username -> nom à tester
         *
         * Sortie : 0 -> membre inexistant | 1 -> membre existant
         *
         *
    */
            private function verif_user($username)
        {

            $check_user = $this->Users->find()
                                        ->where(['username' => $username ])
                                        ->count();

            return $check_user;
        }


            /**
                 * Méthode check_abo
                 *
                 * Récupération du type de profil privé ou public
                 *
                 *
                 * Sortie : 0 -> aucn abonnement | 1 -> abonnement existant et validé
                 *
                 *
            */
                  private function check_abo($username)
                {
                  $check_abo = $this->Abonnements->find()

                                                    ->where(['suiveur' => $this->Authentication->getIdentity()->username, 'suivi' => $username, 'etat' => 1])

                                                    ->count();

                    return $check_abo;
                }


}
