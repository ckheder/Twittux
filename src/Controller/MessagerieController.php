<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use App\Event\MessagerieListener;
use Cake\Routing\Router;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;

/**
 * Messagerie Controller
 *
 * @property \App\Model\Table\MessagerieTable $Messagerie
 * @method \App\Model\Entity\Messagerie[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MessagerieController extends AppController
{

  public function initialize() : void
{

  parent::initialize();

  //listener qui va écouté la création d'un nouveau message

  $MessagerieListener = new MessagerieListener();

  $this->getEventManager()->on($MessagerieListener);

}
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
      public function index()
    {

      $this->viewBuilder()->setLayout('messagerie');

      $this->set('title', 'Twittux | Messagerie'); // titre de la page

    }

    /**
     * View method
     *
     * Voir une conversation
     *
     *
     */
     public function view()
    {
          if ($this->request->is('ajax')) // requête AJAX uniquement
        {
          // test si on peut voir la conversation

          // si oui

            if(AppController::isinconv($this->request->getParam('idconv'), $this->Authentication->getIdentity()->username) == 'oui')
          {

            // récupération des messages paginés de la conversation par ordre décroisant

            $message = $this->Messagerie->find()->select([
                                                          'Messagerie.user_message',
                                                          'Messagerie.message',
                                                          'Messagerie.created'
                                                        ])

                                                ->where(['Messagerie.conversation' => $this->request->getParam('idconv')])
                                                ->order(['Messagerie.created' => 'DESC']);

            $this->set('message', $this->Paginator->paginate($message, ['limit' => 8]));
          }

          // si non

            else
          {

            $no_see = 1; // interdiction de voir

            $this->set('no_see', $no_see); // envoi d'une varibale d'information

            return;

          }

        }

        else // en cas de non requête AJAX on lève une exception 404
      {

        throw new NotFoundException(__('Cette page n\'existe pas.'));
      }
}

    /**
     * Méthode Add
     *
     * Envoi d'un message depuis la page d'accueuil de la messagerie
     *
     * Sortie : renvoi d'une réponse jSON contenant soit le message à affiché dans une conversation soit un résultat d'envoi de message depuis la page d'index de la messagerie
     */
     public function add()
    {
          if ($this->request->is('ajax')) // requête AJAX uniquement
        {

          $destinataire = $this->request->getData('destinataire'); // destinataire

          // test du blocage sur l'envoi de message : tester uniquement en conversation duo ou index de la messagerie

          if(count($destinataire) === 1) // décompte du tableau des destinataires
        {

              if($destinataire[0] == $this->Authentication->getIdentity()->username) // test de l'envoi à soi -même
            {
              return $this->response->withType('application/json')
                                ->withStringBody(json_encode(['Result' => 'sendtoyourself']));
            }

            $typeconv = 'duo'; // décompte à 1 -> conversation en duo

            if(AppController::checkblock($destinataire[0], $this->Authentication->getIdentity()->username) == 'oui')
          {

            return $this->response->withType('application/json')
                                  ->withStringBody(json_encode(['Result' => 'userblock']));

          }

        }
          else
        {
          $typeconv = 'multiple'; // décompte à plus de 1 donc conversation multiple
        }

          if($this->request->getData('conversation') == null) // je vient de la page d'accueil de la messagerie
        {
            // récupération ou génération d'une nouvelle conversation avec mon destinataire

              $conversationresult = $this->get_conv($destinataire[0]);

              $conversation = $conversationresult['conversation']; // identifiant conversation

              $new_conv = $conversationresult['new_conv']; // 0 : nouvelle conversation, 1 : conversation existante

       }

       // conversation existante donc envoi depuis une conversation

        else
       {
         // liste des destinataires

         $conversation = $this->request->getData('conversation');

         $new_conv = 1;

       }

       // tableau des données destinés à la création d'une nouvelle entité messagerie

          $data = array(
                          'user_message' => $this->Authentication->getIdentity()->username, // expediteur
                          'message' => AppController::linkify_content($this->request->getData('message')), // message
                          'conversation' => $conversation // conversation concerné
                          );

        $message = $this->Messagerie->newEmptyEntity();

        $message = $this->Messagerie->patchEntity($message, $data);

        // message envoyé avec succès

              if ($this->Messagerie->save($message))
            {

                if($new_conv == 0) // création d'une nouvelle conversation
              {
                //tableau contenant les informations nécessaires

                $data_new_conv = array('user_message' => $this->Authentication->getIdentity()->username, // expediteur
                                      'conversation' => $conversation, // identifiant de conversation
                                      'destinataire' => $destinataire[0]); // destinataire

                $event = new Event('Model.Messagerie.newconv', $this, ['data_new_conv' => $data_new_conv]);

                $this->getEventManager()->dispatch($event);
              }


                  foreach($destinataire as $destinataire) // on vérifie , pour chaque destinataire , si il accepte les notifications de nouveaux message
                {

                      if(AppController::check_notif('message', $destinataire, $conversation) == 'oui') // notification acceptée
                    {

                      // Evènement de création d'une notification de nouveau message

                      $event = new Event('Model.Messagerie.afteradd', $this, ['data' => $data,'destinataire' => $destinataire,'typeconv' => $typeconv]);

                      $this->getEventManager()->dispatch($event);

                    }
                }

                if($this->request->getData('indexmess')) // envoi message depuis l'index
              {
                return $this->response->withType('application/json')
                                  ->withStringBody(json_encode(['Result' => 'msgok','conversation' => $conversation,'user_message' => $this->Authentication->getIdentity()->username,'message' => $data['message']]));
              }
                else
              {
                return $this->response->withType("application/json")->withStringBody(json_encode($data));
              }

            }

              else // message non envoyé
            {
              return $this->response->withStringBody('msgnotok');
            }

    }

      else // en cas de non requête AJAX on lève une exception 404
    {

      throw new NotFoundException(__('Cette page n\'existe pas.'));

    }
}

