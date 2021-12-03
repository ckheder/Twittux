<!--

 * display.php
 *
 * Affichage d'un coeur rouge si j'aime déjà ce tweet et d'un coeur vide si non
 *
 */ -->
 
 <span class="iconeaime<?= $idtweet;?>">

    <?= ($likestatut == 1) ? "<i class=\"fas fa-heart w3-margin-bottom\" data_id_tweet=\"$idtweet\" data_action=\"like\" style=\"color: red; cursor: pointer\"></i>" : "<i class=\"far fa-heart w3-margin-bottom\" style=\"cursor: pointer\" data_id_tweet=\"$idtweet\" data_action=\"like\"></i>" ;?>

</span>