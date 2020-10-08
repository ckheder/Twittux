<?php
/**
 *  Vue contenant la page d'accueil après login
 */
?>
 

    <div class="w3-col m7">
      <div class="w3-row-padding">
        <div class="w3-col m12">
          <div class="w3-card w3-round w3-white">
            <div class="w3-container w3-padding">
              <h6 class="w3-opacity">Partager quelque chose....</h6>
<!-- formulaire de création d'un tweet -->
                <?= $this->Form->create(null, [
                                                'id' =>'form_tweet',
                                                'url' => ['controller' => 'Tweets', 'action' => 'add']
    
                                                ]);?>
<!--textarea -->
                  <?=$this->Form->textarea('contenu_tweet' , ['id'=>'textarea_tweet','rows' => '3','required'=> 'required','maxlength' => '255']);?>

                    <div class="w3-dropdown-click">
    <a onclick="openemojimenu()" class="btnemoji"><img src="/twittux/img/emoji/grinning.png" width="23" height="23"></a>
    <div id="menuemoji" class="w3-dropdown-content w3-bar-block w3-border">
<?php // parcours du dossier contenant les emojis et affichage dans la div

  $dir = WWW_ROOT . 'img/emoji'; // chemin du dossier
  $iterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
  foreach(new RecursiveIteratorIterator($iterator) as $file)
{
  $img = $file->getFilename();
  echo "<img src='/twittux/img/emoji/$img' class='emoji' data_code='$img'>";  
}
?>
    </div>
  </div>
                  <div id="charactersRemaining" class="w3-opacity">255 caractère(s) restant(s)</div>
                           
            <div class="w3-center">
              <br />
<!--bouton d'envoi -->
                    <?= $this->Form->button('Publier',['class' =>'w3-button w3-blue w3-round']) ?>
            </div>
            <?= $this->Form->end() ?> 
<!--fin formulaire -->

<!--zone de notification sur l'état de l'envoi d'un tweet -->
<div id="alert-area" class="alert-area"></div>
<!--fin zone de notification sur l'état de l'envoi d'un tweet -->
            </div>
          </div>
        </div>
      </div>
<!--affichage des tweets de l'utilisateur et des tweets partagés-->
<div id="list_tweet">

       <?php foreach ($tweets as $tweet):  ?>
  
      <div style="word-wrap: break-word;" class="w3-container w3-card w3-white w3-round w3-margin" id="tweet<?= $tweet->id_tweet ;?>">

        <!-- test si c'est un tweet partagé -->

        <?php

        if($tweet->user_tweet != $this->request->getParam('username'))
        {
          $share = 1; // partage
        }
        else
        {
          $share = 0; // non partage
        }

        ?>

        <!-- bouton dropdown pour supprimer un tweet -->

          <div class="dropdown">

            <br />

              <button onclick="openmenutweet(<?= $tweet->id_tweet ?>)" class="dropbtn">...</button>

                <div id="btntweet<?= $tweet->id_tweet ?>" class="dropdown-content">

                  <a class="deletetweet" href="#" onclick="return false;" data_type="<?= $share ?>" data_idtweet="<?= $tweet->id_tweet ?>"> Supprimer</a>

                </div>

          </div>

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

        <?=  $this->Html->image('/img/avatar/'.$tweet->user_tweet.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($tweet->user_tweet).'','/'.h($tweet->user_tweet).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $tweet->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p><?= $tweet->contenu_tweet ;?></p>

        <hr class="w3-clear">

        <!-- zone d'affichage du nombre de like, commentaire et de partage -->

        <span class="w3-opacity"> 

        <!-- affichage du nombre de like --> 

          <a onclick="openmodallike(<?= $tweet->id_tweet ?>)" style="cursor: pointer;"><span class="nb_like_<?= $tweet->id_tweet ?>"><?= $tweet->nb_like ;?></span> J'aime</a>

        <!-- affichage du nombre de commentaire --> 

          - <?= $tweet->nb_commentaire;?> Commentaire(s)

        <!-- affichage du nombre de partage --> 

          - Partagé <span class="nb_share_<?= $tweet->id_tweet ?>"><?= $tweet->nb_partage ;?></span> fois</span>

        <hr>

        <!--boutons like,commentaire et partage -->

        <p>

        <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="like" data_id_tweet="<?= $tweet->id_tweet ?>"><i class="fa fa-thumbs-up"></i> J'aime</a> 
        &nbsp;
        <a href="./statut/<?= $tweet->id_tweet ;?>" class="w3-margin-bottom"><i class="fa fa-comment"></i> Commenter</a>

        <?php 

              if($this->request->getParam('username') != $authName) // si je ne suis pas sur mon profil
            {
                if($tweet->user_tweet != $authName) // si le post partagé n'est pas un post à moi -> affichage du bouton de partage
              {
              ?>
                &nbsp;
                  <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="share" data_id_tweet="<?= $tweet->id_tweet ?>"><i class="fas fa-retweet"></i> Partager</a> 
        <?php
              }
            }
      ?>
      </p>
      </div>
      
 <?php

endforeach; 

?>
 </div>     

    </div>
    
    <!-- Right Column -->
    <div class="w3-col m2">
       
<!--zone de suggestion ou de hashtag -->

<?php

  if($this->request->getParam('username') != $authName) // si je suis pas sur mon profil
{

?>
<span id="zone_abo">

<!-- test de l'abonnement au profil visité -->

  <?= $this->cell('Abonnements::testabo', [$authName,$this->request->getParam('username')]); ?>

</span>

<!-- affichage d'un bouton de blocage -->

<span id="zone_blocage">

  <button class="w3-button w3-red w3-round w3-right"><a class="followrequest" href="" onclick="return false;" data_action="refuse" data_username="<?= $this->request->getParam('username') ?>"><i class="fas fa-user-lock"></i> Bloquer </a></button>

</span>

<?php 

}

?>
    <!-- End Right Column -->
    </div>
        
       