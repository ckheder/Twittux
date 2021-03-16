<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;

/**
 * Settings Controller
 *
 * @property \App\Model\Table\SettingsTable $Settings
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingsController extends AppController
{

  public function initialize() : void
{
  parent::initialize();

  $this->loadModel('Tweets'); // chargement du modèle Tweets pour la mise à jour du champ private si changement de profil

}

    /**
     * Méthode Setupprofil
     *
     * Configuration d'un profil : privé ou public
     *
     */
     public function setupprofil()
    {
          if ($this->request->is('ajax')) // requête AJAX uniquement
        {
          $data = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

          // requête

          $statement = ConnectionManager::get('default')->prepare(
            'UPDATE settings SET type_profil = :data WHERE username = :username');

            $statement->bindValue('data', $data, 'string');
            $statement->bindValue('username', $this->Authentication->getIdentity()->username, 'string');
            $statement->execute();

            $rowCount = $statement->rowCount();

              if ($rowCount == 1) // si la ligne à bien était modifié, on envoi une réponse 'setupok'
            {
              if($data == 'prive')
              {
                $prive = 1; // tweet prive
              }
                else
              {
                $prive = 0; // tweet public
              }

              // passage des tweets à 0 (public) ou a 1 (prive) lors de changement de type de profil

              $updatetweet = ConnectionManager::get('default')->prepare(
                'UPDATE tweets SET private = :prive WHERE username = :username');

                $updatetweet->bindValue('prive', $prive, 'integer');
                $updatetweet->bindValue('username', $this->Authentication->getIdentity()->username, 'string');
                $updatetweet->execute();

                $rowCount = $updatetweet->rowCount();

                  if($rowCount >= 1)
                {
                  return $this->response->withStringBody('setupok');
                }

            }
              elseif ($rowCount == 0) // aucune modification, envoi d'une réponse 'probleme'
            {
              return $this->response->withStringBody('probleme');
            }
          }

          else // en cas de non requête AJAX on lève une exception 404
        {
          throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
}
    /**
      * Méthode Setupnotif
      *
      * Configuration des différentes notifications
      *
      */
          public function setupnotif()
        {
              if ($this->request->is('ajax')) // requête AJAX uniquement
            {

              $type_notif = $this->request->input('json_decode')->typenotif; // messagerie, citation, commentaire, ....
              $choix = $this->request->input('json_decode')->select; // oui ou non

              // requête de modification

              $statement = ConnectionManager::get('default')->prepare(
                'UPDATE settings SET '.$type_notif.' = :choix WHERE username = :username');

                $statement->bindValue('choix', $choix, 'string');
                $statement->bindValue('username', $this->Authentication->getIdentity()->username, 'string');
                $statement->execute();

                $rowCount = $statement->rowCount();

                  if ($rowCount == 1) // mise à jour réussie, renvoi d'une réponse
                {
                  return $this->response->withStringBody('setupok');
                }
                  elseif ($rowCount == 0) // echec mise à jour , renvoi d'une réponse
                {
                  return $this->response->withStringBody('probleme');
                }
              }

              else // en cas de non requête AJAX on lève une exception 404
            {
              throw new NotFoundException(__('Cette page n\'existe pas.'));
            }

        }
}
