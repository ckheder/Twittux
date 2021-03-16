<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use App\Event\UserListener; // listener personnel pour la création de la ligne settings à l'inscription
use Cake\Http\Exception\NotFoundException;


/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

        public function initialize() : void
    {
        parent::initialize();
        $this->loadModel('Settings'); // chargement du modèle settings

        //listener qui va écouté la création d'un nouvelle utilisateur

        $UserListener = new UserListener();

        $this->getEventManager()->on($UserListener);

    }

        public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['add', 'searchusers','login']);
    }

    /**
     * Méthode Add : création d'un nouvel utilisateur
     *
     */
        public function add()
    {

        if ($this->request->is('post')) // requête POST
    {

      $user = $this->Users->newEmptyEntity(); // création d'une nouvell entité

      $data = array(
                      'username' => $this->request->getData('username'), // nom d'utilisateur
                      'password' => $this->request->getData('password'), // mot de passe
                      'email' =>  $this->request->getData('email'), // adresse mail
                      'description' => 'Aucune description', // description par défaut
                      'lieu' => 'Aucun lieu', // lieu par défaut
                      'website' => 'Aucun site internet'
                    );

        $user = $this->Users->patchEntity($user, $data); // mise à jour de la nouvelle entité

                if ($this->Users->save($user))
            {

                $this->Authentication->setIdentity($user); // connexion manuelle de l'utilisateur

                 // Création de la ligne settings, du dossier utilisateur, avatar par défaut

                $event = new Event('Model.User.afteradd', $this, ['user' => $user]);

                $this->getEventManager()->dispatch($event);

                $this->Flash->success(__('Inscription réussie, bienvenue '.h($this->request->getData('username')).' sur Twittux.'));

                // redirection vers le nouveau profil

                return $this->redirect('/'.$this->Authentication->getIdentity()->username.'');

            }

            // si échec de la création, renvoi des erreurs sur la page d'accueuil

                if($user->getErrors())
              {
                $error_msg = [];

                  foreach($user->getErrors() as $errors)
                {
                      if(is_array($errors))
                    {
                        foreach($errors as $error)
                      {
                            $error_msg[]    =   $error;
                      }
                    }

                      else
                    {
                        $error_msg[]    =   $errors;
                    }
                }

                    if(!empty($error_msg))
                {
                    $this->Flash->error(
                        __("Inscription impossible, veuillez corriger la ou les erreurs ci-dessous<br /><ul><li>".implode("</li><li>", $error_msg)."</li></ul>"), ['escape' => false])
                    ;
                    return $this->redirect('/');
                }
            }
        }
    }

    /**
     * Edit method
     *
     * Modification des informations utilisateurs : avatar, description,...
     *
     *
     */
    public function edit($id = null)
    {

      $this->set('title' , 'Paramètres');

      $this->viewBuilder()->setLayout('settings');

      // récupération des préférénces du type de profil et des choix de notifications

      $settings = $this->Settings->find()
                                  ->select([
                                              'type_profil',
                                              'notif_message',
                                              'notif_citation',
                                              'notif_partage',
                                              'notif_commentaire',
                                              'notif_abonnement'
                                          ])
                    ->where(['username' => $this->Authentication->getIdentity()->username]);

      foreach ($settings as $settings):

              $setup_profil = $settings->type_profil; // type de profil : privé/public
              $notif_message = $settings->notif_message; // accepter ou non les notifications de message
              $notif_citation = $settings->notif_citation; // accepter ou non les notifications de citation
              $notif_partage = $settings->notif_partage; // accepter ou non les notifications de partge de tweets
              $notif_commentaire = $settings->notif_commentaire; // accepter ou non les notifications de nouveau commentaire
              $notif_abonnement = $settings->notif_abonnement; // accepter ou non les notifications de nouvel abonnement

      endforeach;

      //envoi des données à la vue

              $this->set('setup_profil', $setup_profil);
              $this->set('notif_message', $notif_message);
              $this->set('notif_citation', $notif_citation);
              $this->set('notif_partage', $notif_partage);
              $this->set('notif_commentaire', $notif_commentaire);
              $this->set('notif_abonnement', $notif_abonnement);

// traitement des informations utilisateurs

        if ($this->request->is(['post'])) { // requête POST

          $user = $this->Users->get($this->Authentication->getIdentity()->id); // récupération de mes informations

          if(!empty($this->request->getData('submittedfile'))) // avatar envoyé
        {

          $avatar = $this->request->getData('submittedfile');

            if($avatar->getError() == 0) // si pas d'erreur d'envoi
          {

            $imageMimeTypes = array( // type MIME autorisé
                                    'image/jpg',
                                    'image/jpeg'
                                  );

              if($avatar->getSize() > 3047171) // taille du fichier
           {
              return $this->response->withStringBody('sizenotok'); // fichier trop gros
            }

              if(!in_array($avatar->getClientMediaType(), $imageMimeTypes)) // test du type MIME
            {
              return $this->response->withStringBody('typenotok'); // type MIME incorrect
            }

          // renommage du fichier

          $name = $avatar->getClientFilename();

          $name = $this->Authentication->getIdentity()->username . '.jpg';

          $targetPath = 'img/avatar/'. $name.'';

          // déplacement fichier

          $avatar->moveTo($targetPath);

          }

        }

          $data = array(); // création d'un tableau contenant les valeurs modifiées

          // vérification description

            if(!empty($this->request->getData('description'))) // description non vide
          {

            $user->description = strip_tags($this->request->getData('description')); // suppression d'éventuelles balises

            $user->description = AppController::linkify_content($user->description); // parsage

            $data['description'] = $user->description; // stockage dans le tableau data

          }
        // vérification lieu

        if(!empty($this->request->getData('lieu'))) // lieu non vide
      {
        $user->lieu = strip_tags($this->request->getData('lieu')); // suppression d'éventuelles balises

        $data['lieu'] = $user->lieu; // stockage dans le tableau data
      }

      // mot de passe

      if(!empty($this->request->getData('password')))
    {
      if($this->request->getData('password') != $this->request->getData('confirmpassword'))
    {
      return $this->response->withStringBody('notsamepassword'); // adresse mail déjà utlisée
    }
      else
    {
        $data['password'] = $this->request->getData('password'); // stockage dans le tableau data
    }

    }

        // website

        if(!empty($this->request->getData('website')))
      {

        $data['website'] = $this->request->getData('website'); // stockage dans le tableau data

      }

    // adresse mail
    if(!empty($this->request->getData('email'))) // si le champ mail n'est pas vide
  {
      if($this->request->getData('email') != $this->request->getData('confirmemail'))
    {
      return $this->response->withStringBody('notsamemail'); // adresse mail déjà utlisée
    }

    // on vérifie que l'adresse mail n'est pas déjà utilisé

          $verif = $this->Users->find()
                                ->select(['email'])
                                ->where(['email' => $this->request->getData('email')]);

        if ($verif->isEmpty()) // si le mail n'existe pas
      {
        $data['email'] = $this->request->getData('email');
      }
        else
      {
        return $this->response->withStringBody('existingmail'); // adresse mail déjà utlisée
      }
    }

// sauvegarde des données

            $user = $this->Users->patchEntity($user, $data);

            if ($this->Users->save($user))
          {

            return $this->response->withStringBody('updateok'); // mise à jour réussie

          }
            else
          {
            return $this->response->withStringBody('probleme'); // echec ou pas de mise à jour
          }
    }
  }

    /**
     * Delete method
     *
     * Suppression de mon Compte
     */
     public function delete()
    {

      // récupération de l'entité correspondant au profil courant cherchant à delete son compte

      $user = $this->Users->get($this->Authentication->getIdentity()->id);

      // suppression avatar + suppression entité

          if (unlink(WWW_ROOT . 'img/avatar/'.$user->username.'.jpg') AND rmdir(WWW_ROOT . 'img/media/'.$user->username.'') AND $this->Users->delete($user)) // suppression avatar + entitée
        {
            $this->Flash->success(__('Compte supprimé avec succès.'));

            $this->Authentication->logout();

            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

          else
        {
            $this->Flash->error(__('Impossible de supprimer votre compte.'));

            return $this->redirect('/settings');
        }

    }

    /**
     * Méthode Login : authentification d'un utilisateur
     */

       public function login()
    {

        $this->viewBuilder()->setLayout('login'); // définition du layout

        $result = $this->Authentication->getResult(); // récupération du résultaty de l'authentification

          if ($result->isValid()) // authentification réussie
        {
          $user = $this->Authentication->getIdentity(); // on récupère l'identité du connecté

          //  on récupère l'URL de provenance pour rediriger vers celle -ci après identification

          $target = $this->Authentication->getLoginRedirect();

              if (!$target) // je viens de la page d'accueil du site , je suis redirigé vers mon profil
            {
              return $this->redirect('/'.$user->username.'');
            }

              else // je suis redirigé vers la page de provenance
            {
              return $this->redirect($target);
            }
        }

        //mot de passe /login incorrect

          if ($this->request->is('post') && !$result->isValid())
        {
              $this->Flash->error('Nom d\'utilisateur ou mot de passe incorrect.');
        }

    }

        /**
     * Méthode Logout
     *
     * Déconnexion
     *
     */
        public function logout()
    {
        $this->Flash->success('Vous avez été déconnecté.');

        $this->Authentication->logout();

        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    /**
    * Méthode Searchusers
    *
    * Requete de recherche d'utilisateurs pour l'input de recherche sur la navbar
    *
    * Auto-complétion Ajax
    */

        public function searchusers()
    {
         if ($this->request->is('ajax'))
        {
            $this->autoRender = false;

            $name = $this->request->getParam('query'); //terme tapé dans l'input de recherche

            $query_user = $this->Users->find()->select(['username'])
                                              ->where(['username LIKE '  => ''.$name.'%']);

          // pas de résultat, renvoi d'une réponse

              if($query_user->isEmpty())
            {
              return $this->response->withType("application/json")->withStringBody(json_encode('noresult'));
            }
              else
            {
                foreach($query_user as $result) // pour chaque résultat, création d'une ligne de tableau
              {

               $resultUsers[] =  array(
                                        'username' => $result->username
                                        );
              }

            //conversion du tableau en JSON et renvoi de la réponse

            return $this->response->withType("application/json")->withStringBody(json_encode($resultUsers));

                    }
          }

        // accès à la page hors d'une requête Ajax
            else
        {
          throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
    }

}
