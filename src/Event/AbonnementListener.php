<?php
namespace App\Event;

use Cake\I18n\Time;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

/**
 * Abonnement listener
 *
 *  Envoi d'une notification à l'utilisateur visé par un abonnement ou à une demande d'abonnement dans le cas d'un profil privé
 *
 */

class AbonnementListener implements EventListenerInterface {

        public function implementedEvents(): array

    {
        return [
            'Model.Abonnement.afteradd' => 'notifabo',
            'Model.Abonnement.acceptrequest' => 'notifacceptrequest'
        ];
    }

/**
     * Méthode notifabo
     *
     * Création d'une notification d'abonnement ou de demande d'abonnement
     *
     * Paramètres : $data -> tableau contenant les informations de la demande ou de l'abonnement
     *
*/

            public function notifabo($event, $data)
        {

          $entity = TableRegistry::get('Notifications');

            if($data['etat'] == 0) // demande
          {
              $notif = '<img src="/twittux/img/avatar/'.$data['suiveur'].'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/><a href="/twittux/'.$data['suiveur'].'" class="w3-text-indigo">'.$data['suiveur'].'</a> souhaite s\'abonner : <a href="#" class="w3-text-indigo" onclick="requestlink()" data_username ="'.$data['suivi'].'">gérer mes demandes.</a>';
          }
            elseif ($data['etat'] == 1)
          {
              $notif = '<img src="/twittux/img/avatar/'.$data['suiveur'].'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/><a href="/twittux/'.$data['suiveur'].'" class="w3-text-indigo">'.$data['suiveur'].'</a> vous suit désormais.';
          }

          $notif_abo = $entity->newEmptyEntity();

          $notif_abo->user_notif = $data['suivi']; // personne que je suis

          $notif_abo->notification = $notif; // notification

          $notif_abo->statut = 0;

          $notif_abo->created =  Time::now();

          $entity->save($notif_abo);

        }

        /**
     * Méthode notifacceptrequest
     *
     * Création d'une notification d'acceptation de demande d'abonnement
     *
     * Paramètres : $suiveur -> personne qui me suis | $suivi -> profil connecté qui accepte une demande d'abonnement
     *
*/

        public function notifacceptrequest($event, $suiveur, $suivi)
      {

        $entity = TableRegistry::get('Notifications');

        $notif = '<img src="/twittux/img/avatar/'.$suivi.'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/><a href="/twittux/'.$suivi.'" class="w3-text-indigo">'.$suivi.'</a> à accepté votre demande d\'abonnement.';

        $notif_request = $entity->newEmptyEntity();

        $notif_request->user_notif = $suiveur; // personne qui me suis désormais

        $notif_request->notification = $notif; // notification

        $notif_request->statut = 0;

        $notif_request->created =  Time::now();

        $entity->save($notif_request);

      }

}
