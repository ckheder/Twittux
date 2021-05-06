<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Abonnements cell
 */
class AbonnementsCell extends Cell
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
        $this->loadModel('Abonnements');
    }

    /**
     * Méthode testabo : affiche les boutons correspondant à un profil en cours de visite, aux résultats du moteur de recherche
     * ou à mes abonnés afin de déterminer si je peut m'abonner, me désabonner ou si ma demande est en attente
     *
     * Paramètres : $authname -> nom de la personne actuellement connectée, $username -> nom du profil que je visite ou résultat de recherche ou abonnés
     *
     * On récupère l'état actuel de l'abonnement
     */
        public function testabo($authname, $username)
    {
        $testabo = $this->Abonnements->find()->select(['etat'])
                                            ->where(['suiveur' => $authname,'suivi' => $username]);

            if($testabo->isEmpty()) // pas d'abonnement existant
        {
            $infoabo = 'noabo';
        }
            else
        {
            foreach ($testabo as $testabo) // récupération de l'etat de l'abonnement : 0 -> demand en cours, 1 -> abonnement en cours
            {
                $etat_abo = $testabo['etat'];

                    if($etat_abo === 0) // demande
                {
                    $infoabo = 'demande';
                }
                    elseif ($etat_abo === 1) // abonnement en cours
                {
                    $infoabo = 'abonnement';
                }
            }
        }

        $this->set('infoabo', $infoabo);

        $this->set('username',$username);
    }

    /**
     * Méthode infoabo : Récupère le nombre d'abonnement et d'abonné du profil courant
     *
     * Paramètres : $username -> $username -> nom du profil que je visite
     *
     * Renvoi du nombre d'abonnement et d'abonnés
     */
        public function infosabo($username)
    {

        // calcul du nombre d'abonnements du profil courant

        $nb_abonnements = $this->Abonnements->find()->where(['suiveur' => $username,'etat' => 1])->count();

        // renvoi du nombre d'abonnements du profil courant

        $this->set('nbabonnements',$nb_abonnements);

        // calcul du nombre d'abonné du profil courant

        $nb_abonnes = $this->Abonnements->find()->where(['suivi' => $username,'etat' => 1])->count();

        // renvoi du nombre d'abonné du profil courant

        $this->set('nbabonnes',$nb_abonnes);

        // renvoi du nom de l'utilisateur courant pour la génération de liens

        $this->set('username',$username);
    }
}
