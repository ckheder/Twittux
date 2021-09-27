<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Datasource\ConnectionManager;

/**
 * Conversation cell
 */
class ConversationCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
    public function initialize(): void
    {

      $this->loadModel('UserConversation');
    }


    /**
     * method userconv
     *
     * Récupérer les utilisateurs d'une conversation
     *
     * Paramètres : $conv -> identifiant de la conversation, $authname : utilisateur courant, afin de ne pas l'afficher sur la liste des membres d'une conversation
     */


      public function usersconv($conv, $authname)
    {


      $users_conv = $this->UserConversation->find()
                                        ->select(['user_conv'])
                                        ->where(['conversation' => $conv])
                                        ->where(['user_conv !=' => $authname]);


      $this->set('usersconv' , $users_conv);
      $this->set('conv', $conv);
    }
}
