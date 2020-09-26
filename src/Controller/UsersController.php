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

        //listener qui va écouté la création d'un nouvelle utilisateur

        $UserListener = new UserListener();

        $this->getEventManager()->on($UserListener);

    }

        public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add', 'logout', 'delete','searchusers']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
        public function view()
    {
        $this->set('title' , ''.$this->request->getParam('username').'/ Twittux');

        $this->viewBuilder()->setLayout('profil');
        //$user = $this->Users->find()->select([
                                                //'username'])
        //->where([''])

        //$this->set('user', $user);
    }

    /**
     * Méthode Add : création d'un nouvel utilisateur
     *
     */
        public function add()
    {
        $user = $this->Users->newEmptyEntity(); // création d'une nouvell entité

        if ($this->request->is('post')) // requête POST
    { 
        $user = $this->Users->patchEntity($user, $this->request->getData()); // mise à jour de la nouvelle entité
         
                if ($this->Users->save($user)) 
            {

                $this->Auth->setUser($user); // on authentifie l'utilisateur directement

                 // Création de la ligne settings, du dossier utilisateur, avatar par défaut

                $event = new Event('Model.User.afteradd', $this, ['user' => $user]);

                $this->getEventManager()->dispatch($event);

                $this->Flash->success(__('Inscription réussie, bienvenue '.h($this->request->getData('username')).' sur Twittux.'));

                // redirection vers le nouveau profil

                return $this->redirect('/'.$this->Auth->user('username').'');

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
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Méthode Login : authentification d'un utilisateur
     */

       public function login()
    {

        $this->viewBuilder()->setLayout('login'); // définition du layout

            if ($this->request->is('post')) // requête POST
        {

        // Authentification

        $user = $this->Auth->identify();

                if ($user) // Authentification réussie
            { 
                $this->Auth->setUser($user);
                return $this->redirect('/'.$this->Auth->user('username').'');

            }

            $this->Flash->error('Votre identifiant ou votre mot de passe est incorrect.');

            return $this->redirect('/');

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

        return $this->redirect($this->Auth->logout()); // redirection vers l'accueuil
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
