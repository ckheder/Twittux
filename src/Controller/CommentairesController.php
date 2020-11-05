<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use App\Event\CommentaireListener;
use Cake\Http\Exception\NotFoundException;

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
            $commentaire = $this->Commentaires->newEmptyEntity();

            $idcomm = $this->idcomm(); // génération d'un nouvel identifiant de tweet

            $auttweet = $this->request->getData('user_tweet'); // auteur du tweet

            $data = array(
                            'id_comm' => $idcomm,
                            'commentaire' => AppController::linkify_content($this->request->getData('commentaire')),
                            'id_tweet' => $this->request->getData('id_tweet'),
                            'username' => $this->Auth->user('username')
                            );

            $commentaire = $this->Commentaires->patchEntity($commentaire, $data);

                if ($this->Commentaires->save($commentaire))
            {

                if($auttweet != $this->Auth->user('username')) // si le profil qui commente n'est pas celui qui est l'auteur du tweet
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

         }

            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }

    }

    /**
     * Delete method
     *
     * @param string|null $id Commentaire id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {

            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

        $commentaire = $this->Commentaires->get($id);

          if($commentaire->username == $this->Auth->user('username'))
        {

              if ($this->Commentaires->delete($commentaire))
            {

              return $this->response->withStringBody('ok');
            }

        }

          else

        {

            return $this->response->withStringBody('nonok');
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
