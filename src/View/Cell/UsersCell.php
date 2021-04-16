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
     * Paramètres : $username -> profil courant, $authname -> utilisateur connecté visitant le profil, $no_see -> (valeur 0,1 ou 2) état du blocage du username vis a vis du authname
     */
        public function display($username, $authname = null, $no_see = null)
    {

        $usersinfos = $this->Users->find()
                                    ->select(['description','lieu','website','created'])
                                    ->where(['username' => $username]);

        $this->set('usersinfos', $usersinfos); // infos utilisateur courant
        $this->set('username',$username);
        $this->set('authName',$authname);
        $this->set('no_see',$no_see);
    }
}
