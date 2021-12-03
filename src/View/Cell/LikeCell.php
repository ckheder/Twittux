<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Like cell
 */
class LikeCell extends Cell
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
        $this->loadModel('Aime');
    }

    /**
     * Default display method.
     * 
     * Détermine si je like ou pas un tweet sur la page de profil, news et le moeteur de recherche
     * 
     * Paramètres : $username : utilisateur connecté | $idtweet -> identifiant du tweet concerné
     * 
     * @return void
     */
        public function display($username, $idtweet)
    {

        $likestatut = $this->Aime->find()->where(['username' => $username,'id_tweet' => $idtweet])->count();

        // renvoi du résultat
        
        $this->set('likestatut',$likestatut);

        // renvoi de l'identifiant du tweet
    
        $this->set('idtweet', $idtweet);

    }
}
