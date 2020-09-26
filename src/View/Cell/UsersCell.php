<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Users cell
 */
class UsersCell extends Cell
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
        $this->loadModel('Users');
    }

    /**
     * Méthode display : affiche les informations d'un utilisateur : sur tweet(index, view)
     *
     */
        public function display($username)
    {
        
        $usersinfos = $this->Users->find()
                                    ->select(['description','lieu','created'])
                                    ->where(['username' => $username]);

        $this->set('usersinfos', $usersinfos);
        $this->set('username',$username);
    }
}
