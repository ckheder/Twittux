<?php
namespace App\Event;


use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * Listener UserListener
 *
 * Création de la ligne Settings d'un nouvel enregistré ainsi que les différends dossiers utilisateurs, avatar et cover par défaut | Suppression de toutes les informations après suppression de compte
 *
 */

class UserListener implements EventListenerInterface {

        public function implementedEvents(): array

    {
        return [
            'Model.User.afteradd' => 'addentity',
        ];
    }

/**
     * Méthode addentity
     *
     * Création de la ligne Settings pour un utilisateur nouvellement enregistré, création des dossiers utilisateurs, avatar et cover
     *
     * Paramètres : $user -> tableau contenant le nom de la persone qui vient de s'inscrire
     *
*/

            public function addentity($event, $user)
        {

            //$entity = TableRegistry::get('users')

            //$query = $entity->query();

            // le reste est complété par le SGBD

            //$query->insert(['user_id'])
                    //->values([
                                //'user_id' => $user->username
                         //   ])
                    //->execute();

            //creation du dossier utilisateur

            $dir = new Folder('/var/www/html/twittux/webroot/img/media/'.$user->username.'', true, 0755);

            // copie de l'avatar par defaut

            $srcfile='/var/www/html/twittux/webroot/img/default.png';

            $dstfile='/var/www/html/twittux/webroot/img/avatar/'.$user->username.'.jpg';

            copy($srcfile, $dstfile);


        }

}


