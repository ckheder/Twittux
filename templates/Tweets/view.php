<?php
/**
 *  Vue contenant l'affichage d'un tweet et de ses commentaires
 */
?>
<div class="w3-col m9">

  <!--zone de notification -->

                    <div id="alert-area" class="alert-area"></div>

  <!--fin zone de notification  -->

    <?php if(isset($no_see))
  {

      if($no_see === 2) // si cette variable existe et vaut 2 (renvoi par le controller) on visite un profil privé auquel on est pas abonné
    {
      ?>
      <div class="w3-container">

        <div class="w3-panel w3-red">

          <p>

            Ce tweet est privé, vous devez suivre <?= $user_tweet ;?> pour consulter ce tweet.

          </p>

        </div>

        <?php if($authName) // si je suis connecté, affichage d'un bouton d'abonnement
    {

      ?>

    <div class="w3-center">

      <span class="zone_abo">

        <button class="w3-button w3-blue w3-round"><a class="follow" href="" onclick="return false;" data_action="add" data_username="<?= $user_tweet ?>">Suivre</a></button>

      </span>

    </div>

    <?php
  }

  ?>

  </div>

<?php

}

      elseif ($no_see === 1) // si cette variable existe et vaut 1 (renvoi par le controller) je suis bloqué je ne peut pas voir le tweet
    {
  ?>
      <div class="w3-container">

        <div class="w3-panel w3-red">

          <p>  <?=  $this->Html->image('/img/avatar/'.$user_tweet.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle', 'width'=>60, 'height'=>60)); // avatar?> <?= $user_tweet ;?> vous à bloqué.</p>

        </div>

      </div>

  <?php

    }

}

    else // information sur le tweet
  {

  ?>

      <div style="word-wrap: break-word;" class="w3-container w3-card w3-white w3-round w3-margin"><br>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$tweet->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <?php

          if($authName) // si non auth, pas de bouton
        {

          ?>

                        <!--bouton de désactivation des commentaires -->
                        
    <div class="dropdown">

      <button onclick="opencommoption()" class="dropbtn">...</button>

        <div id="commoption" class="dropdown-content">

          <a class="optioncomm" href="#" onclick="return false;"> Désactiver les commentaires</a>

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

      <!--zone de notification sur l'état de l'envoi d'un commentaire -->

      <div id="alert-area" class="alert-area"></div>

      <!--fin zone de notification sur l'état de l'envoi d'un commentaire -->

      <?php if($authName) // si non auth, pas de comm
      {



// ZONE COMMENTAIRE /

// formulaire de création de commentaire

echo $this->Form->create(null, [
                                'id' =>'form_comm',
                                'url' => ['controller' => 'Commentaires', 'action' => 'add']

                                ]);
//<!--textarea
                  echo $this->Form->textarea('commentaire' , ['id'=>'textarea_comm','rows' => '3','required'=> 'required','maxlength' => '255']);?>

                <!--bouton dropdown -->

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

          <?= $this->Form->hidden('user_tweet',['value' => $tweet->username]); ?>

            <div class="w3-center">

              <br />

<!--bouton d'envoi -->

                    <?= $this->Form->button('Commenter',['class' =>'w3-button w3-blue w3-round']) ?>
            </div>

            <?= $this->Form->end(); } else {
              echo '<div class="w3-panel w3-border w3-light-grey"><p>

            <i class="fas fa-info-circle"></i> Vous devez vous connecter ou vous inscrire pour commenter ce tweet.  </p></div>';
            }?>

<!--fin formulaire -->

    <br />

</div>

<!--affichage des commentaires -->

<div class="w3-center">



            <h5>

              <i class="fa fa-comment"></i> <span class="nbcomm"><?= $tweet->nb_commentaire;?></span>  commentaire(s)

            </h5>
</div>

<br />

<div id="list_comm">

<?php foreach ($commentaires as $commentaires) : ?>

    <div style="word-wrap: break-word;" class="w3-container w3-card w3-round w3-margin w3-white" id="comm<?= $commentaires->id_comm ?>"><br>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$commentaires->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <?php if($authName) // si non auth, pas de comm
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

          <a class="deletecomm" href="" onclick="return false;" data_idcomm="<?= $commentaires->id_comm ?>"> Supprimer</a>

          <a class="blockuser" href="" onclick="return false;" data_username="<?= $commentaires->username ?>">Bloquer <?= $commentaires->username ?></a>

          <?php

        }

           ?>

          <a class="deletecomm" href="" onclick="return false;"> Signaler</a>

      </div>

    </div>

    <?php

  } ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($commentaires->username).'','/'.h($commentaires->username).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $commentaires->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>


        <hr class="w3-clear">

        <!--corps du commentaire -->

        <p><?= $commentaires->commentaire ;?></p>

    </div>



<?php endforeach; ?>

</div>

<br />



<?php

}

 ?>

<!-- fin affichage des commentaires -->

<!-- FIN ZONE COMMENTAIRE -->

 </div>
