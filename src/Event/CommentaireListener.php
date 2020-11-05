<?php
namespace App\Event;

use Cake\I18n\Time;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

/**
 * Abonnement listener
 *
 * Création d'une notification indiquant qu'un utilisateur à commenté un post
 */

class CommentaireListener implements EventListenerInterface {

        public function implementedEvents(): array

    {
        return [
            'Model.Commentaire.afteradd' => 'notifcomm',
        ];
    }

/**
     * Méthode notifcomm
     *
     * Création d'une notification de nouveau commentaire
     *
     * Paramètres : $data -> tableau contenant les informations du commentaire, $auttweet -> personne à qui est destiné la notification
     *
*/

            public function notifcomm($event, $data, $auttweet)
        {

          $entity = TableRegistry::get('Notifications');

          $notif = '<img src="/twittux/img/avatar/'.$data['username'].'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/><a href="/twittux/'.$data['username'].'" class="w3-text-indigo">'.$data['username'].'</a> à commenté votre <a href="/twittux/statut/'.$data['id_tweet'].'" class="w3-text-indigo">publication.</a>';

          $notif_comm = $entity->newEmptyEntity();

          $notif_comm->user_notif = $auttweet; // auteur du tweet

          $notif_comm->notification = $notif; // notification

          $notif_comm->statut = 0;

          $notif_comm->created =  Time::now();

          $entity->save($notif_comm);


        }

}
