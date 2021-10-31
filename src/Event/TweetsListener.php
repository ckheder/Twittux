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
            'Model.Tweets.afteradd' => 'notifcitation'
        ];
    }

/**
     * Méthode notifcitation
     *
     * Création d'une notification de citation
     *
     * Paramètres : $usertweet => utilisateur ayant cité, $usercitation => utilisateur cité, $idtweet => identifiant du tweet concerné
     *
*/

      public function notifcitation($event, $usertweet, $usercitation, $idtweet) 
    {

        // envoi d'une notification a chaque personne cité dans un tweet

        $entity = TableRegistry::get('Notifications');

        $notif = '<img src="/twittux/img/avatar/'.$usertweet.'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/"/><a href="/twittux/'.$usertweet.'" class="w3-text-indigo">'.$usertweet.'</a> à vous à cité dans un <a href="/twittux/statut/'.$idtweet.'" class="w3-text-indigo">tweet.</a>';

        $notif_cite = $entity->newEmptyEntity();

        $notif_cite->user_notif = $usercitation;

        $notif_cite->notification = $notif;

        $notif_cite->statut = 0;

        $notif_cite->created =  Time::now();

        $entity->save($notif_cite);

    }

/**
     * Méthode getUsernames
     *
     * Extraction des usernames précédés d'un @ dans un tweet
     *
     * Paramètres : $string => le tweet posté
     *
*/

            function getUsernames($string)
          {

              preg_match_all("/(^|[^@\w])@(\w{1,15})\b/", $string, $matches); // recherche par Regex

                  if ($matches) // si il y'a des résultats, on les stocks dans un tableau
                {
                    $atusernameArray = array_count_values($matches[0]); // comptage des valeurs du tableau

                    $at_username = array_keys($atusernameArray);
                
                    if(count($at_username) > 0) // si des hashtags sont trouvés
                  {
                    foreach ($at_username as $key => $username):

                    $username = str_replace('>@', '', $username); // suppression du symbole @

                    $at_username[$key] = $username; // mise à jour du tableau avec les username sans @

                    endforeach;
                  }
                } 
                    return $at_username;
          }

/**
     * Méthode getHashtags
     *
     * Extraction des mots précédés d'un # dans un tweet
     *
     * Paramètres : $string => le tweet posté
     *
*/

            function getHashtags($string)
          {

            preg_match_all("/(#\w+)/u", $string, $matches); // recherche par Regex

              if ($matches) // si il y'a des résultats, on les stocks dans un tableau
            {
                $hashtagArray = array_count_values($matches[0]); // comptage des valeurs du tableau

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
