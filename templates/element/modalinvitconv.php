<!--

 * modalinvitconv.php
 *
 * Fenêtre modale affichant un formulaire pour inviter 1 ou plusieurs membres à rejoindre une conversation
 *
 */ -->

<div id="modalinvitconv" class="w3-modal">

  <div class="w3-modal-content w3-card-4 w3-animate-top" style="max-width:600px">

    <header class="w3-container w3-black w3-center">

      <span onclick="closemodaleaddconv()" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Fermer">&times;</span>

        <h3>Inviter</h3>

    </header>

    <div class="w3-row-padding">

      <div class="w3-col m12">

          <div class="w3-container w3-padding">

            <h6 class="w3-opacity" id="headeraddconv"></h6>

            <!-- formulaire d'invitation -->

<?= $this->Form->create(null, [
                                'id' =>'form_addtoconv',
                                'url' => ['controller' => 'Conversation', 'action' => 'addtoconv']

                                ]);?>


<span id="inputadduser">

<!-- les input sont ajouter dynamiquement par le javascript pour ne pas dépasser 5 membres par conversation -->

</span>

<div class="w3-center">

  <br />

<!--bouton d'envoi -->

        <?= $this->Form->button('Envoyer',['class' =>'w3-button w3-blue w3-round']) ?>

</div>

<?= $this->Form->end() ?>

          </div>

      </div>

    </div>

  </div>

</div>
