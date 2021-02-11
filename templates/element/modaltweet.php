<!--

 * modaltweet.php
 *
 * Fenêtre modal affichant la textarea de tweet
 *
 */ -->

<div id="modaltweet" class="w3-modal">

    <div class="w3-modal-content w3-card-4 w3-animate-top" style="max-width:600px">

      <header class="w3-container w3-black w3-center">

        <span onclick="document.getElementById('modaltweet').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Fermer">&times;</span>

          <h3>Tweeter</h3>

      </header>

      <div class="w3-row-padding">

        <div class="w3-col m12">

            <div class="w3-container w3-padding">

              <h6 class="w3-opacity">Partager quelque chose....</h6>

<!-- formulaire de création d'un tweet -->

                <?= $this->Form->create(null, [
                                                'id' =>'form_tweet',
                                                'url' => ['controller' => 'Tweets', 'action' => 'add']

                                                ]);?>
<!--textarea -->
                  <?= $this->Form->textarea('contenu_tweet' , ['id'=>'textarea_tweet','rows' => '3','required'=> 'required','maxlength' => '255']);?>

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

            </div>

        </div>

      </div>

    </div>

  </div>

  <?= $this->Html->script('addtweet.js'); ?> <!-- ajouter un tweet -->
