<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Datasource\ConnectionManager;

/**
 * Blocage Controller
 *
 * @property \App\Model\Table\BlocageTable $Blocage
 * @method \App\Model\Entity\Blocage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BlocageController extends AppController
{

  public $paginate = [
                  'limit' => 10,
                  'maxLimit' => 30

                  ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
      public function index()
    {
      $this->viewBuilder()->setLayout('follow');

      // titre de page dynamique

       $this->set('title', 'Utilisateurs bloqués');

       // récupération des personnes que j'ai bloqués

           $user_block = $this->Blocage->find()

           ->select(['bloque'])

           ->where(['bloqueur' =>  $this->Authentication->getIdentity()->username])

           ->order((['bloque' => 'ASC']));

           $this->set('user_block', $this->Paginator->paginate($user_block, ['limit' => 30]));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
     public function add()
    {
        if ($this->request->is('ajax')) // requête AJAX/POST uniquement
      {

        $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

      // vérification d'un blocage existant

          if(AppController::checkblock($this->Authentication->getIdentity()->username, $jsonData->username) == 'non') // pas de blocage existant
        {

          //création d'une nouvelle entité blocage

          $block = $this->Blocage->newEmptyEntity();

          $data = array(
                          'bloqueur' => $this->Authentication->getIdentity()->username, // utilisateur qui bloque (profil courant)
                          'bloque' => $jsonData->username // utilisateur que je bloque

                      );

          $block = $this->Blocage->patchEntity($block, $data);

            if ($this->Blocage->save($block)) // ajout d'un blocage réussi
          {

            // suppression d'un éventuel abonnement existant

            $statement = ConnectionManager::get('default')->prepare(
              'DELETE FROM abonnements WHERE suiveur = :suiveur AND suivi = :suivi');


              $statement->bindValue('suiveur', $jsonData->username, 'string');
              $statement->bindValue('suivi', $this->Authentication->getIdentity()->username, 'string');
              $statement->execute();

        // renvoi d'une réponse au format JSON

            return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'addblock']));
          }

          else // échec création d'un like, renvoi d'une réponse au format JSON
        {
          return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'probleme']));
        }
      }

      else // renvoi d'une réponse JSON signifiant que l'on à déja bloqué cet utilisateur
    {

      return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'existblock']));

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
     * @param string|null $id Blocage id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
      if ($this->request->is('ajax')) // requête AJAX uniquement
  {

      $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

      $username = $jsonData->username; //nom de la personne que je débloque

      $statement = ConnectionManager::get('default')->prepare(
      'DELETE FROM blocage WHERE bloqueur = :bloqueur AND bloque = :bloque');


      $statement->bindValue('bloqueur', $this->Authentication->getIdentity()->username, 'string');
      $statement->bindValue('bloque', $username, 'string');
      $statement->execute();

      // Récupération du nombre de ligne affectée

      $rowCount = $statement->rowCount();

          if ($rowCount == 1) // blocage supprimée avec succès , renvoi d'une réponse au format JSON
      {
          return $this->response->withType('application/json')
                                  ->withStringBody(json_encode(['Result' => 'blocagesupprime']));
      }
          elseif ($rowCount == 0) // échec suppression, renvoi d'une réponse au format JSON
      {
          return $this->response->withType('application/json')
                                  ->withStringBody(json_encode(['Result' => 'blocagenonsupprime']));
      }

  }
      else // en cas de non requête AJAX on lève une exception 404
  {
      throw new NotFoundException(__('Cette page n\'existe pas.'));
  }
    }
}
