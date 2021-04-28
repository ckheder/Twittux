<!--

 * offlinenavbar.php
 *
 * Barre de navigation pour les utilisateurs non connectés
 *
 */ -->

<!-- navbar -->
<div class="w3-top">

  <div class="w3-bar w3-black w3-large">

<!-- lien affichage navbar responsive -->

      <!-- lien affichage page d'accueuil -->

      <a href="/twittux/" title="Accueil" class="w3-bar-item w3-button w3-padding-large w3-hover-white"><i class="fas fa-home"></i></a>

      <!-- lien affichage Tendances -->

      <a href="/twittux/trending" title="Tendances" class="w3-bar-item w3-button w3-padding-large w3-hover-white"><i class="fas fa-hashtag"></i></a>

      <!-- zone de recherche -->

            <div class="w3-dropdown-hover">

              <input type="text" class="input-search" placeholder="Recherche sur Twittux..." maxlength="50">

      <!-- liste contenant les résultats de la recherche d'utilisateurs -->

                  <ul id="autocomplete-results" class="w3-ul w3-hoverable"></ul>

            </div>

<!-- lien d'ouverture de la modale login -->
        <a onclick="document.getElementById('modallogin').style.display='block'" class="w3-bar-item w3-button w3-padding-large w3-hover-white w3-right"><i class="fas fa-sign-in-alt"></i></a>

  </div>

</div>

<?= $this->Html->script('navbar.js'); ?> <!-- gestion des actions de la navbar : autocomplete, responsive -->
