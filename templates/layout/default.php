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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>
<body>

    <div class="w3-content" style="max-width:1400px">

        <div class="w3-row">

          <?= $this->element('offlinenavbar') ?><!-- navbar offline -->

          <?= $this->fetch('content') ?>

          <?= $this->element('modallogin') ?><!-- fenêtre modale de connexion -->

        </div>

<footer class="w3-container w3-dark-grey">

    <p class="w3-center">Conditions d'utilisation - Contribuer - Contact © 2021 Christophe KHEDER. Tous droits réservés.</p>

</footer>

    </div>

<?= $this->Html->script('navbar.js'); ?> <!-- traitement des actions de la navbar -->

</body>
</html>
