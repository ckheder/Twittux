<!--

 * offlinenavbar.php
 *
 * Barre de navigation pour les utilisateurs non connectés
 *
 */ -->

<!-- navbar -->
     <div class="w3-bar w3-black  w3-large">

<!-- lien affichage navbar responsive -->

      <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-left w3-hover-white w3-large" href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>

        <!-- zone de recherche -->

              <div class="w3-dropdown-hover">

                <input type="text" class="w3-bar-item w3-input input-search"  placeholder="Recherche sur Twittux...">

                  <div class="w3-dropdown-content">

        <!-- liste contenant les résultats de la recherche d'utilisateurs -->

                    <ul id="autocomplete-results" class="w3-ul w3-hoverable"></ul>

                  </div>

              </div>
<!-- lien d'ouverture de la modale login -->
        <a onclick="document.getElementById('modallogin').style.display='block'" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white w3-right"><i class="fas fa-sign-in-alt"></i></a>
      </div>
<!-- Navbar on small screens -->
      <div id="smallscreensnav" class="w3-bar-block w3-hide w3-large">

        <div class="w3-dropdown-hover">

          <form class="testrest">

            <input type="text" class="w3-bar-item w3-input input-search-resp" placeholder="Recherche sur Twittux...">

          </form>

        </div>
<!-- lien d'ouverture de la modale login -->
        <a onclick="document.getElementById('modallogin').style.display='block'" class="w3-bar-item w3-button w3-padding-large"><i class="fas fa-sign-in-alt"></i> Connexion</a>
      </div>
<?= $this->Html->script('navbar.js'); ?> <!-- gestion des actions de la navbar : autocomplete, responsive -->
