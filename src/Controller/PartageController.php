<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Datasource\ConnectionManager;

/**
 * Partage Controller
 *
 * @property \App\Model\Table\PartageTable $Partage
 * @method \App\Model\Entity\Partage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PartageController extends AppController
{

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
            if ($this->request->is('ajax') AND $this->request->is('post')) // requête AJAX/POST uniquement
        {

            $jsonData = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

            // vérification d'un like existant

            $query_share = $this->Partage->find()->select(['id_partage'])->where([
                                                                            'username' => $this->Auth->user('username'),
                                                                            'id_tweet' => $jsonData]);

                    if($query_share->isEmpty()) // pas de like existant
                {

                    //création d'une nouvell eentité partage

                $share = $this->Partage->newEmptyEntity();

                $data = array(
                                'username' => $this->Auth->user('username'), // utilisateur qui partage
                                'id_tweet' => $jsonData // identifiant du tweet
                            
                            );
                           
                $share = $this->Partage->patchEntity($share, $data);      

                if ($this->Partage->save($share)) // ajout d'un like réussi, renvoi d'une réponse au format JSON
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'addshare']));
             }
                else // échec création d'un like, renvoi d'une réponse au format JSON
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'probleme']));
            }
        }

            else // renvoi d'une réponse JSON signifiant que l'on à déja partagé ce post
        {

            return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'existshare']));
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
     * Suppression d'un partage
     *
     */
        public function delete()
    {
            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

            $idtweet = $this->request->input('json_decode'); // récupération des informations envoyées en JSON

            // requête de suppression du partage

            $statement = ConnectionManager::get('default')->prepare(
            'DELETE FROM partage WHERE username = :username AND id_tweet = :idtweet');


            $statement->bindValue('username', $this->Auth->user('username'), 'string');
            $statement->bindValue('idtweet', $idtweet, 'string');
            $statement->execute();

            // Récupération du nombre de ligne affectée

            $rowCount = $statement->rowCount(); 

                if ($rowCount == 1) // partage supprimé avec succès , renvoi d'une réponse au format JSON
            {
                return $this->response->withStringBody('tweetsupprime');
            }
                elseif ($rowCount == 0) // échec suppression, renvoi d'une réponse au format JSON
            {
                return $this->response->withStringBody('tweetnonsupprime');
            }
                        
        }
            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
    }
}
