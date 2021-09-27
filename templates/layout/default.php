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
        Bienvenue sur Twittux
    </title>
    <?= $this->Html->meta('favicon.ico','img/favicon.ico', ['type' => 'icon']); ?>
    <?= $this->Html->css('w3');?>
    <?= $this->Html->css('custom');?>
    <?= $this->Html->css('//fonts.googleapis.com/css?family=Athiti'); ?>
    <?= $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css');?>
</head>
<body>

  <?= $this->element('modalforgotpassword') ?> <!-- modale d'envoi de tweet -->

    <div class="w3-container">

         <div class="w3-row">

             <?= $this->Flash->render() ?>

          <?= $this->fetch('content') ?>

        </div>

<footer class="w3-container w3-dark-grey">

    <p class="w3-center"><a href="/twittux/privacy">Politique de confidentialité</a> - <a href="/twittux/help">Aide</a> - <a href="https://github.com/ckheder/Twittux" target="_blank">Contribuer/Signaler un problème.</a></p>

</footer>

    </div>

<!-- script JS -->

    <?= $this->Html->script('homepage.js'); ?> <!-- test des cookies, traitement du formulaire d'envoi d'adresse mail pour réinitialiser le mot de passe-->

</body>

</html>
