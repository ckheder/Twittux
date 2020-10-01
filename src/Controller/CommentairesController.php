<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Http\Exception\NotFoundException;

/**
 * Commentaires Controller
 *
 * @property \App\Model\Table\CommentairesTable $Commentaires
 * @method \App\Model\Entity\Commentaire[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentairesController extends AppController
{

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

            if ($this->request->is('ajax')) // requête AJAX uniquement
        {
            $commentaire = $this->Commentaires->newEmptyEntity();

            $idcomm = $this->idcomm(); // génération d'un nouvel identifiant de tweet

            $data = array(
                            'id_comm' => $idcomm,
                            'commentaire' => AppController::linkify_content($this->request->getData('commentaire')),
                            'id_tweet' => $this->request->getData('id_tweet'),
                            'username' => $this->Auth->user('username')
                            );

            $commentaire = $this->Commentaires->patchEntity($commentaire, $data);

                if ($this->Commentaires->save($commentaire)) 
            {

                return $this->response->withType("application/json")->withStringBody(json_encode($commentaire));
            }
           
         }
     
            else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
   
    }

    /**
     * Delete method
     *
     * @param string|null $id Commentaire id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //$this->request->allowMethod(['post', 'delete']);


            if ($this->request->is('ajax')) // requête AJAX uniquement
        {

        $commentaire = $this->Commentaires->get($id);

        if($commentaire->username == $this->Auth->user('username'))
        {

        if ($this->Commentaires->delete($commentaire)) {
            //$this->Flash->success(__('The commentaire has been deleted.'));

            return $this->response->withStringBody('ok');
        }
        } else {
            //$this->Flash->error(__('The commentaire could not be deleted. Please, try again.'));

            return $this->response->withStringBody('nonok');
        }

        //return $this->redirect($this->referer());
    }
                else // en cas de non requête AJAX on lève une exception 404
        {
            throw new NotFoundException(__('Cette page n\'existe pas.'));
        }
}

     /**
     * Méthode Idcomm
     *
     * Calcul d'un id de comm aléatoire
     *
     * Sortie : $idtweet -> id de comm
     *
     *
*/
        private function idcomm()
    {

        $idcomm = rand();

        // on vérifie si il existe déjà

        $query = $this->Commentaires->find()
                                ->select(['id_comm'])
                                ->where(['id_comm' => $idcomm]);

                if ($query->isEmpty())
            {
                return $idcomm;
            }
                else
            {
                idcomm(); // ou $this->idcomm();
            }
    }
}
