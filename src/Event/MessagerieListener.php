<?php
namespace App\Event;

use Cake\I18n\Time;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

/**
 * Messagerie listener
 *
 * Création d'une notification de nouveau message, création d'une conversation, notification d'invitation à rejoindre une conversation
 */

class MessagerieListener implements EventListenerInterface {

        public function implementedEvents(): array

    {
        return [
            'Model.Messagerie.afteradd' => 'notifmessage',
            'Model.Messagerie.newconv' => 'newconv',
            'Model.Messagerie.notiftoinvit' => 'notiftoinvit',
        ];
    }

/**
     * Méthode notifmessage
     *
     * Création d'une notification de nouveau message
     *
     * Paramètres : $data -> tableau contenant les informations
     *
*/

            public function notifmessage($event, $data, $destinataire)
        {


          $entity = TableRegistry::get('Notifications');

          $notif = '<img src="/twittux/img/avatar/'.$data['user_message'].'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/><a href="/twittux/'.$data['user_message'].'" class="w3-text-indigo">'.$data['user_message'].'</a> vous à envoyé un  <a href="#" data_msg_conv="'.$data['conversation'].'" class="w3-text-indigo">message.</a>';

          $notif_msg = $entity->newEmptyEntity();

          $notif_msg->user_notif = $destinataire;

          $notif_msg->notification = $notif;

          $notif_msg->statut = 0;

          $notif_msg->created =  Time::now();

          $entity->save($notif_msg);

        }

          // création d'une nouvelle entité conversation / userconversation dans le cas d'une nouvelle conversation

          public function newconv($event,$data_new_conv)
      {

        // création de la ligne 'Conversation'

            $table_conv = TableRegistry::get('Conversation');

            $new_conv = $table_conv->newEmptyEntity();

            $new_conv->id_conv = $data_new_conv['conversation'];

            $new_conv->type_conv = 'duo';

            $table_conv->save($new_conv);

        // création des 2 lignes 'UserConversation'

        $data = [
    [
        'user_conv' => $data_new_conv['user_message'], // moi
        'conversation' => $data_new_conv['conversation'],
        'visible' => 'oui'
    ],
    [
      'user_conv' => $data_new_conv['destinataire'], // destinataire
      'conversation' => $data_new_conv['conversation'],
      'visible' => 'oui'
    ],
];

        // sauvegarde des entitées 'UserConversation'

        $user_conv_table = TableRegistry::get('UserConversation');
        $entities = $user_conv_table->newEntities($data);
        $result = $user_conv_table->saveMany($entities);


      }

// création d'une notification d'invitation à rejoindre une conversation

        public function notiftoinvit($event, $data)
      {

        $entity = TableRegistry::get('Notifications');

        $notif = '<img src="/twittux/img/avatar/'.$data['whoinvit'].'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/><a href="/twittux/'.$data['whoinvit'].'" class="w3-text-indigo">'.$data['whoinvit'].'</a> vous à invité à rejoindre une conversation : <a href="#" class="w3-text-indigo" data_typeconv = "'.$data['typeconv'].'" data_idconv = "'.$data['conversation'].'">Rejoindre</a>';

        $notif_joinconv = $entity->newEmptyEntity();

        $notif_joinconv->user_notif = $data['usertoinvit']; // personne invitée

        $notif_joinconv->notification = $notif; // notification

        $notif_joinconv->statut = 0;

        $notif_joinconv->created =  Time::now();

        $entity->save($notif_joinconv);

      }

}
