<?php
declare(strict_types=1);

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Blocage cell
 */
class BlocageCell extends Cell
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
      $this->loadModel('Blocage');
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display($authname, $username)
    {
      /**
       * Méthode display : affiche les boutons correspondant à un profil en cours de visite, aux résultats du moteur de recherche
       * ou à mes abonnés afin de déterminer si je peut les bloquer ou les débloquer
       *
       * Paramètres : $authname -> nom de la personne actuellement connectée, $username -> nom du profil que je visite ou résultat de recherche ou abonnés
       *
       * On récupère l'état actuel du blocage
       */

          $testblock = $this->Blocage->find()->where(['bloqueur' => $authname,'bloque' => $username]);


              if($testblock->isEmpty()) // pas d'abonnement existant
          {
              $infoblock = 'noblock';
          }
              else
          {

              $infoblock = 'block';
          }

          $this->set('infoblock', $infoblock);
          $this->set('username',$username);

    }
}
