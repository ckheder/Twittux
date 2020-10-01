<!--

 * actualités.php
 *
 * Mise en page actualité
 *
 */ -->
    <div class="w3-col m7">
      <div class="w3-row-padding">
          <div class="w3-card w3-round w3-white">
            <div class="w3-container w3-padding">
              <div id="list_actu_online">
<!--zone de notification -->
                  <div id="alert-area" class="alert-area"></div>
<!--fin zone de notification  -->
  <?php

          if(isset($no_actu)) // rien à afficher
        {

         echo '<div class="w3-container w3-blue">Vous ne suivez actuellement personne : vous trouverez quelques suggestions de personne à suivre à droite ou vous pouvez utiliser le moteur de recherche pour trouver une personne spécifique. Vous suivez aussi peut être quelqu\'un qui n\'a pas encore tweeté.</div>';

        }
          else
        {

          foreach ($actu as $actu): ?>

<div style="word-wrap: break-word;" class="w3-container w3-card w3-white w3-round" id="tweet<?= $actu->id_tweet ;?>">
              <br>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$actu->user_tweet.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

                        <!--menu déroulant : suppression d'un abonnement / signaler un post -->
    <div class="dropdown">
      <button onclick="openmenutweet(<?= $actu->id_tweet ?>)" class="dropbtn">...</button>
        <div id="btntweet<?= $actu->id_tweet ?>" class="dropdown-content">
          <a class="unfollow" href="" onclick="return false;" data_action="delete" data_username="<?= $actu->user_tweet ?>">Ne plus suivre</a>
    <a href="#">Signaler ce post </a>  
        </div>
    </div>

        <!--nom d'utilisateur -->

        <h4><?= $actu->user_tweet ;?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $actu->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p><?= $actu->contenu_tweet ;?></p>

        <hr class="w3-clear">

        <span class="w3-opacity"> <a onclick="openmodallike(<?= $actu->id_tweet ?>)" style="cursor: pointer;"><span class="nb_like_<?= $actu->id_tweet ?>"><?= $actu->nb_like ;?></span> J'aime</a>- <?= $actu->nb_commentaire;?> Commentaire(s)</span>

        <hr class="w3-clear">

        <!--boutons like et commentaire -->

        <button type="button" class="w3-button w3-blue-grey w3-margin-bottom" onclick="return false;" data_action="like" data_id_tweet="<?= $actu->id_tweet ?>"><i class="fa fa-thumbs-up"></i> J'aime</button> 
        <a href="./statut/<?= $actu->id_tweet ;?>" class="w3-btn w3-grey w3-margin-bottom"><i class="fa fa-comment"></i> Commenter</a> 

</div>

            <?php endforeach; ?>

            <!--lien pagination -->

            <div id="pagination">

            <?= $this->Paginator->next('Next page'); ?>

            </div>

 <?php

        } ?>
 </div>
</div>
</div>
</div>
</div>