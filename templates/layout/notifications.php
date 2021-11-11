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
    <?= $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/socket.io/4.1.2/socket.io.js'); ?>
</head>

<!-- BODY -->

<body>

        <?= $this->element('navbar') ?>

        <?= $this->element('modaltweet') ?>

    <div class="w3-container w3-content" style="max-width:1400px;margin-top:60px;">

        <div class="w3-row">

            <?= $this->element('notifmenu') ?>

            <?= $this->fetch('content') ?>

        </div>

    </div>

<?= $this->Html->scriptStart(); ?>

          <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->
          <?= sprintf(
                        'var csrfToken = %s;',
                        json_encode($this->request->getAttribute('csrfToken'))
                        ); ?>

<!-- script JS -->

    var authname = "<?= $authName ?>"; <!-- utilisateur authentifié -->

<?= $this->Html->scriptEnd();?>

<?= $this->Html->script('notifications.js'); ?> <!-- traitement des actions : marquer comme lue, supprimer une notification , tous supprimer -->

</body>

</html>
