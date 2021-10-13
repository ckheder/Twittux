<?php
/**
 *  Vue contenant l'affichage d'un tweet et de ses commentaires
 */
?>

  <!--zone de notification -->

<div id="alert-area" class="alert-area"></div>

  <!--fin zone de notification  -->

  <div style="word-wrap: break-word;" class="w3-container w3-card w3-white w3-round w3-margin">

    <?php

        if(isset($no_see)) // variable pour déterminer le message à afficher si je ne peut pas voir un tweet
      {

          if($no_see === 2) // si cette variable vaut 2 (renvoi par le controller) on visite un profil privé auquel on est pas abonné
        {
          ?>

              <div class="w3-panel w3-red">

                <p>

                  Ce tweet est privé, vous devez suivre <?= $user_tweet ;?> pour consulter ce tweet.

                </p>

              </div>

        <?php

              if($authName) // si je suis connecté, affichage d'un bouton d'abonnement
            {

        ?>
              <div class="w3-center">

                <span class="zone_abo">

                  <button class="w3-button w3-blue w3-round"><a class="follow" href="" onclick="return false;" data_action="add" data_username="<?= $user_tweet ?>">Suivre</a></button>

                </span>

              </div>

      <?php

            }

          }

      elseif ($no_see === 1) // si cette variable vaut 1 (renvoi par le controller) je suis bloqué je ne peut pas voir le tweet
    {
  ?>

        <div class="w3-panel w3-red">

          <p>  <?=  $this->Html->image('/img/avatar/'.$user_tweet.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle', 'width'=>60, 'height'=>60)); // avatar?> <?= $user_tweet ;?> vous à bloqué.</p>

        </div>

  <?php

    }

}

    else // information sur le tweet
  {

  ?>

      <br>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$tweet->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <?php

          if($authName AND $authName === $tweet->username) // si non auth, pas de bouton et si auth vaut l'auteur du tweet apparition du bouton
        {

          ?>

            <!--bouton de désactivation des commentaires -->

            <div class="dropdown">

              <button onclick="opencommoption()" class="dropbtn">...</button>

                <div id="commoption" class="dropdown-content">

                  <a class="optioncomm" data_idtweet ="<?= $tweet->id_tweet ;?>" data-actioncomm = "<?= ($tweet->allow_comment == 0) ? 1 : 0 ;?>" href="#" onclick="return false;"> <?= ($tweet->allow_comment == 0) ? 'Désactiver les commentaires' : 'Activer les commentaires' ;?></a>

                </div>

            </div>

        <?php

        }

        ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($tweet->username).'','/'.h($tweet->username).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $tweet->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p><?= $tweet->contenu_tweet ;?></p>

        <hr />

      <!--zone de notification sur l'état de l'envoi d'un commentaire -->

      <div id="alert-area" class="alert-area"></div>


      <!--fin zone de notification sur l'état de l'envoi d'un commentaire -->

      <?php

        if($authName) // si non auth, pas de formulaire d'envoi de commentaire
      {


// ZONE COMMENTAIRE

// formulaire de création de commentaire

        echo $this->Form->create(null, [
                                        'id' =>'form_comm',
                                        'url' => ['controller' => 'Commentaires', 'action' => 'add']

                                      ]);

        echo $this->Form->textarea('commentaire' , ['id'=>'textarea_comm','rows' => '3','required'=> 'required','maxlength' => '255','placeholder' => 'Commenter...',($tweet->allow_comment == 1) ? 'disabled' : '']);?>

                <!--bouton dropdown pour les emojis -->

                <div class="w3-dropdown-click">

                  <a onclick="openemojimenu()" class="btnemoji">

                    <img src="/twittux/img/emoji/grinning.png" width="23" height="23"></a>

                      <div id="menuemojie" class="w3-dropdown-content w3-bar-block w3-border">

                        <?php // parcours du dossier contenant les emojis et affichage dans la div

                          $dir = WWW_ROOT . 'img/emoji';

                          $iterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);

                          foreach(new RecursiveIteratorIterator($iterator) as $file)
                        {
                          $img = $file->getFilename();

                          echo "<img src='/twittux/img/emoji/$img' class='emoji' data_code='$img'>";
                        }

                        ?>

                    </div>

              </div>

          <!--champ caché contenant l'id du tweet -->

          <?= $this->Form->hidden('id_tweet',['value' => $this->request->getParam('id')]); ?>

          <!--champ caché contenant l'auteur du tweet -->

          <?= $this->Form->hidden('user_tweet',['value' => $tweet->username]); ?>

          <!--champ caché contenant l'état des commentaires : autorisé ou non -->

          <?= $this->Form->hidden('allowcomm',['value' => $tweet->allow_comment]); ?>

          <!--div d'affichage de l'etat des commentaires : si désactivés -> affichage d'un message , sinon affichage du bouton d'envoi -->

            <div id ="allow_submit_comm" class="w3-center">

              <?php

                if($tweet->allow_comment == 1)
              {
                ?>

                <div class="w3-container w3-panel w3-border w3-red">

                  <p>

                    <i class="fas fa-info-circle"></i> Les commentaires sont désactivés pour ce tweet.

                  </p>

                </div>

              <?php

              }

              else
            {
              ?>

              <button class="w3-button w3-blue w3-round" type="submit">Commenter</button>

              <?php

            }

              ?>

          </div>

            <?= $this->Form->end();

          }

          else // non auth, affichage d'un message
        {
          ?>

          <div class="w3-panel w3-border w3-light-grey">

              <p>

                <i class="fas fa-info-circle"></i> Vous devez vous connecter ou vous inscrire pour commenter ce tweet.

              </p>

          </div>

        <?php

        }

        ?>

<!--fin formulaire -->

<!--affichage des commentaires -->


  <hr />

  <div class="w3-center">

      <h5>

          <i class="fa fa-comment"></i> <span class="nbcomm"><?= $tweet->nb_commentaire;?></span>  commentaire(s)

      </h5>

  </div>

  <hr />

  <div id="list_comm">

<?php

 foreach ($commentaires as $commentaires) : ?>


    <div style="word-wrap: break-word;" class="itemcomm" id="comm<?= $commentaires->id_comm ?>">

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$commentaires->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <?php

          if($authName) // si non auth, pas de comm
        {
          ?>

                <!--bouton dropdown -->
    <div class="dropdown">

      <button onclick="opencommoption(<?= $commentaires->id_comm ?>)" class="dropbtn">...</button>

        <div id="btncomm<?= $commentaires->id_comm ?>" class="dropdown-content">

          <?php

            if($commentaires->username == $authName OR $tweet->username == $authName) // si je suis l'auteur du commentaire ou l'auteur du tweet
          {

            ?>

          <a class="deletecomm" href="#" onclick="return false;" data_idcomm="<?= $commentaires->id_comm ?>"> Supprimer</a>

          <?php

          if($commentaires->username == $authName) // si je suis l'auteur du commentaire
        {
          ?>

          <a class="updatecomment" href="#" onclick="return false;" data_idcomm="<?= $commentaires->id_comm ?>"> Modifier</a>

        <?php

        }

        }

          if($commentaires->username != $authName) // si je ne suis ni l'auteur du commentaire ni l'auteur du tweet
        {

          ?>

          <a class="blockuser" href="" onclick="return false;" data_username="<?= $commentaires->username ?>">Bloquer <?= $commentaires->username ?></a>

          <a class="signalcomm" href="" onclick="return false;"> Signaler</a>

          <?php

        }

           ?>

      </div>

    </div>

    <?php

  } ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($commentaires->username).'','/'.h($commentaires->username).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $commentaires->created->i18nformat('dd MMMM YYYY - HH:mm');?><?= ($commentaires->created < $commentaires->modified) ? " · modifié" : ''; ?></span>


        <!--corps du commentaire -->

        <p class="commcontent<?= $commentaires->id_comm ?>"><?= $commentaires->commentaire ;?></p>

        <hr />

    </div>

<?php endforeach; ?>

</div>

<!-- pagination -->

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

  <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des commentaires</div>

</div>

<?php

}

 ?>
<br />

</div>
<!-- fin affichage des commentaires -->

<!-- FIN ZONE COMMENTAIRE -->

<script>

  var idtweet = "<?= $tweet->id_tweet;?>"; // identifiant du tweet visité, servira à la connexion à Node Js

</script>
