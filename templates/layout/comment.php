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

  <?php

      if($authName) // si je suis connecté, chargement de la navbar et de la modal pour tweet
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

    <div class="w3-container w3-content" style="max-width:1400px;margin-top:50px;">

<div class="w3-col m2">&nbsp;</div>

      <div class="w3-col m6">

        <div class="w3-container">

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

<!-- test pour déterminer si le tweet visité est privé ou public : va servir en javascript pour charger ou non des éléments comme le formulaire de commentaire -->

<?php

  if(isset($no_see)) // si la variable existe dans la vue, c'est un tweet privé
{
  $no_see = 1; // tweet privé
}
  else
{
  $no_see = 0; // tweet public
}

?>

<script>

var no_see = "<?= $no_see ?>";

</script>

          <?= $this->Html->script('commentaire.js'); ?> <!-- ajout d'un commentaire, supprimer un commentaire,emoji -->

</body>

</html>
