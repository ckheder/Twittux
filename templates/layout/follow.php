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
    <?= $this->Html->script('//unpkg.com/@webcreate/infinite-ajax-scroll/dist/infinite-ajax-scroll.min.js'); ?>
</head>

<!-- BODY -->

<body>

        <?= $this->element('navbar') ?>

        <?= $this->element('modaltweet') ?>

    <div class="w3-container w3-content" style="max-width:1400px;margin-top:60px;">

      <div class="w3-col m3">

        <!-- lien de navigation sur la page social -->

         <div class="w3-bar-block w3-white">

           <!-- lien vers la pages des abonnements d'une personne -->

            <button class="w3-bar-item w3-button w3-red linkso" id="following" onclick="loadSocialItem('<?= $this->request->getParam('username') ?>', 'following')"><i class="fas fa-user-circle"></i> Abonnements</button>

     			<!-- lien vers la pages des abonnés d'une personne -->

     			  <button class="w3-bar-item w3-button linkso" id="followers" onclick="loadSocialItem('<?= $this->request->getParam('username') ?>', 'followers')"><i class="far fa-user-circle"></i> Abonnés</button>

          <?php

            if($this->request->getParam('username') == $authName) // si l'utilisateur courant visite sa propre page de 'social' on affiche les liens de demannde et d'utilisateurs bloqué
          {

           ?>
           <!-- lien vers la pages des demande d'abonnements -->

   				 <button class="w3-bar-item w3-button linkso" id="requests" onclick="loadSocialItem('<?= $this->request->getParam('username'); ?>', 'requests')"><i class="fas fa-user-friends"></i> Demande</button>

   				<!-- lien vers la pages des utilisateurs bloqués -->

   				 <button class="w3-bar-item w3-button linkso" id="usersblocks" onclick="loadSocialItem('<?= $this->request->getParam('username'); ?>', 'usersblocks')"><i class="fas fa-user-lock"></i> Utilisateurs bloqués</button>

           <?php

          }

            ?>

       </div>

     </div>

     <div hidden class="spinner"></div> <!-- image de chargement des données -->

       <div id="socialsinfos">

            <?= $this->fetch('content') ?>

          </div>

        </div>

    </div>

          <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->
          <?= $this->Html->scriptBlock(sprintf(
                                                'var csrfToken = %s;',
                                              json_encode($this->request->getAttribute('csrfToken'))
                                              )); ?>

<!-- script JS -->

<script>


  var currentuser = "<?= $this->request->getParam('username') ?>"; // savoir sur quel "groupe" d'url je me trouve : hashtag/search

</script>

        <?= $this->Html->script('follow.js'); ?> <!-- traitement des actions : delete un abonnement, répondre à une demande,afficher les notifications correspondantes,... -->

</body>

</html>
