<?php
/**
 *  Vue contenant la page d'accueil après login : liste des tweets
 */

?>

<!--zone de notification sur l'état de l'envoi d'un tweet -->
<div id="alert-area" class="alert-area"></div>
<!--fin zone de notification sur l'état de l'envoi d'un tweet -->

<!--affichage des tweets de l'utilisateur et des tweets partagés-->

  <?php

    if(isset($tweets) AND count($tweets) == 0) // aucun tweet à afficher
  {
    ?>
      <div class="w3-container">

        <div class="w3-panel w3-blue">

          <p>Aucun tweet à afficher.</p>

        </div>

      </div>

  <?php

  }

    if($no_see === 1) // si cette variable existe (renvoi par le controller) et vaut 1 on est bloqué
  {
      ?>
        <div class="w3-container">

          <div class="w3-panel w3-red">

            <p>

              <?=  $this->Html->image('/img/avatar/'.$this->request->getParam('username').'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle', 'width'=>60, 'height'=>60)); // avatar?> <?= $this->request->getParam('username') ;?> vous à bloqué.

            </p>

          </div>

        </div>

<?php

    }

    elseif($no_see === 2) // si cette variable existe et vaut 2 (renvoi par le controller) on visite un profil privé auquel on est pas abonné
  {
    ?>

    <div class="w3-container">

      <div class="w3-panel w3-red">

      <p>

        Ce profil est privé, vous devez suivre <?= $this->request->getParam('username') ;?> pour consulter ses tweets.

      </p>

      </div>

    </div>

<?php

  }

  else // profil public ou privé mais abonné -> affichage des tweets
  {
    ?>

  <div class="w3-container displaymessagenewtweet"></div> <!-- div qui servira à afficher un message 'nouveaux tweet de...' pour les personnes autres que le profil courant en cas de nouveau tweet du profil courant -->

    <div class="usertweets">

        <?php foreach ($tweets as $tweet):  ?>

      <div style="word-wrap: break-word;" class="w3-container w3-card w3-white w3-round w3-margin item" id="tweet<?= $tweet->id_tweet ;?>">

        <!-- test si c'est un tweet partagé -->

        <?php

          if($tweet->username != $this->request->getParam('username'))
        {
          $share = 1; // partage
        }
          else
        {
          $share = 0; // non partage
        }

        ?>

        <?php if($authName) // si non auth, pas de comm
        {
          ?>

        <!-- bouton dropdown pour supprimer un tweet -->

          <div class="dropdown">

            <br />

              <button onclick="openmenutweet(<?= $tweet->id_tweet ?>)" class="dropbtn">...</button>

                <div id="btntweet<?= $tweet->id_tweet ?>" class="dropdown-content">

                  <?php

                    if($tweet->username == $authName) // si je suis l'auteur du tweet , je peux le supprimer
                  {
                    ?>

                    <a class="deletetweet" href="#" onclick="return false;" data_type="<?= $share ?>" data_idtweet="<?= $tweet->id_tweet ?>"> Supprimer</a>

                    <?php
                  }
                      elseif ($share == 1) // je ne suis pas l'auteur du tweet mais je l'ais partager donc je peux le supprimer

                  {

                    ?>

                    <a class="deletetweet" href="#" onclick="return false;" data_type="<?= $share ?>" data_idtweet="<?= $tweet->id_tweet ?>"> Supprimer</a>

                    <?php

                  }

                    else // je ne suis ni l'auteur et je n'ais pas partagé ce post (autre profil) je peux le signaler
                  {
                    ?>

                    <a href="#">Signaler ce post </a>

                    <?php
                  }

                  ?>

                </div>

          </div>

        <?php } ?>

<!-- affichage différend selon partage -->

        <?php

          if($share == 1) // affichage d'un message comme quoi le profil courant à partagé le post
        {

          echo '<p><span class="w3-opacity"><i class="fas fa-retweet"></i> Partagé par '.$this->request->getParam('username').'</span></p>';
        }
          else
        {
          echo '<br />';
        }

        ?>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$tweet->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($tweet->username).'','/'.h($tweet->username).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $tweet->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p><?= $tweet->contenu_tweet ;?></p>

        <hr class="w3-clear">

        <!-- zone d'affichage du nombre de like, commentaire et de partage -->

        <span class="w3-opacity">

        <!-- affichage du nombre de like -->

          <a <?= ($tweet->nb_like > 0) ? "onclick=\"openmodallike($tweet->id_tweet)\" style=\"cursor: pointer;\"" : ''; ?> class="modallike_<?= $tweet->id_tweet ?>"><span class="nb_like_<?= $tweet->id_tweet ?>"><?= $tweet->nb_like ;?></span> J'aime</a>

        <!-- affichage du nombre de commentaire -->

          - <?= $tweet->nb_commentaire;?> Commentaire(s)

        <!-- affichage du nombre de partage -->

          - Partagé <span class="nb_share_<?= $tweet->id_tweet ?>"><?= $tweet->nb_partage ;?></span> fois</span>

        <hr>

        <?php if($authName) // si non auth, pas de commentaire, like ou de partage
        {
          ?>

        <!--boutons like et partage -->

        <p>

        <!-- cell d'affichage si j'aime ou non le tweet -->

        <?= $this->cell('Like', [$authName, $tweet->id_tweet]); ?>

        <!-- lien vers les commentaires -->

        <a href="./statut/<?= $tweet->id_tweet ;?>" class="w3-margin-right"><i title="Commenter ce tweet" class="fas fa-comment"></i></a>

        <?php

              if($this->request->getParam('username') != $authName) // si je ne suis pas sur mon profil
            {
                if($tweet->username != $authName) // si le post partagé n'est pas un post à moi -> affichage du bouton de partage
              {
              ?>
                  <?= $this->cell('Partage', [$authName, $tweet->username, $tweet->id_tweet]); ?>
              <?php

              }

            }

      ?>
      </p>

    <?php

      }

  ?>
      </div>

 <?php

endforeach; ?>

</div>

<div hidden id="spinnerajaxscroll"></div>

<?php

if ($this->Paginator->hasNext())
{

 ?>

 <div class="pagination">

  <?= $this->Paginator->options([

                                  'url' => '/'.$this->request->getParam('username').''

                                ]);?>

  <?= $this->Paginator->next('Next page'); ?>

</div>

<?php

}

?>
<div class="w3-center">

<div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des tweets de <?= $this->request->getParam('username') ?></div>

</div>

<?php

}

?>
