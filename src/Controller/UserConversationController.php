<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Exception\NotFoundException;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use App\Event\MessagerieListener;

/**
 * UserConversation Controller
 *
 * @property \App\Model\Table\UserConversationTable $UserConversation
 * @method \App\Model\Entity\UserConversation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserConversationController extends AppController
{

  public function initialize() : void
{

  parent::initialize();

  //listener qui va écouté la création d'un nouveau message

  $MessagerieListener = new MessagerieListener();

  $this->getEventManager()->on($MessagerieListener);

}

/**
 * Méthode Edit
 *
 * Afficher / Masquer une conversation
 *
 * Sortie : Renvoi d'une réponse pour le traitement en Javascript
 */
 public function edit()
{
        if ($this->request->is('ajax')) // requête AJAX uniquement
      {
          $idconv = $this->request->input('json_decode')->idconv; // identifiant de la conversation

          $action = $this->request->input('json_decode')->action; // masquer/afficher conversation

  // requête de modification

  $statement = ConnectionManager::get('default')->prepare(
    'UPDATE userconversation SET visible = :action WHERE conversation = :idconv AND user_conv = :user_conv');

    $statement->bindValue('action', $action);
    $statement->bindValue('idconv', $idconv);
    $statement->bindValue('user_conv', $this->Authentication->getIdentity()->username, 'string');
    $statement->execute();

    $rowCount = $statement->rowCount();

      if ($rowCount == 1) // mise à jour réussie, renvoi d'une réponse
    {
      return $this->response->withStringBody('updateok');
    }
      elseif ($rowCount == 0) // echec mise à jour , renvoi d'une réponse
    {
      return $this->response->withStringBody('pasupdate');
    }
  }

      else // en cas de non requête AJAX on lève une exception 404
    {
      throw new NotFoundException(__('Cette page n\'existe pas.'));
    }
}

/**
 * Méthode Addtoconv
 *
 * Traitement de l'ajout d'une personne à une conversation
 *
 * Sortie : Renvoi d'une réponse JSON pour le traitement en Javascript
 */

  public function addtoconv()
{
      if ($this->request->is('ajax')) // requête AJAX uniquement
    {
      // récupération des données issues du formulaire de la modale 'modalinvitconv'

      $invituser = $this->request->getData('userinvit'); // personne invitée

      $conversation = $this->request->getData('conversation'); // identifiant de la conversation

      $typeconv = $this->request->getData('typeconv'); // duo / multiple

      $invit = 0; // nombre d'invité

      // on vérifie pour chaque utilisateur invité si il fait partie déjà de la conversation

    foreach($invituser as $invituser)
  {

      if($invituser != $this->Authentication->getIdentity()->username) // je ne peut pas m'inviter moi même
    {

          if($this->isinconv($conversation, $invituser) === 'non')
        {

          $data = array('whoinvit' => $this->Authentication->getIdentity()->username, // compte courant invitant
                        'usertoinvit' => $invituser, // personne invitée
                        'conversation' => $conversation, // identifiant de la conversation
                        'typeconv' => $typeconv);

        // Evènement de création d'une notification d'invitation à rejoindre une conversation

          $event = new Event('Model.Messagerie.notiftoinvit', $this,  ['data' => $data]);

          $this->getEventManager()->dispatch($event);

          $invit ++; // incrémentation du nombre d'invité

        }
      }
    }

  if($invit == 0) // si personne n'ais invité , renvoi d'une réponse JSON
{

  return $this->response->withType('application/json')
                      ->withStringBody(json_encode(['Result' => 'noinvit']));
}

  else // si il y'a au moins 1 invité , renvoi d'une réponse JSON
{

  return $this->response->withType('application/json')
                      ->withStringBody(json_encode(['Result' => 'invitok']));
}

  }

  else // en cas de non requête AJAX on lève une exception 404 de merde
{
  throw new NotFoundException(__('Cette page n\'existe pas.'));
}

}

/**
 * Méthode Joinconv
 *
 * Rejoindre une conversation depuis une notification d'invitation à rejoindre une conversation
 *
 * Sortie : Renvoi d'une réponse JSON pour le traitement en Javascript
 */

  public function joinconv()
{
      if ($this->request->is('ajax')) // requête AJAX uniquement
    {

      $jsonData = $this->request->input('json_decode'); // récupération des données JSON

      $idconv = $jsonData->idconv; //identifiant de la conversation

      $typeconv = $jsonData->typeconv; // duo/multiple

  // vérifie si pas déjà dans le cas d'un reclique sur le lien de la notification

      if(AppController::isinconv($idconv, $this->Authentication->getIdentity()->username) === 'non')
    {

      // création d'une nouvelle entitée UserConversation

      $conversation = $this->UserConversation->newEmptyEntity();

      $data = array(
                      'user_conv' =>  $this->Authentication->getIdentity()->username,
                      'conversation' => $idconv,
                      'visible' => 'oui' // conversation viusible par défaut
                  );

                  $conversation = $this->UserConversation->patchEntity($conversation, $data);

          if($this->UserConversation->save($conversation)) //création d'entité réussie

        {
          // mise à jour du type de conversation si elle est duo, on la passe en multiple (toutes les conversation sont duo par défaut)

      if($typeconv === 'duo')
    {
      $this->loadModel('Conversation');

      $query = $this->Conversation->query();

      $query->update()
            ->set(['type_conv' => 'multiple'])
            ->where(['id_conv' => $idconv])
            ->execute();
    }

    // renvoi d'une réponse JSON pour le traitement Javascript

    return $this->response->withType('application/json')
                        ->withStringBody(json_encode(['Result' => 'joinconvok']));
  }

}

//si déjà dans la conversation car clique depuis la notification plusieurs fois

      elseif (AppController::isinconv($idconv, $this->Authentication->getIdentity()->username) === 'oui')
    {

  return $this->response->withType('application/json')
                        ->withStringBody(json_encode(['Result' => 'joinconvok']));
    }

      else // impossible de rejoindre cette conversation
    {
          return $this->response->withType('application/json')
                              ->withStringBody(json_encode(['Result' => 'joinconvnotok']));
    }
}

  else // en cas de non requête AJAX on lève une exception 404
{
  throw new NotFoundException(__('Cette page n\'existe pas.'));
}

}

}
