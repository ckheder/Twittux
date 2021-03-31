<!--

 * navbar.php
 *
 * Barre de navigation en haut
 *
 */ -->

 <div class="w3-top">

 <div class="w3-bar w3-black w3-left-align w3-large">

    <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-left w3-padding-large w3-hover-white" href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>

<!-- lien vers actualités -->

    <a href="/twittux/actualites" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Actualités"><i class="fa fa-globe"></i></a>

<!-- lien vers les abonnements -->

    <a href="/twittux/abonnements/<?= $authName ;?>" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Social"><i class="fas fa-user-friends"></i></a>

<!-- lien vers la messagerie -->

    <a href="/twittux/messagerie" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Messagerie"><i class="fa fa-envelope"></i></a>

<!-- lien vers les notifications -->

    <a href="/twittux/notifications" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Notifications"><i class="fa fa-bell"></i></a>

<!-- lien vers les paramètres -->

    <a href="/twittux/settings" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Paramètres"><i class="fas fa-users-cog"></i></a>

<!-- zone de recherche -->

      <div class="w3-dropdown-hover">

        <input type="text" class="input-search" placeholder="Recherche sur Twittux..." maxlength="50">

<!-- liste contenant les résultats de la recherche d'utilisateurs -->

            <ul id="autocomplete-results" class="w3-ul w3-hoverable"></ul>

      </div>


    <!-- lien de déconnexion -->

    <a href="/twittux/logout" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white w3-right" title="Déconnexion"><i class="fas fa-sign-out-alt"></i></a>

    <!-- lien vers mon profil -->

    <a href="/twittux/<?= $authName ?>" class="w3-hide-small w3-padding-large w3-hover-white w3-right" title="Mon profil">
         <?= $this->Html->image('/img/avatar/'.$authName.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle', 'width'=>'23', 'height'=>'23'));?></a>

    <!-- lien pour tweeter -->

    <a onclick="document.getElementById('modaltweet').style.display='block'" class="w3-bar-item w3-button w3-padding-large w3-right"><i class="fas fa-pen"></i></a>

  </div>

</div>
<!-- Navbar on small screens -->

<div id="smallscreensnav" class="w3-bar-block w3-hide w3-large">

  <a href="/twittux/actualites" class="w3-bar-item w3-button w3-padding-large"><i class="fa fa-globe"></i> Actualités</a>

  <a href="/twittux/abonnements/<?= $authName ;?>" class="w3-bar-item w3-button w3-padding-large"><i class="fas fa-user-friends"></i> Social</a>

  <a href="/twittux/messagerie" class="w3-bar-item w3-button w3-padding-large"><i class="fa fa-envelope"></i> Messagerie</a>

  <a href="/twittux/notifications" class="w3-bar-item w3-button w3-padding-large"><i class="fa fa-bell"></i> Notifications</a>

  <a href="/twittux/settings" class="w3-bar-item w3-button w3-padding-large"><i class="fas fa-users-cog"></i> Paramètres</a>

  <a href="/twittux/<?= $authName ?>" class="w3-bar-item w3-button w3-padding-large"> <i><?= $this->Html->image('/img/avatar/'.$authName.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle', 'width'=>'21', 'height'=>'21')); ?></i> Mon profil</a>

  <a href="/twittux/logout" class="w3-bar-item w3-button w3-padding-large"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>

</div>

 <?= $this->Html->script('navbar.js'); ?> <!-- gestion des actions de la navbar : autocomplete, responsive -->
