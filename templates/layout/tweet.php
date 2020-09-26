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
use Cake\Routing\Router;
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

    <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">

        

        <?= $this->Flash->render();?>

        <div class="w3-row">

            <?php

            //Remplir dynamiquement la cell des informations utilisateurs : si on est sur la page des profils la cell est remplit avec les données du profil courant, si on est sur une autre page, on remplit la cell avec les infos de l'utilisateur authentifié

            $current_route = Router::url(null, false); // récupération de l'URL actuelle

            $route_profil = Router::url(['_name' => 'profil', 'username' => $this->request->getParam('username')]); // URL profil

            if($current_route == $route_profil)
        {
            $username = $this->request->getParam('username');
        }
            else
        {
            $username = $authName;
        }

            ?>

            <?= $this->cell('Users',['username' => $username]); ?> 
 
            <?= $this->fetch('content') ?>

        </div>
  
    </div>

        <!-- génération d'un token CSRF pour l'envoi de données en AJAX -->
            <?= $this->Html->scriptBlock(sprintf(
                                                'var csrfToken = %s;',
                                              json_encode($this->request->getAttribute('csrfToken'))
                                              )); ?>

<!-- script JS -->

        <?= $this->Html->script('tweet.js'); ?> <!-- ajout d'un tweet, supprimer un tweet, s'abonner à un profil, affichages des notifications correspondantes -->

</body>

</html>
