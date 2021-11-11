<!--

 * testabo.php
 *
 * Affichagde des boutons relatifs à un abonnement: profil, moteur de recherche et toutes les pages abonnements : abonnements, abonnés
 *
 */ -->
 
<?php

	if($infoabo == 'noabo') // pas d'abonnement existant, création d'un bouton permettant de suivre la personne
{
	?>
		<button class="w3-button w3-blue w3-round"><a class="follow" href="" onclick="return false;" data_action="add" data_username="<?= $username ?>"><i class="fas fa-user-plus"></i> Suivre</a></button>
	<?php
}
	elseif ($infoabo == 'demande') // demande envoyée ou en attente, création d'un bouton permettant d'annuler la demande
{
	?>
	<button class="w3-button w3-orange w3-round"><a class="follow" href="" onclick="return false;" data_action="cancel" data_username="<?= $username ?>"><i class="fas fa-user-times"></i> Annuler</a></button>

	<?php
}
	elseif ($infoabo == 'abonnement') // abonnement existant, création d'un bouton permettant de le supprimer
{
	?>
	<button class="w3-button w3-red w3-round"><a class="follow" href="" onclick="return false;" data_action="delete" data_username="<?= $username ?>"><i class="fas fa-user-minus"></i> Ne plus suivre</a></button>
	<?php
}
?>
