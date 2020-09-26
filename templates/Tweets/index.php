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
       <?php foreach ($tweets as $tweet): ?>
      
      <div style="word-wrap: break-word;" class="w3-container w3-card w3-white w3-round w3-margin" id="tweet<?= $tweet->id_tweet ;?>"><br>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$tweet->user_tweet.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

                        <!--bouton -->
    <div class="dropdown">
  <button onclick="openmenutweet(<?= $tweet->id_tweet ?>)" class="dropbtn">...</button>
  <div id="btntweet<?= $tweet->id_tweet ?>" class="dropdown-content">
    <a class="deletetweet" href="#" onclick="return false;" data_idtweet="<?= $tweet->id_tweet ?>"> Supprimer</a>
  </div>
</div>

        <!--nom d'utilisateur -->

        <h4><?= $tweet->user_tweet ;?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $tweet->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p><?= $tweet->contenu_tweet ;?></p>

        <!--boutons like et commentaire -->

        <button type="button" class="w3-button w3-blue-grey w3-margin-bottom"><i class="fa fa-thumbs-up"></i> <?= $tweet->nb_like ;?>  Like</button> 
        <a href="./statut/<?= $tweet->id_tweet ;?>" class="w3-btn w3-grey w3-margin-bottom"><i class="fa fa-comment"></i> <?= $tweet->nb_commentaire;?>  commentaire(s)</a> 
      </div>
      
 <?php endforeach; ?>
 </div>     

    </div>
    
    <!-- Right Column -->
    <div class="w3-col m2">
       
<!--zone de suggestion ou de hashtag -->

<?php

  if($this->request->getParam('username') != $authName)
{

?>
<span id="zone_abo">


<?= $this->cell('Abonnements::testabo', [$authName,$this->request->getParam('username')]); ?>

</span>

              <span id="zone_blocage">
<button class="w3-button w3-red w3-round w3-right"><a class="followrequest" href="" onclick="return false;" data_action="refuse" data_username="<?= $this->request->getParam('username') ?>"><i class="fas fa-user-lock"></i> Bloquer </a></button>
</span>

<?php 

}

?>


  
    <!-- End Right Column -->
    </div>
        
       