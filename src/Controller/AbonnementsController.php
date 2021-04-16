<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use App\Event\AbonnementListener;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;

/**
 * Abonnements Controller
 *
 * @property \App\Model\Table\AbonnementsTable $Abonnements
 * @method \App\Model\Entity\Abonnement[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
    class AbonnementsController extends AppController
{

        public $paginate = [
                        'limit' => 10,
                        'maxLimit' => 30

                        ];

        public function initialize() : void
      {

        parent::initialize();

        //listener qui va écouté la création d'un nouvel abonnement

        $AbonnementListener = new AbonnementListener();

        $this->getEventManager()->on($AbonnementListener);

      }
    /**
     * Méthode Abonnement
     *
     * Retourne la liste des abonnements dela personne
     *
     * Paramètre : username donné en URL
     */

        public function abonnements()
    {
           $this->viewBuilder()->setLayout('follow');

           // titre de page dynamique

            $this->set('title', ''.$this->request->getParam('username').'| Abonnements');

        // récupération des informations sur les personnes que je suis

            $abonnement_valide = $this->Abonnements->find()

            ->select(['Users.username','Users.description'])

            ->where(['Abonnements.suiveur' =>  $this->request->getParam('username') ])

            ->where(['etat' => 1])

            ->order((['Users.username' => 'ASC']))

            ->contain(['Users']);

            $this->set('abonnement_valide', $this->Paginator->paginate($abonnement_valide, ['limit' => 30]));
    }

    /**
     * Méthode Abonné
     *
     * Retourne la liste des abonnés
     *
     * Paramètre : username donné en URL
     */

            public function abonnes()
        {
            $this->viewBuilder()->setLayout('follow');

            //titre de page dynamique

            $this->set('title', ''.$this->request->getParam('username').'| Abonnés');

        // récupération des informations sur les personnes qui me suivent

            $abonne_valide = $this->Abonnements->find()

            ->select(['Users.username','Users.description'])

            ->leftjoin(
                    ['Users'=>'users'],
                    ['Users.username = (Abonnements.suiveur)']
                    )

            ->where(['suivi' =>  $this->request->getParam('username'),'etat' => 1 ])

            ->order((['Users.username' => 'ASC']));

            $this->set('abonne_valide', $this->Paginator->paginate($abonne_valide, ['limit' => 30]));
        }

    /**
     * Méthode add
     *
     * Ajout d'un nouvel abonnement
     *
     * Paramètre : username donné en URL
     */
        public function add()
    {
            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

            $username = $jsonData->username; //nom de la personne à ajouté

            // vérification blocage : si je suis bloqué, je ne peut pas m'abonner

            if(AppController::checkblock($username, $this->Authentication->getIdentity()->username) == 'oui')
          {

            return $this->response->withType('application/json')
                                    ->withStringBody(json_encode(['Result' => 'userblock']));

          }

            // vérification de l'existence d'un abonnement

            $check_abo  = $this->Abonnements->find()
                                            ->where(['suiveur' => $this->Authentication->getIdentity()->username, 'suivi' => $username]);


            // je suis déjà abonné , renvoi d'une réponse au format JSON

                if(!$check_abo->isEmpty())
            {

                return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'dejaabonne']));
            }

              if(AppController::get_type_profil($username ) == 'prive') // si c'est un profil prive
            {
              $etat = 0; // demande d'abonnement car profil prive
            }
              else
            {
              $etat = 1; // abonnement validé car profil public
            }

            // création d'un nouvel abonnement

                $abonnement = $this->Abonnements->newEmptyEntity();

                $data = array(
                                'suiveur' => $this->Authentication->getIdentity()->username, // moi
                                'suivi' =>  $username, // personne que je veut suivre
                                'etat' => $etat
                            );

            $abonnement = $this->Abonnements->patchEntity($abonnement, $data);

                    if ($this->Abonnements->save($abonnement)) // création d'abonnement réussie, renvoi d'une réponse au format JSON
                {


                  if(AppController::check_notif('abonnement', $username ) == 'oui') // si la personne a laquell je m'abonne accepte les notifications d'abonnement
                {

                  // Evènement de création d'une notification de d'abonnement ou de demande

                  $event = new Event('Model.Abonnement.afteradd', $this, ['data' => $data]);

                  $this->getEventManager()->dispatch($event);

                }

                    if($etat == 0) // demande d'abonnement réussie
                  {
                    return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'demandeok']));
                  }
                    else // abonnement réussie
                  {
                    return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'abonnementajoute']));
                  }

                }
                  else // impossible de s'abonner
                {
                    return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'abonnementnonajoute']));
                }
        }

            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
    }

    /**
     * Méthode cancel
     *
     * Annuler une demande d'abonnement
     *
     * Paramètre : username donné en URL
     */
        public function cancel()
    {
            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

            $username = $jsonData->username; //nom de la personne concerné par la demande

            $statement = ConnectionManager::get('default')->prepare(
            'DELETE FROM abonnements WHERE suiveur = :suiveur AND suivi = :suivi AND etat = 0');

            $statement->bindValue('suiveur', $this->Authentication->getIdentity()->username, 'string');
            $statement->bindValue('suivi', $username, 'string');
            $statement->execute();

            $rowCount = $statement->rowCount();

                if ($rowCount == 1) // annulation réussie, envoi d'une réponse au format JSON
            {

                return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'demandeannule']));
            }
                elseif ($rowCount == 0) // échec annulation, envoi d'une réponse au format JSON
            {

                return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'demandenonannule']));
            }

        }
            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
    }

    /**
     * Méthode delete
     *
     * Suppressiopn d'un abonnement
     *
     * Paramètre : username donné en URL
     */
        public function delete()
    {
            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

            $username = $jsonData->username; //nom de la personne concerné par la demande

            $statement = ConnectionManager::get('default')->prepare(
            'DELETE FROM abonnements WHERE suiveur = :suiveur AND suivi = :suivi AND etat = 1');


            $statement->bindValue('suiveur', $this->Authentication->getIdentity()->username, 'string');
            $statement->bindValue('suivi', $username, 'string');
            $statement->execute();

            // Récupération du nombre de ligne affectée

            $rowCount = $statement->rowCount();

                if ($rowCount == 1) // abonnement supprimée avec succès , renvoi d'une réponse au format JSON
            {
                return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'abonnementsupprime']));
            }
                elseif ($rowCount == 0) // échec suppression, renvoi d'une réponse au format JSON
            {
                return $this->response->withType('application/json')
                                        ->withStringBody(json_encode(['Result' => 'abonnementnonsupprime']));
            }

        }
            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
    }



    /**
     * Méthode Demande
     *
     * Retourne la liste des demande d'abonnements (page accessible uniquement par moi)
     *
     * Paramètre : $this->Authentication->getIdentity()->username -> variable Auth contenant l'username
     */

        public function demande()
    {

        $this->viewBuilder()->setLayout('follow');

        $this->set('title', 'Demande(s) de suivi');

        // Récupération des informations sur mes demandes d'abonnements

         $abonnement_attente = $this->Abonnements->find()

        ->select([
                    'Users.username','Users.description'
                ])

        ->leftjoin(
                    ['Users'=>'users'],
                    ['Users.username = (Abonnements.suiveur)']
                    )

        ->where(['suivi' =>  $this->Authentication->getIdentity()->username,'etat' => 0])

        ->order((['Users.username' => 'ASC']))

        ->limit(10);

        $this->set('abonnement_attente', $this->paginate($abonnement_attente, ['limit' => 10]));


    }

        /**
     * Méthode Request
     *
     * Traitement des demandes d'abonnements
     *
     * Les données sont envoyées en AJAX au format JSON depuis la page demande
     */

        public function request()
    {
            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

            $username = $jsonData->username; //nom de la personne concerné par la demande

            $action = $jsonData->action; // accept/refuse

                if($action == 'accept') // j'accepte la demande d'abonnement
            {
                // mise à jour de la ligne concernée

                $statement = ConnectionManager::get('default')->prepare(
                'UPDATE abonnements SET etat = 1 WHERE suiveur = :suiveur AND suivi = :suivi');

                $statement->bindValue('suiveur', $username, 'string');
                $statement->bindValue('suivi', $this->Authentication->getIdentity()->username, 'string');
                $statement->execute();

                // récupération du nombre de ligne affectée: ici 1

                $rowCount = $statement->rowCount();

                    if ($rowCount == 1) // 1 ligne affectée : renvoi d'une réponse JSON
                {
                    return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'accept']));
                }
                    else // renvoi d'une réponse JSON signifiant un échec
                {
                    return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'noaccept']));
                }
            }
                elseif ($action == 'refuse') // je refuse une demande d'abonnement
            {

                // suppression de la ligne concernée

               $statement = ConnectionManager::get('default')->prepare(
                'DELETE FROM abonnements WHERE suiveur = :suiveur AND suivi = :suivi AND etat = 0');

                $statement->bindValue('suiveur', $username, 'string');
                $statement->bindValue('suivi', $this->Authentication->getIdentity()->username, 'string');
                $statement->execute();

                // récupération du nombre de ligne affectée: ici 1

                $rowCount = $statement->rowCount();

                if ($rowCount == 1) // 1 ligne affectée : renvoi d'une réponse JSON
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'refuse']));
            }

                else // renvoi d'une réponse JSON signifiant un échec
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'norefuse']));
            }

              }
          }
                else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }

    }
}
