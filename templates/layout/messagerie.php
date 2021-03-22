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

        <?= $this->element('navbar') ?> <!-- barre de navigation -->

        <?= $this->element('modaltweet') ?> <!-- modale d'envoi de tweet -->

        <?= $this->element('modalinvitconv') ?> <!-- modale d'invitation à rejoindre une conversation -->

    <div class="w3-container w3-content" style="max-width:1400px;">

        <div class="w3-row">

          <!-- liste des conversations -->

              <div class="w3-col m3">

                <div class="w3-center">

                <h4>

                  <i class="fas fa-comment-dots"></i>&nbsp;&nbsp;Discussions</h4>

                </div>

                <div hidden class="spinner"></div> <!-- spinner de chargement de la liste des conversations -->

              <div id = "listconv">

              <!-- affichage de la liste des conversations -->

              </div>

            </div>

          <?= $this->fetch('content') ?>

        </div>

    </div>

    <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->

    <?= $this->Html->scriptBlock(sprintf(
                                          'var csrfToken = %s;',
                                        json_encode($this->request->getAttribute('csrfToken'))
                                        )); ?>

      <?= $this->Html->script('messagerie.js'); ?>

</body>

</html>
