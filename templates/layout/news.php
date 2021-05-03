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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>

<!-- BODY -->

<body>

        <?= $this->element('navbar') ?>

        <?= $this->element('modallike') ?>

        <?= $this->element('modaltweet') ?>

          <div class="w3-container w3-content" style="max-width:1400px;margin-top:50px;">

            <!--

            * Menu de tri des tweets sur la page d'actualités
            *
            */ -->

             <div class="w3-col m3" style="margin-top:16px">

            		<div class="w3-bar-block w3-white">

            			<!-- lien de tri vers les plus récents -->

                     <button id="showtmostrecentweets" class="w3-bar-item w3-button tablinknews w3-red"><i class="fas fa-clock"></i> Les plus récents</button>

                 <!-- lien de tri vers les plus commentés -->

                    	<button id="showtmostcommentsweets" class="w3-bar-item w3-button tablinknews"><i class="fas fa-comments"></i> Les plus commentés</button>

           		</div>

           	</div>

            <div class="w3-col m6">

              <div hidden class="spinner"></div> <!-- image de chargement des données -->

                <div id="list_actu_online">

                  <?= $this->fetch('content') ?>

                </div>

          </div>

          <!-- Right Column -->

          <?php

            if (!$this->request->isMobile()) // si je ne suis pas sur mobile, affichage de la cell des hashtags
          {
          ?>
            <div class="w3-col m3">

              <div class="w3-panel w3-border w3-light-grey">

                <h4 class="w3-center"><i class="fas fa-globe"></i> Tendances</h4>

                  <?= $this->cell('Hashtag'); ?>

              </div>

            </div>

          <!-- End Right Column -->

          <?php

          }

          ?>

        </div>

          <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->
          <?= $this->Html->scriptBlock(sprintf(
                                                'var csrfToken = %s;',
                                              json_encode($this->request->getAttribute('csrfToken'))
                                              )); ?>

<!-- script JS -->

        <?= $this->Html->script('news.js'); ?> <!-- traitement des actions : delete un abonnement, répondre à une demande,afficher les notifications correspondantes,... -->

</body>

</html>
