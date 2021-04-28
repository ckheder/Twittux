<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use App\Event\CommentaireListener;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;

/**
 * Commentaires Controller
 *
 * @property \App\Model\Table\CommentairesTable $Commentaires
 * @method \App\Model\Entity\Commentaire[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentairesController extends AppController
{

  public function initialize() : void
{
  parent::initialize();


  //listener qui va écouté la création d'un nouveau commentaire

  $CommentaireListener = new CommentaireListener();

  $this->getEventManager()->on($CommentaireListener);

}

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

          if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            if($this->request->getData('allowcomm') == 1) // les commentaires sont bloqués (donnée envoyée en champ caché par le formulaire)
          {
            return $this->response->withType('application/json')
                                  ->withStringBody(json_encode(['result' => 'commblock']));
          }
            else
          {

            $commentaire = $this->Commentaires->newEmptyEntity();

            $idcomm = $this->idcomm(); // génération d'un nouvel identifiant de tweet

            $auttweet = $this->request->getData('user_tweet'); // auteur du tweet

            $contenu_comm = strip_tags($this->request->getData('commentaire')); // suppression des tags éventuels

            // on vérifie si je ne suis pas bloqué

            if(AppController::checkblock($auttweet, $this->Authentication->getIdentity()->username) == 'oui')
          {

            return $this->response->withType('application/json')
                                    ->withStringBody(json_encode(['result' => 'nocomm']));
          }

            $data = array(
                            'id_comm' => $idcomm,
                            'commentaire' => AppController::linkify_content($contenu_comm),
                            'id_tweet' => $this->request->getData('id_tweet'),
                            'username' => $this->Authentication->getIdentity()->username
                            );

            $commentaire = $this->Commentaires->patchEntity($commentaire, $data);

                if ($this->Commentaires->save($commentaire))
            {

                if($auttweet != $this->Authentication->getIdentity()->username) // si le profil qui commente n'est pas celui qui est l'auteur du tweet
              {

                if(AppController::check_notif('commentaire', $auttweet ) == 'oui') // si l'auteur du tweet accepte les notifications de commentaire
              {

              // Evènement de création d'une notification de commentaire

              $event = new Event('Model.Commentaire.afteradd', $this, ['data' => $data, 'auttweet' => $auttweet]);

              $this->getEventManager()->dispatch($event);
           }
         }

                return $this->response->withType("application/json")->withStringBody(json_encode($commentaire));
            }
              elseif (!$this->Commentaires->save($commentaire)) // echec de l'envoi du commentaire
            {

              return $this->response->withType('application/json')
                                      ->withStringBody(json_encode(['result' => 'nocomm']));
            }

         }
       }

            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }

    }

    /**
     * Suppression d'un commentaire
     *
     * @param string|null $id Commentaire id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {

            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

          // on vérifie si je suis bien l'auteur du commentaire ($this->request->input('json_decode')->idcomm) ou que je suis l'auteur d'un tweet ($this->request->input('json_decode')->idtweet)qui veut supprimer un commentaires

          $check_comm_user = $this->Commentaires->find()

          ->leftjoin(
                  ['Tweets'=>'tweets'],
                  ['Tweets.id_tweet = (Commentaires.id_tweet)']
                  )

          ->where([
                    'OR' => ['Commentaires.username' => $this->Authentication->getIdentity()->username,'Tweets.username' => $this->Authentication->getIdentity()->username]])

          ->where(['Tweets.id_tweet' => $this->request->input('json_decode')->idtweet, 'Commentaires.id_comm' => $this->request->input('json_decode')->idcomm]);


            if($check_comm_user->isEmpty()) // si la requête est vide, je ne peut supprimer ce commentaire
          {
            return $this->response->withType('application/json')
                                  ->withStringBody(json_encode(['Result' => 'deletecommnotok']));
          }

              else
          {
            $entity = $this->Commentaires->get($this->request->input('json_decode')->idcomm); // on récupère l'entité correspondant a l'id du comm

              if($this->Commentaires->delete($entity)) // si l'entité est correctement supprimée
            {
              return $this->response->withType('application/json')
                                    ->withStringBody(json_encode(['Result' => 'deletecommok']));
            }
              else
            {
              return $this->response->withType('application/json')
                                    ->withStringBody(json_encode(['Result' => 'deletecommnotok']));
            }

          }

    }
            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
}

/**
 * Méthode Actioncomm
 *
 * Activation / Désactivation des commentaires pour un tweet donné
 *
 * Paramètres : id du tweet , action à effectué
 *
 * Par défaut : 0 ->commentaire activé et 1 -> commentaire désactivé
 */
        public function actioncomm()
      {

          if ($this->request->is('ajax')) // requête AJAX uniquement
        {

          $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

          $idtweet = $jsonData->idtweet; //identifiant du tweet concerné

          $action = $jsonData->action; // 0 ou 1 : si 0 je désactive les commentaires, si 1 j'active les commentaires

          $statement = ConnectionManager::get('default')->prepare('UPDATE tweets SET allow_comment = :action WHERE id_tweet = :idtweet');

          $statement->bindValue('action', $action, 'boolean');

          $statement->bindValue('idtweet', $idtweet, 'integer');

          $statement->execute();

          $rowCount = $statement->rowCount();

            if ($rowCount == 1) // mise à jour réussie
          {
            return $this->response->withStringBody('updatecommok');
          }

            elseif ($rowCount == 0) // échec mise à jour information
          {
            return $this->response->withStringBody('updatecommnotok');
          }

        }
        else // en cas de non requête AJAX on lève une exception 404
      {
        throw new NotFoundException(__('Cette page n\'existe pas.'));
      }
    }

     /**
     * Méthode Idcomm
     *
     * Calcul d'un id de comm aléatoire
     *
     * Sortie : $idtweet -> id de comm
     *
     *
*/
        private function idcomm()
    {

        $idcomm = rand();

        // on vérifie si il existe déjà

        $query = $this->Commentaires->find()
                                ->select(['id_comm'])
                                ->where(['id_comm' => $idcomm]);

                if ($query->isEmpty())
            {
                return $idcomm;
            }
                else
            {
                idcomm(); // ou $this->idcomm();
            }
    }
}
