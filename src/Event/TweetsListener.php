<?php
namespace App\Event;

use Cake\Event\EventListener;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Database\Expression\QueryExpression;
use Cake\Routing\Router;

/**
 * Tweet Listener
 *
 * Vérifie si un ou plusieurs utilisateurs sont mentionnés dans le post et si oui , on vérifie si il accepte les notification de citation
 * Vériication et stockage des hashtags si utilisé
 */


class TweetsListener implements EventListenerInterface {

    public function implementedEvents(): array {
        return [
            'Model.Tweets.afteradd' => 'afteradd'
        ];
    }

/**
     * Méthode afteradd
     *
     * Extraire les username de mes posts et envoi de notification de citation si accepté pour chacun, traitement des hashtag
     *
     * Paramètres : $data -> tableau contenant le nom de la persone qui vient de tweeter et le tweet en question
     *
*/

    public function afteradd($event, $data) {

        // envoi d'une notification a chaque personne cité dans un tweet

        $entity = TableRegistry::get('Notifications');

        $array_username = $this->getUsernames($data['contenu_tweet']);

        if(count($array_username) != 0) // si le tableau n'est pas vide
    {
        //on parcourt le tableau de résultat et on vérifie pour chaque personne si il accepte les notifications de citation

            foreach ($array_username as $at_username):

                $username = str_replace('>@', '', $at_username);

            if($username != $data['username'])

        {
                if($this->testnotifcite($username) == 'oui')
            {

                $notif = '<img src="/twittux/img/avatar/'.$data['username'].'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/"/><a href="/twittux/'.$data['username'].'" class="w3-text-indigo">'.$data['username'].'</a> à vous à cité dans un <a href="/twittux/statut/'.$data['id_tweet'].'" class="w3-text-indigo">tweet.</a>';

                $notif_cite = $entity->newEmptyEntity();

                $notif_cite->user_notif = $username;

                $notif_cite->notification = $notif;

                $notif_cite->statut = 0;

                $notif_cite->created =  Time::now();

                $entity->save($notif_cite);
            }
        }
            endforeach;
    }

}
/**
     * Méthode testnotifcite
     *
     * Vérifie si le username en paramètre accepte les notifications de citation
     *
     * Paramètres : $username -> nom de la personne
     *
*/
                private function testnotifcite($username)
            {
                $table_settings = TableRegistry::get('Settings');

                $query = $table_settings->find()
                                        ->select(['notif_citation'])
                                        ->where(['username' => $username ]);

            foreach ($query as $verif_notif)
                {
                  $notification_citation = $verif_notif['notif_citation'];
                }

             return $notification_citation;
            }

// fonction d'extraction des username

                function getUsernames($string)
            {
                $at_username = FALSE;

                preg_match_all("/(^|[^@\w])@(\w{1,15})\b/", $string, $matches);

                    if ($matches)
                {
                    $atusernameArray = array_count_values($matches[0]);

                    $at_username = array_keys($atusernameArray);
                }
                    return $at_username;
            }

// fonction d'extraction des hashtag : récupération des #hashtags

            function getHashtags($string)
          {

            preg_match_all("/(#\w+)/u", $string, $matches);

            if ($matches) {

                            $hashtagArray = array_count_values($matches[0]);

                            $hashtag_Array = array_keys($hashtagArray);

                              if(count($hashtag_Array) > 0) // si des hashtags sont trouvés
                            {
                              foreach ($hashtag_Array as $key => $hashtag):

                              $hashtag = str_replace('#', '', $hashtag); // suppression du symbole #

                              $this->hashtag($hashtag); // envoi à la fonction hashtag() pour traitement en BDD

                              $hashtag_Array[$key] = $hashtag; // mise à jour du tableau avec les hashtags sans #

                              endforeach;
                            }
                          }

                            return $hashtag_Array;

          }

            /**
                 * Méthode hashtag
                 *
                 * Vérifie si le hashtag utilisé existe déjà
                 *
                 * Paramètres : $hashtag -> variable contenant un hashtag préalablement trouvé
                 *
            */

              private function hashtag($hashtag)
            {

              $table_hashtag = TableRegistry::get('Hashtag');

              $query = $table_hashtag->find()
                                      ->select(['hashtag'])
                                      ->where(['hashtag' => $hashtag ]);

                    if($query->isEmpty()) // hashtag inexistant, on crée une nouvelle entitée
                  {

                    $newhashtag = $table_hashtag->newEmptyEntity();

                    $newhashtag->hashtag = $hashtag;

                    $newhashtag->nb_post_hashtag = 1;

                    $table_hashtag->save($newhashtag);

                  }

                    else // incrémentation de 1 ou plus
                  {
                      $query = $table_hashtag->query();

                      $query->update()
                            ->set($query->newExpr('nb_post_hashtag = nb_post_hashtag + 1'))
                            ->where(['hashtag' => $hashtag])
                            ->execute();

                    }
            }
}
