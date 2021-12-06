<!--

 * display.php
 *
 * Affichage d'un symbole partage bleu si j'ai déjà partagé ce tweet
 *
 */ -->
 
 <span class="iconeshare<?= $idtweet;?>">

    <?= ($partagestatut == 1) ? "<i title=\"Vous avez déjà partagé ce tweet \" class=\"fas fa-share-square\" style=\"color: #4F50F8;\"></i>" : "<i title=\"Partager ce tweet\" class=\"far fa-share-square w3-margin-bottom\" style=\"cursor: pointer\" data_id_tweet=\"$idtweet\" data_auttweet=\"$auttweet\" data_action=\"share\"></i>" ;?>

</span>