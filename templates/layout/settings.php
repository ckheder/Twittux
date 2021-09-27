<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $title ;?>
    </title>
    <?= $this->Html->meta('favicon.ico','img/favicon.ico', ['type' => 'icon']); ?>
    <?= $this->Html->css('w3');?>
    <?= $this->Html->css('custom');?>
    <?= $this->Html->css('//fonts.googleapis.com/css?family=Athiti'); ?>
    <?= $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css');?>
</head>

<!-- BODY -->

<body>

        <?= $this->element('navbar') ?>

        <?= $this->element('modaltweet') ?>

    <div class="w3-container w3-content" style="max-width:1400px;margin-top:60px;">

        <div class="w3-col m2">&nbsp;</div>

          <div class="w3-col m6">

          <?= $this->Flash->render();?>

            <?= $this->fetch('content') ?>

          </div>

          <!-- Div d'informations sur le paramétrage du compte utilisateur -->

          <div class="w3-col m3 w3-light-grey w3-padding">

            <h4 class="w3-center"><i class="fas fa-info"></i> Informations</h4>

                  <div class="settingsheader">Mes informations</div>

                  <p>

                    <span class ="w3-text-green">

                      <strong>Aucune de vos informations ne sera partagée avec qui que se soit.</strong>

                  </span>

                  </p>

                  <p>

                    Votre description, votre lieux et votre site web sont facultatifs.

                  </p>

                  <p>

                    <span class ="w3-text-red">

                      <strong>Assurez-vous que votre adresse mail est valide pour pouvoir retrouver votre mot de passe si vous l'avez perdu ou pour le réinitialiser.</strong>

                    </span>

                  </p>


              <div class="settingsheader">Paramètres de profil </div>

                <p>

                  Un profil public accepte automatiquement les demandes d'abonnements, tous les tweets sont publics (n'importe qui peut les commenter et les partager, ils sont visibles dans les résultats de recherche).

                </p>

                <p>

                  Un profil privé vous donne le choix de qui peut vous suivre, voir vos tweets (les tweets privés ne se partagent pas), vos tweets n'apparaîtront pas dans le moteur de recherche.

                </p>

              <div class="settingsheader">Supprimer mon compte</div>

                <p>

                  Ceci entraînera la suppression de toutes vos données : informations personnelles, tweets, messages, médias,partage,...

                </p>

                <p>

                  <span class ="w3-text-red">

                    <strong>Aucune données ne sera conservées.</strong>

                  </span>

                </p>

          </div>

    </div>

          <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->
          <?= $this->Html->scriptBlock(sprintf(
                                                'var csrfToken = %s;',
                                              json_encode($this->request->getAttribute('csrfToken'))
                                              )); ?>

<!-- script JS -->

<?= $this->Html->script('settings.js'); ?> <!-- traitement des paramètres : mise à jour des informations utilisateur , des préférences de profil et de notifications -->

</body>

</html>
