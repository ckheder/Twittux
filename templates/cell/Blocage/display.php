<!--

 * display.php
 *
 * Affichagde des boutons relatifs à un blocage: profil, moteur de recherche et abonnés
 *
 */ -->

<?php

	if($infoblock == 'noblock') // pas de blocage existant, création d'un bouton permettant de bloquer la personne
{
	?>

		<button class="w3-button w3-black w3-round"><a class="blockuser" href="" onclick="return false;" data_username="<?= $username ?>"><i class="fas fa-lock"></i> Bloquer </a></button>

	<?php

}
	elseif ($infoblock == 'block') // blocage existant, création d'un bouton permettant de débloquer la personne
{

	?>
	
	<button class="w3-button w3-black w3-round"><a class="deblockuser" href="" onclick="return false;" data_username="<?= $username ?>"><i class="fas fa-unlock"></i> Débloquer </a></button>

	<?php
}

?>
