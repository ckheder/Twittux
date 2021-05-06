<!--

 * infosabo.php
 *
 * Affichage du nombre d'abonnements et d'abonnés du profil courant et liens pour visiter les pages concernées
 *
 */ -->

 <!-- nombre d'abonnements -->

<p>

  <i class="fas fa-user-circle fa-fw w3-margin-right w3-text-theme"></i>

    <a href="/twittux/social/<?= $username ?>" class="w3-text-blue"> <?= $nbabonnements ?> abonnement(s)</a>

</p>

<!-- nombre d'abonnés -->

<p>

  <i class="far fa-user-circle fa-fw w3-margin-right w3-text-theme"></i>

    <a href="#" id="usersfollowers" data_username ="<?= $username ?>" class="w3-text-blue"> <?= $nbabonnes ?> abonné(s)</a>

</p>
