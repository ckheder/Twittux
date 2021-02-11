<?php
namespace App\Event;

use Cake\I18n\Time;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

/**
 * Partage listener
 *
 * Création d'une notification indiquant qu'un utilisateur à partagé l'un de mes posts
 *
 */

class PartageListener implements EventListenerInterface {

        public function implementedEvents(): array

    {
        return [
            'Model.Partage.afteradd' => 'notifshare',
        ];
    }

/**
     * Méthode notifshare
     *
     * Création d'une notification de partage
     *
     * Paramètres : $data -> tableau contenant les informations du commentaire sur celui qui partage et quel post est concerné
     *
*/

            public function notifshare($event, $data)
        {

          $entity = TableRegistry::get('Notifications');

          $notif = '<img src="/twittux/img/avatar/'.$data['username'].'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/><a href="/twittux/'.$data['username'].'" class="w3-text-indigo">'.$data['username'].'</a> à partagé votre <a href="/twittux/statut/'.$data['id_tweet'].'" class="w3-text-indigo">publication.</a>';

          $notif_share = $entity->newEmptyEntity();

          $notif_share->user_notif = $data['auttweet']; // auteur du tweet

          $notif_share->notification = $notif; // notification

          $notif_share->statut = 0;

          $notif_share->created =  Time::now();

          $entity->save($notif_share);

        }
}
