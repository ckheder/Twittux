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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>

<!-- BODY -->

<body>

        <?= $this->element('navbar') ?>

        <?= $this->element('modallike') ?>

    <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">

        <div class="w3-row">

              <div class="w3-rest">

                <div class="w3-row-padding">

                  <div class="w3-round">
         
<!--zone de notification -->
                  <div id="alert-area" class="alert-area"></div>
<!--fin zone de notification  -->

<div class="w3-container w3-center w3-light-grey">

  <h4>
      <b><?=  $this->request->getParam('query') ;?> </b>
  </h4>

</div>
<!-- lien de tri de la recherche -->

<?= $this->element('searchmenu') ?>

<div hidden id="spinner"></div> <!-- image de chargement des données -->

<div id="result_search">

          <?= $this->fetch('content') ?>
</div>

                </div>

            </div>

        </div>
  
    </div>
   
          <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->
          <?= $this->Html->scriptBlock(sprintf(
                                                'var csrfToken = %s;',
                                              json_encode($this->request->getAttribute('csrfToken'))
                                              )); ?>

<!-- détermine si la recherche ce fait sur les hashtags ou non (utilisé en AJAX) -->

    <?php

      if(Router::url(null, false) === Router::url(['_name' => 'search', 'query' => $this->request->getParam('query')]))
    {
      $currenturl =  'search';
    }
      else
    {
      $currenturl =  'hashtag';
    }

    ?>

<!-- script JS -->

<script>

  var keyword = "<?= $this->request->GetParam('query') ?>"; // mot clé de a recherche

  var currenturl = "<?= $currenturl ?>"; // savoir sur quel "groupe" d'url je me trouve : hashtag/search

</script>

        <?= $this->Html->script('search.js'); ?> <!-- traitement des liens du menu, ajout/demande/suppression d'ami depuis les résultats de recherche -->
</body>

</html>
