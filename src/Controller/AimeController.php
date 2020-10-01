<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Http\Exception\NotFoundException;


/**
 * Aime Controller
 *
 * @property \App\Model\Table\AimeTable $Aime
 * @method \App\Model\Entity\Aime[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AimeController extends AppController
{

  /**
     * View method
     *
     * @param string|null $id Aime id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view()
    {

        $this->viewBuilder()->setLayout('ajax');

        
        // récupération des informations sur les personnes que je suis

            $username_like = $this->Aime->find()

            ->select(['username'])

            ->where(['id_tweet' =>  $this->request->getParam('idtweet')]);

            $this->set('username_like', $this->Paginator->paginate($username_like, ['limit' => 30]));
    }

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

            $query_like = $this->Aime->find()->select(['id_like'])->where([
                                                                            'username' => $this->Auth->user('username'),
                                                                            'id_tweet' => $jsonData]);

                if($query_like->isEmpty()) // pas de like existant
            {

                $aime = $this->Aime->newEmptyEntity();

                    $data = array(
                                    'username' => $this->Auth->user('username'),
                                    'id_tweet' => $jsonData
                            
                                );
                           
                $aime = $this->Aime->patchEntity($aime, $data);

                    // ajout d'un like

                if ($this->Aime->save($aime)) 
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'addlike']));
             }
                else // échec création d'un like, renvoi d'une réponse au format JSON
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'probleme']));
            }
        }

// suppression d'un like

            else
        {

            // recherche de l'id du like existant

            $query_like = $this->Aime->find()->select(['id_like'])->where([
                                                                        'username' => $this->Auth->user('username'),
                                                                        'id_tweet' => $jsonData
        ]);

            foreach ($query_like as $query_like) 
        {
            $id_like = $query_like['id_like'];
        }

            $entity = $this->Aime->get($id_like); // chargement de l'entité

            $result = $this->Aime->delete($entity); // suppression de l'entité

                if ($result) // suppression d'un like , renvoi d'une réponse au format JSON
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'dislike']));                         
            }
                else // échec suppression, renvoi d'une réponse au format JSON
            {
                return $this->response->withType('application/json')->withStringBody(json_encode(['Result' => 'probleme']));                          
            }
    }
}

            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
}

}
