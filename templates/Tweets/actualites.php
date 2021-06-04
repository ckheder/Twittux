<!--

 * actualités.php
 *
 * Mise en page actualité
 *
 */ -->


<div class="w3-col m6">

  <div class="onlinenews">


<!--zone de notification -->
                  <div id="alert-area" class="alert-area"></div>
<!--fin zone de notification  -->
  <?php

          if(isset($no_actu)) // rien à afficher
        {

         echo '<div class="w3-panel w3-border w3-light-grey"><p>

              <i class="fas fa-info-circle"></i> Vous ne suivez actuellement personne : vous trouverez quelques suggestions de personne à suivre à droite ou vous pouvez utiliser le moteur de recherche pour trouver une personne spécifique. Vous suivez aussi peut être quelqu\'un qui n\'a pas encore tweeté.</p></div></div>';

        }
          else
        {

 foreach ($actu as $actu): ?>

  <div style="word-wrap: break-word;" class="w3-container w3-card w3-white w3-round w3-margin itemnews">

<!-- bouton de dropdown  : signaler un post, ne plus suivre -->

    <div class="dropdown">

    <br />

      <button onclick="openmenutweet(<?= $actu->id_tweet ?>)" class="dropbtn">...</button>

        <div id="btntweet<?= $actu->id_tweet ?>" class="dropdown-content">

          <a class="follow" href="" onclick="return false;" data_action="delete" data_username="<?= $actu->username ?>">Ne plus suivre <?= $actu->username ?></a>

          <a href="#">Signaler ce post </a>

        </div>

      </div>

  <?php

          // si c'est un tweet partagé

          if(!is_null($actu->Partage['username']) AND $actu->Partage['username'] != $actu->username)
        {

            if($actu->Partage['username'] == $authName) // si c'est moi qui ais partagé
          {
            echo '<p><span class="w3-opacity"><i class="fas fa-retweet"></i> Vous avez partagé ce post.</span></p>';
          }
            else // ce n'est pas moi qui ais partagé
          {
            echo '<p><span class="w3-opacity"><i class="fas fa-retweet"></i> '.$actu->Partage['username'].' à partagé ce post.</span></p>';
          }

        }
          else
        {
          echo '<br />';
        }

        ?>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$actu->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($actu->username).'','/'.h($actu->username).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $actu->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p><?= $actu->contenu_tweet ;?></p>

        <hr class="w3-clear">

        <!-- zone d'affichage du nombre de like, commentaire et de partage -->

        <span class="w3-opacity">

        <!-- affichage du nombre de like -->

        <a <?= ($actu->nb_like > 0) ? "onclick=\"openmodallike($actu->id_tweet)\" style=\"cursor: pointer;\"" : ''; ?> class="modallike_<?= $actu->id_tweet ?>" ><span class="nb_like_<?= $actu->id_tweet ?>"><?= $actu->nb_like ;?></span> J'aime</a>

        <!-- affichage du nombre de commentaire -->

          - <?= $actu->nb_commentaire;?> Commentaire(s)

        <!-- affichage du nombre de partage -->

          - Partagé <span class="nb_share_<?= $actu->id_tweet ?>"><?= $actu->nb_partage ;?></span> fois</span>

        <hr>

        <!--boutons like,commentaire et partage -->

         <p>

        <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="like" data_id_tweet="<?= $actu->id_tweet ?>"><i class="fa fa-thumbs-up"></i> J'aime</a>
        &nbsp;
        <a href="./statut/<?= $actu->id_tweet ;?>" class="w3-margin-bottom"><i class="fa fa-comment"></i> Commenter</a>

        <?php

        // si je ne suis pas l'auteut du tweet et que ce n'est pas un tweet partagé, affichage du bouton de partage

          if($actu->username != $authName AND $actu->Partage['username'] != $authName)
        {

          ?>

        &nbsp;

        <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="share" data_auttweet = "<?= $actu->username ?>" data_id_tweet="<?= $actu->id_tweet ?>"><i class="fas fa-retweet"></i> Partager</a>

        <?php

        }

      ?>

      </p>

</div>

            <?php endforeach; ?>

            </div>

            <div hidden id="spinnerajaxscroll"></div>

            <!--lien pagination -->

              <?php

              if ($this->Paginator->hasNext())
              {
               ?>

               <div class="pagination">

              <?= $this->Paginator->next('Next page'); ?>

              </div>

              <?php

            }

?>
<div class="w3-center">

  <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin de l'actualités</div>

</div>

 <?php

        } ?>
</div>
