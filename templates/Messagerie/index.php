<!--

 * index.php
 *
 * Page d'accueuil de la messagerie
 *
 */ -->

<div class="w3-col m7">

  <div class="w3-row-padding">

        <div class="w3-padding">

<!--zone de notification -->

              <div id="alert-area" class="alert-area"></div>

<!--fin zone de notification  -->

<div hidden class="w3-right" id="btnoptionconv"> <!-- bouton d'option d'une conversation (masquée par défaut , affiché au chargement d'une conversation) -->

  <button onclick="openconvoption()" class="btnconv"><i class="fas fa-cog"></i></button>

    <div id="convoption" class="w3-dropdown-content w3-bar-block w3-border">

      <a href="#" class="w3-bar-item w3-button" onclick="return false;"> Signaler cette conversation</a>

      <a href="#" class="w3-bar-item w3-button editconv" data_visible="non" onclick="return false;"> Désactiver cette conversation</a>

      <a onclick="openmodalinvitconv()" id="joinconv" class="w3-bar-item w3-button"> Inviter à rejoindre cette conversation</a>

  </div>

</div>

<div class="w3-center">

<h4>

  <i class="fas fa-envelope"></i>&nbsp;&nbsp;<span class="headmessage">Nouveau message</span></h4>

</div>

<!-- formulaire de création d'un nouveau message -->

<?= $this->Form->create(null, [
                                'id' =>'form_message',
                                'url' => ['controller' => 'Messagerie', 'action' => 'add']

                                ]);?>

      <?= $this->Form->control('destinataire[]',['type' => 'text','class' =>'w3-input w3-border destinataire','id' => 'user_message','label' =>'','placeholder'=>'Destinataire']); ?>

      <?= $this->Form->textarea('message' , ['id'=>'textarea_message','rows' => '3','required'=> 'required','maxlength' => '255','placeholder'=>' Message...']);?>

      <!-- sert à indiquer au controller Messagerie que le message que je viens d'envoyer provient de la page d'accueuil de la messagerie -->

      <?= $this->Form->hidden('indexmess' , ['id'=>'indexmess','value' =>'indexmess']);?>

<!-- menu emoji -->

    <div class="w3-dropdown-click">

      <a onclick="openemojimenu()" class="btnemoji"><img src="/twittux/img/emoji/grinning.png" width="23" height="23"></a>

      <div id="menuemojimessage" class="w3-dropdown-content w3-bar-block w3-border">

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

        <div class="w3-center">

          <br />

                <?= $this->Form->button('Envoyer',['class' =>'w3-button w3-blue w3-round']) ?>

        </div>

<?= $this->Form->end() ?>

<div hidden id="spinnerconv"></div>

<!-- spiner de chargement d'une conversation -->

<div id="displayconv" style="margin-top : 15px;">

  <!-- affichage conversation -->

</div>
</div>
</div>
</div>
