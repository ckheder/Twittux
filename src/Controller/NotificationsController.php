<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotificationsTable $Notifications
 * @method \App\Model\Entity\Notification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationsController extends AppController
{
  /**
   * Méthode Index
   *
   * Retourne la liste de toute mes notifications lues et non lues
   *
   */
    public function index()
  {
      $this->viewBuilder()->setLayout('notifications');

      $this->set('title', 'Twittux | Notifications'); // titre de la page

    // Récupération de mes notifications par odre décroissant de date

      $notifications = $this->Notifications->find()
                                            ->where(['user_notif' =>  $this->Authentication->getIdentity()->username ])
                                            ->order(['created'=> 'DESC']);


        $this->set('notifications', $this->Paginator->paginate($notifications, ['limit' => 10]));
  }
    /**
      * Méthode Statut
      *
      * Configuration des différentes notifications
      *
      */
          public function statut()
        {
              if ($this->request->is('ajax')) // requête AJAX uniquement
            {

              $statut = $this->request->input('json_decode')->statut; // récupération du statut

              switch($statut){

                // si le statut vaut 0 ou 1 -> mise à jour du statut d'une notification (lue/non lue)

            case "0":
            case "1":

                  if($statut === "0") // la notification devient lue
                {
                  $statut = 1;
                }
                  elseif ($statut === "1") // la notification devient non lue
                {
                  $statut = 0;
                }

                // mise à jour en base de donnée

                $statement = ConnectionManager::get('default')->prepare(
                'UPDATE notifications SET statut = :statut WHERE id_notif = :idnotif');

                $statement->bindValue('statut', $statut);
                $statement->bindValue('idnotif', $this->request->input('json_decode')->id_notif);
                $statement->execute();

                $rowCount = $statement->rowCount();

                    if ($rowCount == 1) // mise à jour réussie, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'updateok']));
                }
                    elseif ($rowCount == 0) // échec annulation, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'probleme']));
                }
                    break;

                    //suppression d'une notification

          case "deletenotif":

                $statement = ConnectionManager::get('default')->prepare(
                'DELETE FROM notifications WHERE user_notif = :usernotif AND id_notif = :idnotif');

                $statement->bindValue('usernotif', $this->Authentication->getIdentity()->username, 'string');
                $statement->bindValue('idnotif', $this->request->input('json_decode')->id_notif);
                $statement->execute();

                $rowCount = $statement->rowCount();

                    if ($rowCount == 1) // suppression réussie, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'deletenotifok']));
                }
                    elseif ($rowCount == 0) // échec suppression, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'probleme']));
                }
                    break;

                // marquer toutes les notifications comme lues

          case "allread":
                $statement = ConnectionManager::get('default')->prepare(
                'UPDATE notifications SET statut = 1 WHERE user_notif =  :usernotif');

                $statement->bindValue('usernotif', $this->Authentication->getIdentity()->username, 'string');
                $statement->execute();

                $rowCount = $statement->rowCount();

                    if ($rowCount >= 1) // mise à jour réussie, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'allnotifreadok']));
                }
                    elseif ($rowCount == 0) // échec mise à jour, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'probleme']));
                }
                    break;

                    //supprimer toute les notifications

        case "alldelete":
                $statement = ConnectionManager::get('default')->prepare(
                'DELETE FROM notifications WHERE user_notif = :usernotif');

                $statement->bindValue('usernotif', $this->Authentication->getIdentity()->username, 'string');
                $statement->execute();

                $rowCount = $statement->rowCount();

                    if ($rowCount >= 1) // suppressions réussies, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'allnotifdeleteok']));
                }
                    elseif ($rowCount == 0) // échec suppressions, envoi d'une réponse au format JSON
                {

                    return $this->response->withType('application/json')
                                            ->withStringBody(json_encode(['Result' => 'probleme']));
                }
                    break;
            }

          }
              else // en cas de non requête AJAX on lève une exception 404
            {
              throw new NotFoundException(__('Cette page n\'existe pas.'));
            }
        }
}
