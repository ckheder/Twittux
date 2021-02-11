<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Paginator');
        $this->loadModel('Settings');
        $this->loadModel('UserConversation');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');

        $this->loadComponent('Auth', [

            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password'
                    ]
                ]
            ],
            'authError' => 'Vous devez vous identifier pour voir cette page.',
            'loginAction' => [
                'controller' => 'users',
                'action' => 'login'
            ],

             'logoutRedirect' => [
                'controller' => 'Pages',
                'action' => 'display',
                'home'
            ]
        ]);

        $this->Auth->allow(['display', 'view', 'index']);

        $this->set('authName', $this->Auth->user('username')); // nom du connecté
    }

    /**
     * Méthode linkify_content
     *
     * Conversion des @username en lien vers le profil, émoji vers image, # lien cliquable vers le moteur de recherche, URL vers lien cliquable, media vers iframe
     *
     * Paramètre : $content -> contenu du tweet,message,comm
     *
     * Sortie : $content -> contenu parsé
*/
        public function linkify_content($contenu)
    {

        $contenu = preg_replace('/(^|[^@\w])@(\w{1,20})\b/','$1<a href="/twittux/$2" class="w3-text-blue">@$2</a>', $contenu); // @username

        $contenu =  preg_replace('/:([^\s]+):/', '<img src="/twittux/img/emoji/$1.png" class="emoji" alt=" :$1: "/>', $contenu); // emoji

        //URL

        $pattern_link = '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\\\\\\\w]*))?)/';

        $contenu = preg_replace($pattern_link, '<a href="$1" class="w3-text-blue" target="_blank">$1</a>', $contenu);

        // conversion média en lecteur vidéo

// youtube
            if (preg_match('~\[videoYoutube]([^{]*)\[/videoYoutube]~i', $contenu))
        {
            $contenu = preg_replace('~\[videoYoutube]([^{]*)\[/videoYoutube]~i', '<p><iframe src="https://www.youtube.com/embed/$1"  width="100%" height="360" frameborder="0" allowfullscreen></iframe></p>', $contenu);
        }
// dailymotion
            elseif (preg_match('~\[videoDailymotion]([^{]*)\[/videoDailymotion]~i', $contenu))
        {
            $contenu = preg_replace('~\[videoDailymotion]([^{]*)\[/videoDailymotion]~i', '<p><iframe frameborder="0" width="100%" height="360" src="//www.dailymotion.com/embed/video/$1" allowfullscreen></iframe></p>', $contenu);
        }
// clip twitch
            elseif (preg_match('~\[clipTwitch]([^{]*)\[/clipTwitch]~i', $contenu))
        {
        $contenu = preg_replace('~\[clipTwitch]([^{]*)\[/clipTwitch]~i', '<p><iframe src="https://clips.twitch.tv/embed?autoplay=false&clip=$1&tt_content=embed&tt_medium=clips_embed" width="100%" height="360" frameborder="0" scrolling="no" allowfullscreen="true"></iframe></p>', $contenu);
        }
// video twitch
            elseif (preg_match('~\[videoTwitch]([^{]*)\[/videoTwitch]~i', $contenu))
        {
        $contenu = preg_replace('~\[videoTwitch]([^{]*)\[/videoTwitch]~i', '<p><iframe src="https://player.twitch.tv/?autoplay=false&video=v$1" frameborder="0" allowfullscreen="true" scrolling="no" height="378" width="100%"></iframe></p>', $contenu);
        }
// instagram
            elseif (preg_match('~\[InstagramPost]([^{]*)\[/InstagramPost]~i', $contenu))
        {
        $contenu = preg_replace('~\[InstagramPost]([^{]*)\[/InstagramPost]~i', '<p><iframe src="https://www.instagram.com/p/$1/embed/captioned/" width="100%" height="780" frameborder="0" scrolling="no" allowtransparency="true"></iframe></p>', $contenu);
        }
// lien vers une image distante
            elseif (preg_match('~\[imageUrl]([^{]*)\[/imageUrl]~i', $contenu))
        {
        $contenu = preg_replace('~\[imageUrl]([^{]*)\[/imageUrl]~i', '<a href="$1" ><img src="$1" width="100%" alt="img_media"/></a>', $contenu);
        }

        $contenu =  preg_replace('/#([^\s]+)/','<a href="/twittux/search/hashtag/%23$1" class="w3-text-blue">#$1</a>',$contenu); //#something

        return $contenu;
    }


        /**
         * Méthode check_notif
         *
         * Détermine si la personne veut ou pas des notifications, cas particulier des notification de message : on détermine si la conversation est masquée ou pas
         *
         * Paramètre : $notification -> type de notification : comm, abo, citation,... | $username -> profil sur qui faire le test | $conversation -> identifiant de conversation dans le cas d'une notification de message
         *
         * Sortie : $check_notif : oui | non
    */
            public function check_notif($notification, $username, $conversation = null)
        {

          // cas de la messagerie (on vérifie si la conversation n'est pas masquée du côté du destinataire)

            if($notification == 'message')
          {
            $notif_message_visible = $this->Settings
                                    ->find()
                                    ->select(['visible' => 'UserConversation.visible','notif_message' =>'Settings.notif_message'])
                                    ->innerJoin(
                ['UserConversation' => 'userconversation'],
                [
                'Settings.username = UserConversation.user_conv'
              ])

                                    ->where(['Settings.username' =>  $username,'UserConversation.conversation' => $conversation]);

                                    foreach ($notif_message_visible as $notif_message_visible)
                                  {
                                    $visible = $notif_message_visible->visible;
                                    $notif_message = $notif_message_visible->notif_message;
                                  }

                  if($visible == 'non')
                {
                    $check_notif = 'non';
                }
                  elseif ($notif_message == 'non')
                {
                  $check_notif = 'non';
                }
                  else
                {
                  $check_notif = 'oui';
                }

          }
            else // traitement des autres notifications
          {

            $check_notif = $this->Settings->find()->select(['notif_'.$notification.''])->where(['username' => $username]);

            $notif = 'notif_'.$notification.'';

                foreach ($check_notif as $check_notif)
              {
                $check_notif = $check_notif->$notif;
              }
          }

          return $check_notif;

        }

        /**
             * Méthode Get_Type_Profil
             *
             * Récupération du type de profil privé ou public
             *
             * Sortie : public -> profil public | prive -> profil privé
             *
             *
        */
              public function get_type_profil($username)
            {

                $type_profil = $this->Settings->find()
                                                ->select(['type_profil'])
                                                ->where(['username' => $username]);

                            foreach ($type_profil as $type_profil)
                        {
                            $type_profil = $type_profil->type_profil;
                        }

                return $type_profil;
            }

            /**
                 * Méthode IsinConv
                 *
                 * Vérifie si la personne fais partie de la conversation en paramètres : répondre à une invitation de nouveau dans les notifications
                 *
                 * Paramètres : idconv -> identifiant de la conversation, $username -> nom de la personne
                 *
                 * Sortie : oui | non
                 *
                 *
            */

              public function isinconv($idconv,$username)
            {
              $query = $this->UserConversation->find()->where(['conversation' => $idconv,'user_conv' => $username]);

                  if ($query->isEmpty()) // si pas de résultat, on peut l'inviter dans la conversation
                {
                  $inconv = 'non';
                }
                  else
                {
                  $inconv = 'oui';
                }

                return $inconv;
            }

    }