/**
 * Méthode listconv
 *
 * Récupération de toutes les conversations de l'utilisateur courant ainsi que le dernier message posté, si la conversation est visible ou non et le type de conversation (duo/multiple)
 *
 */

      public function listconv()
    {

          if ($this->request->is('ajax')) // requête AJAX uniquement
        {
          $connection = ConnectionManager::get('default');

      $conv = $connection->execute('SELECT M.conversation, DM.message AS message, DM.created AS created, DM.user_message AS user_message,M.visible, C.type_conv
            FROM ( SELECT messagerie.conversation, MAX( created ) AS max_date, UC.visible AS visible FROM messagerie  INNER join userconversation UC ON messagerie.conversation = UC.conversation
                  WHERE UC.user_conv = "'.$this->Authentication->getIdentity()->username.'"
                  GROUP BY messagerie.conversation ) M
            INNER JOIN conversation C ON M.conversation = C.id_conv
            INNER join userconversation UC ON C.id_conv = UC.conversation
            INNER JOIN messagerie DM ON C.id_conv = DM.conversation
            AND M.max_date = DM.created
            GROUP BY M.conversation
            ORDER BY DM.created DESC');

        $this->set('conv' , $conv); // renvoi de la liste des conversations

      }
      else // en cas de non requête AJAX on lève une exception 404
    {

      throw new NotFoundException(__('Cette page n\'existe pas.'));

    }
  }

    /**
* Méthode messagefromprofil
*
* Redirection vers une conversation ou la page d'accueil de la messagerie depuis le profil
*
* Paramètre : $username : profil visité
*
*/

  public function messagefromprofil()
{
      if ($this->request->is('ajax')) // requête AJAX uniquement
   {
     $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

     $username = $jsonData->username; //nom de la personne à qui j'envoi un message depuis sa page de profil

     // vérification si y'a déjà une conversation entre nous

     $conversationresult = $this->get_conv($username);

        if($conversationresult['new_conv'] == 0) // pas de conversation existante, on ajoute son nom au tableau de résultat de la fonction
       {
         $conversationresult['username'] = $username;
       }

       // renvoi d'une réponse JSON contenant le résultat de la fonction

         return $this->response->withType("application/json")->withStringBody(json_encode($conversationresult));

    }

      else // en cas de non requête AJAX on lève une exception 404
    {

      throw new NotFoundException(__('Cette page n\'existe pas.'));

    }

}

    /**
* Méthode get_conv
*
* Récupération d'une éventuelle conversation et génération d'un nouvel id de conversation si nécessaire
*
* Paramètre : $destinataire -> identifiant du destinataire du message
*
* Sortie : un identifiant de conversation existant ou généré et une variable d'information si c'est une nouvelle conversation ou existante
*/

  private function get_conv($destinataire)
{

//récupération d'une éventuelle conversation existante

// récupération de toutes les conversations duo de mon destinataire

$otherparticipant = $this->Conversation
                        ->find()
                        ->select(['id_conv'])
                        ->innerJoin(
                                    ['UserConversation' => 'userconversation'],
                                    ['Conversation.id_conv = UserConversation.conversation']

                                    )
                        ->where(['UserConversation.user_conv' =>  $destinataire,'Conversation.type_conv' => 'duo']);

// on vérifie maintenant si dans la liste de conversation précedemment récupérer il y'en as une en commun avec moi
$checkconv = $this->UserConversation
                ->find()
                ->select(['conversation'])
                ->where(['user_conv' =>  $this->Authentication->getIdentity()->username ]) // moi
                ->andwhere(['conversation IN' => $otherparticipant]); //destinataire


      if ($checkconv->isEmpty()) // si pas de résultat, on crée une nouvelle conversation
    {

      $conversation = rand();// création d'id de conversation aléatoire

      $new_conv = 0; // conversation inexistante, variable à 0

    }

      else // stockage de la conversation
    {
        foreach ($checkconv as $row)
      {

        $conversation = $row['conversation'];

        $new_conv = 1;

      }

    }

    // renvoi de l'id de conversation et si il s'agit d'une nouvelle conversation

    return array('conversation' => $conversation,

                'new_conv' => $new_conv);
}
}
