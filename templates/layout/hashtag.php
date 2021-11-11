<?php
use Cake\Routing\Router;
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

  <?php

        if($authName) // si je suis authentifié, chargement de la navbar et de la modal pour tweeter
      {

        echo $this->element('navbar');

        echo $this->element('modaltweet');

      }
        else // chargement de la navbar offline et de la modal de connexion
      {
        echo $this->element('offlinenavbar');

        echo $this->element('modallogin') ;

      }
?>
        <div class="w3-container w3-content w3-medium" style="max-width:1400px;margin-top:50px;">

          <?php

            if (!$this->request->isMobile()) // si ne suis pas sur mobile, affichage d'une div d'information
          {
          ?>

          <div class="w3-col m4 w3-panel w3-light-grey w3-border ">

            <h4 class="w3-center"><i class="fas fa-info"></i> Informations</h4>

              <p>
                  Tous les sujets les plus discutés du moment sur Twittux.
              </p>

              <p>
                  Vous pouvez consulter ces discussions et y participer en utilisant le hashtag concerné.
              </p>

              <p>
                  Vous pouvez même lancer votre propre sujet tendance en utilisant un hashtag non lancé.
              </p>

          </div>

          <?php

        } ?>

          <div class="w3-col m6">

            <?= $this->fetch('content') ?>

          </div>

        </div>

<!-- Right Column -->

<!-- script JS -->

<?= $this->Html->scriptStart(); ?>

  var authname = "<?= $authName ?>"; <!-- utilisateur authentifié -->

<?= $this->Html->scriptEnd();?>

<?= $this->Html->script('hashtag.js'); ?> <!-- Infinite Ajax Scroll de la liste des hashtags -->

</body>

</html>
