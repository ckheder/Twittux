<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Partage cell
 */
class PartageCell extends Cell
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
        $this->loadModel('Partage');
    }

    /**
     * Default display method.
     * 
     * Détermine si j'ai déjà partagé ou pas un tweet sur la page de profil, news et le moeteur de recherche
     * 
     * Paramètres : $username : utilisateur connecté | $auttweet : auteur du tweet | $idtweet -> identifiant du tweet concerné
     * 
     * @return void
     */
        public function display($username, $auttweet, $idtweet)
    {

        $partagestatut = $this->Partage->find()->where(['username' => $username,'id_tweet' => $idtweet])->count();

        // renvoi du résultat
        
        $this->set('partagestatut',$partagestatut);

        // renvoi de l'identifiant du tweet
    
        $this->set('idtweet', $idtweet);

        // renvoi du propriétaire du tweet

        $this->set('auttweet', $auttweet);

    }
}
