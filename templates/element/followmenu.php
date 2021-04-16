<!--

 * followmenu.php
 *
 * Menu de navigation dans les pages d'abonnements
 *
 */ -->
<?php

	if ($this->request->getParam('username')) // si la variable d'URL username existe, on l' utilise dans les URL du menu
{
	$username = $this->request->getParam('username');
}
	else
{
	$username = $authName; //  si la variable d'URL n'existe pas , on utilise la valeur du Authname dans les URL du menu
}

?>

  <div class="w3-third">

 		<div class="w3-bar-block w3-white">
 			<!-- lien vers la pages des abonnements d'une personne -->
  			<a href="/twittux/abonnements/<?= $username ;?>" class="w3-bar-item w3-button"><i class="fas fa-user-circle"></i> Abonnements</a>
  			<!-- lien vers la pages des abonnés d'une personne -->
  			<a href="/twittux/abonnes/<?= $username;?>" class="w3-bar-item w3-button"><i class="far fa-user-circle"></i> Abonnés</a>
  			<!-- lien vers la pages des demande d'abonnements -->
				<a href="/twittux/abonnement/demande" class="w3-bar-item w3-button"><i class="fas fa-user-friends"></i> Demande</a>
				<!-- lien vers la pages des utilisateurs bloqués -->
				<a href="/twittux/userblock" class="w3-bar-item w3-button"><i class="fas fa-user-friends"></i> Utilisateurs bloqués</a>
		</div>

  </div>
