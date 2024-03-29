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

      if($authName) // si je suis authentifié, chargement de la navbar, de la modal de like et de la modale pour tweeter
    {

      echo $this->element('navbar');

      echo $this->element('modallike');

      echo $this->element('modaltweet');

    }
      else // chargement de la navbar offline et de la modal de connexion
    {
      echo $this->element('offlinenavbar');

      echo $this->element('modallogin') ;

    }
?>

    <div class="w3-container w3-content" style="max-width:1400px;margin-top:70px;">

        <div class="w3-row">

                <div class="w3-row-padding">

                  <div class="w3-round">

<!--zone de notification -->

                    <div id="alert-area" class="alert-area"></div>

<!--fin zone de notification  -->

<!-- lien de tri de la recherche -->
<div class="w3-col m2 menusearch">

  <div class="w3-bar-block w3-white">

    <button class="w3-bar-item w3-button linksearch w3-red" id="searchtweets" onclick="loadSearchItem('searchtweets')"><i class="fas fa-pencil-alt"></i> Tweets</button>

    <button class="w3-bar-item w3-button linksearch" id="searchusers" onclick="loadSearchItem('searchusers')"><i class="fas fa-user"></i> Personnes</button>

    <button class="w3-bar-item w3-button linksearch" id="searchmostrecent" onclick="loadSearchItem('searchmostrecent')"><i class="fas fa-clock"></i> Récent</button>

    <button class="w3-bar-item w3-button linksearch" id="searchmediapics" onclick="loadSearchItem('searchmediapics')"><i class="fas fa-image"></i> Média</button>

  </div>

</div>

<div class="w3-col m7 contentsearch">

  <div hidden class="spinner"></div> <!-- image de chargement des données -->

<!-- zone d'affichage des résultats -->

  <div class="w3-container w3-center w3-light-grey">

    <h4>
        <b><i class="fas fa-search"></i> <?=  $this->request->getParam('query') ;?> </b>
    </h4>

  </div>

    <div id="result_search">

          <?= $this->fetch('content') ?>

    </div>

</div>

            </div>

        </div>

  </div>

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

<?= $this->Html->scriptStart(); ?>

          <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->
          <?= sprintf(
                        'var csrfToken = %s;',
                        json_encode($this->request->getAttribute('csrfToken'))
                      ); ?>


  var keyword = "<?= $this->request->GetParam('query') ?>"; <!-- mot clé de a recherche -->

  var currenturl = "<?= $currenturl ?>"; <!-- savoir sur quel "groupe" d'url je me trouve : hashtag/search -->

  var authname = "<?= $authName ?>"; <!-- utilisateur authentifié --> 

<?= $this->Html->scriptEnd();?>

<?= $this->Html->script('search.js'); ?> <!-- traitement des liens du menu, ajout/demande/suppression d'ami depuis les résultats de recherche -->

</body>

</html>
