<?php
namespace App\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;

/**
 * Controller Search
 *
 * Moteur de recherche
 *
 */
    class SearchController extends AppController
{

        public $paginate = [
                            'limit' => 8,
                            ];



            public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Users');
        $this->loadModel('Tweets');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'media','hashtag','mediahashtag','searchusers','userhashtag']); // on autorise les gens non identifiés à accéder au moteur de recherche
    }

        /**
     * Méthode Index
     *
     * Recherche les tweets correspondant au mot-clé
     *
     */

        public function index()
    {

            if ($this->request->is('ajax')) // si la requête est de type AJAX, on charge la layout spécifique
        {

            $this->viewBuilder()->setLayout('ajax');
        }
            else
        {
            $this->viewBuilder()->setLayout('search'); // sinon le layout 'search'
        }

            $keyword = $this->request->GetParam('query'); // mot-clé pour la recherche

            $this->set('title', ''.$keyword.' - Recherche sur Twittux'); // titre de la page

            // on récupère toutes les informations du tweets contenant le mot clé ou si l'auteur est le mot clé

            $this->set('query_tweet', $this->paginate($this->Tweets->find()->select([
                                                                                        'Tweets.id_tweet',
                                                                                        'Tweets.username',
                                                                                        'Tweets.contenu_tweet',
                                                                                        'Tweets.created',
                                                                                        'Tweets.nb_commentaire',
                                                                                        'Tweets.nb_partage',
                                                                                        'Tweets.nb_like',
                                                                                    ])
                                                ->where(['MATCH (Tweets.contenu_tweet,Tweets.username) AGAINST(:search)'])
                                                ->where(['private' => 0]) // on ne cherche que les tweets publics
                                                ->bind(':search', $keyword)));

    }

    /**
     * Méthode média : afficher les tweets contenant un média uploadé
     *
     * Paramètres : $id -> identifiant donné en URL
     *
     */
        public function media()
    {

        if ($this->request->is('ajax')) // si la requête est de type AJAX, on charge la layout spécifique
      {
        $this->viewBuilder()->setLayout('ajax');
      }
        else
      {
        $this->viewBuilder()->setLayout('search'); // sinon le layout 'search'
      }

      $keyword = $this->request->GetParam('query'); // mot-clé pour la recherche

      // on récupère toutes les informations du tweets contenant #mot-clé

      $this->set('resultat_tweet_media', $this->paginate($this->Tweets->find()->select([
                                                                                      'Tweets.id_tweet',
                                                                                      'Tweets.username',
                                                                                      'Tweets.contenu_tweet',
                                                                                      'Tweets.created',
                                                                                      'Tweets.nb_commentaire',
                                                                                      'Tweets.nb_partage',
                                                                                      'Tweets.nb_like',
                                                      ])
                                              ->where(['Tweets.contenu_tweet REGEXP' => '<img.+?class=".*?media_tweet.*?"','MATCH (Tweets.contenu_tweet,Tweets.username) AGAINST(:search)'])
                                              ->where(['private' => 0])
                                              ->bind(':search', $keyword)));

      }

      /**
     * Méthode searchusers
     *
     * Recherche les utilisateurs dont le nom commence par le mot-clé
     *
     */

        public function searchusers()
    {

            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            $this->viewBuilder()->setLayout('ajax'); // chargement du layout AJAX

            $keyword = $this->request->GetParam('query'); // mot-clé pour la recherche

            $this->set('title', ''.$keyword.' - Recherche sur Twittux'); // titre de la page

            // recherche dans les users

            $this->set('query_users', $this->paginate($this->Users->find()->select([
                                                                                        'Users.username',
                                                                                        'Users.description',
                                                                                    ])
                                            ->where(['Users.username LIKE '  => ''.$keyword.'%'])));
        }
        else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }

    }

            /**
     * Méthode hashtag
     *
     * Recherche les tweets contenant le #$keyword
     *
     */

        public function hashtag()
    {
            if ($this->request->is('ajax')) // si la requête est de type AJAX, on charge la layout spécifique
        {
            $this->viewBuilder()->setLayout('ajax');
        }
            else
        {
            $this->viewBuilder()->setLayout('search');
        }

        $keyword = preg_replace('/#([^\s]+)/','$1',$this->request->GetParam('query')); // suppression du # dans le mot clé

        $this->set('title', 'Hashtag '.$keyword.' sur Twittux'); // titre de la page

        // on récupère toutes les informations du tweets contenant #mot-clé

        $this->set('resultat_tweet', $this->paginate($this->Tweets->find()->select([
                                                                                        'Tweets.id_tweet',
                                                                                        'Tweets.username',
                                                                                        'Tweets.contenu_tweet',
                                                                                        'Tweets.created',
                                                                                        'Tweets.nb_commentaire',
                                                                                        'Tweets.nb_partage',
                                                                                        'Tweets.nb_like',
                                                        ])
                                                ->where(['Tweets.contenu_tweet REGEXP' => '#[[:<:]]'.$keyword.'[[:>:]]'])
                                                ->where(['private' => 0])));

    }

    /**
* Méthode mediahashtag
*
* Recherche les tweets avec média (photo) contenant le #$keyword
*
*/

        public function mediahashtag()
      {
          if ($this->request->is('ajax')) // si la requête est de type AJAX, on charge la layout spécifique

        {
          $this->viewBuilder()->setLayout('ajax');
        }

          else
        {
          $this->viewBuilder()->setLayout('search');
        }

          $keyword = preg_replace('/#([^\s]+)/','$1',$this->request->GetParam('query')); // suppression du # dans le mot clé

          $this->set('title', 'Hashtag '.$keyword.' sur Twittux'); // titre de la page

          // on récupère toutes les informations du tweets contenant #mot-clé

          $this->set('resultat_tweet_hashtag_media', $this->paginate($this->Tweets->find()->select([
                                                                                                    'Tweets.id_tweet',
                                                                                                    'Tweets.username',
                                                                                                    'Tweets.contenu_tweet',
                                                                                                    'Tweets.created',
                                                                                                    'Tweets.nb_commentaire',
                                                                                                    'Tweets.nb_partage',
                                                                                                    'Tweets.nb_like',
                                                                                                  ])
                                                                                            ->where(['Tweets.contenu_tweet REGEXP' => '<img.+?class=".*?media_tweet.*?"','Tweets.contenu_tweet REGEXP' => '#[[:<:]]'.$keyword.'[[:>:]]'])
                                                                                            ->where(['private' => 0])));

      }

    /**
     * Méthode userhashtag
     *
     * Recherche les description d'utilisateurs contenant le #$keyword
     *
     */

        public function userhashtag()
    {
            if ($this->request->is('ajax')) // requête AJAX uniquement
        {
            $this->viewBuilder()->setLayout('ajax');

            $keyword = preg_replace('/#([^\s]+)/','$1',$this->request->GetParam('query'));

            $this->set('resultat_users', $this->paginate($this->Users->find()->select([
                                                                                        'Users.username',
                                                                                        'Users.description',
                                                        ])
                                                ->where(['Users.description REGEXP' => '#[[:<:]]'.$keyword.'[[:>:]]'])));
    }
            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
    }

}
