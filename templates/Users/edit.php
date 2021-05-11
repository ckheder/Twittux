<div class="w3-center">

  <header class="w3-container w3-border w3-teal">

    <h4>Paramètres de mon compte</h4>

  </header>

</div>

 <div class="w3-container w3-white">

    <!--zone de notification sur l'état de l'envoi d'un tweet -->
    <div id="alert-area" class="alert-area"></div>
    <!--fin zone de notification sur l'état de l'envoi d'un tweet -->

<h3>Mes informations</h3>

    <hr>

<!-- création du formulaire de mise à jour des informations -->

            <?= $this->Form->create(null, ['url' => '/settings',
  'enctype' => 'multipart/form-data','id' =>'form_settings']) ?>

<!-- section avatar -->

  <div class="w3-row w3-section">

    <div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-portrait"></i></div>

      <span class="w3-opacity">Changer ma photo </span>

        <br />

<div class="w3-rest">

<?= $this->Form->file('submittedfile',['id' => 'submittedfile']) ?> <!-- input avatar -->

<span class="w3-opacity">(jpg/jpeg) 3mo maximum </span>

<div class="w3-center">

  <p class="w3-opacity">Prévisualisation</p>

  <!-- zone de preview -->

<?= $this->Html->image('default.png', ['alt' => '','id' => 'previewHolder', 'width' =>128, 'height'=> 'auto','class'=>'w3-circle']); ?>

</div>

</div>

</div>

<!-- input mot de passe -->

<div class="w3-row w3-section">

<div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-key"></i></div>

<div class="w3-rest">

    <?= $this->Form->control('password',['class' =>'w3-input w3-border','label' =>'','id'=>'password','placeholder'=>'Nouveau mot de passe']); ?>

</div>

</div>

<!-- input confirmation mot de passe -->

<div class="w3-row w3-section">

<div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-key"></i></div>

<div class="w3-rest">

      <?= $this->Form->control('confirmpassword',['type'=>'password','class' =>'w3-input w3-border','id'=>'confirmpassword','label' =>'','placeholder'=>'Confirmer nouveau mot de passe']); ?>

</div>

</div>

<!-- input adresse mail -->

<div class="w3-row w3-section">

<div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-at"></i></div>

<div class="w3-rest">

      <?= $this->Form->control('email',['class' =>'w3-input w3-border','label' =>'','id'=>'mail','placeholder'=>'Nouvelle adresse mail']); ?>

</div>

</div>
<!-- input confirmation adresse mail -->

<div class="w3-row w3-section">

<div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-at"></i></div>

<div class="w3-rest">

      <?= $this->Form->control('confirmemail',['class' =>'w3-input w3-border','id' => 'confirmemail','label' =>'','placeholder'=>'Confirmer nouvelle adresse mail']); ?>

</div>

</div>

<!-- input description -->

<div class="w3-row w3-section">

<div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-smile"></i></div>

<div class="w3-rest">

      <?= $this->Form->textarea('description',['class' =>'w3-input w3-border','id' => 'description','label' =>'','placeholder'=>'Brève description de moi-même']); ?>

</div>

</div>

<!-- input lieu -->

<div class="w3-row w3-section">

<div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-globe"></i></div>

<div class="w3-rest">

      <?= $this->Form->control('lieu',['type' => 'text','class' =>'w3-input w3-border','id' => 'lieu','label' =>'','placeholder'=>'Ex : Paris, New York, Montréal,...']); ?>

</div>

</div>

<!-- input website -->

<div class="w3-row w3-section">

<div class="w3-col" style="width:50px"><i class="w3-xxlarge fas fa-desktop"></i></div>

<div class="w3-rest">

      <?= $this->Form->control('website',['type' => 'text','class' =>'w3-input w3-border','id' => 'website','label' =>'','placeholder'=>'https://www.monsite.com']); ?>

</div>

</div>

    <div class="w3-center">

      <br />

            <?= $this->Form->button('Mise à jour',['class' =>'w3-btn w3-blue-grey']) ?>

    </div>

            <?= $this->Form->end() ?>

<!-- fin formulaire -->

<!-- Paramètres -->

        <h3>Paramètres</h3>

         <hr>

             <div class="w3-center">

                  <span class="w3-opacity">Configurer mon profil </span>
            </div>

            <hr>

             <p>

               <span class="w3-opacity">Définir mon profil </span>

               <span class="w3-right">

               <input class="w3-radio" type="radio" name="profil" <?= ($setup_profil == 'public') ? "checked='checked'" : ''; ?> onchange="setupprofil()" value="public">
               <label>Public</label>

               <input class="w3-radio" type="radio" name="profil" <?= ($setup_profil == 'prive') ? "checked='checked'" : ''; ?> onchange="setupprofil()" value="prive">
               <label>Privé</label>

             </span>

           </p>

<!-- notifications -->

            <h3>Notifications</h3>

              <hr>

              <div class="w3-center">

                  <span class="w3-opacity" id="notifications">Configurer mes notifications </span>

              </div>

              <hr>

              <p>

<!-- notif de nouveau message -->

              <span class="w3-opacity">Recevoir des notifications de nouveaux messages</span>

              <span class="w3-right">

              <input class="w3-radio" type="radio" name="notif_message" <?= ($notif_message == 'oui') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="oui">
              <label>Oui</label>

              <input class="w3-radio" type="radio" name="notif_message" <?= ($notif_message == 'non') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="non">
              <label>Non</label>

              </span>

              <br />

              <br />

<!-- notif de partage de tweet -->

              <span class="w3-opacity">Recevoir des notifications de partage (quand l'un de vos posts est partagés)</span>

              <span class="w3-right">

              <input class="w3-radio" type="radio" name="notif_partage" <?= ($notif_partage == 'oui') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="oui">
              <label>Oui</label>

              <input class="w3-radio" type="radio" name="notif_partage" <?= ($notif_partage == 'non') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="non">
              <label>Non</label>

              </span>

              <br />

              <br />

<!-- notif de citation dans un tweet -->

              <span class="w3-opacity">Recevoir des notifications de citation (quand votre nom est cité dans un tweet)</span>

              <span class="w3-right">

              <input class="w3-radio" type="radio" name="notif_citation" <?= ($notif_citation == 'oui') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="oui">
              <label>Oui</label>

              <input class="w3-radio" type="radio" name="notif_citation" <?= ($notif_citation == 'non') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="non">
              <label>Non</label>

              </span>

              <br />

              <br />

<!-- notif de demande ou de nouvel abonnement -->

              <span class="w3-opacity">Recevoir des notifications d'abonnements</span>

              <span class="w3-right">

              <input class="w3-radio" type="radio" name="notif_abonnement" <?= ($notif_abonnement == 'oui') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="oui">
              <label>Oui</label>

              <input class="w3-radio" type="radio" name="notif_abonnement" <?= ($notif_abonnement == 'non') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="non">
              <label>Non</label>

              </span>

              <br />

              <br />

<!-- notif de nouveau commentaire -->

              <span class="w3-opacity">Recevoir des notifications de nouveaux commentaires</span>

              <span class="w3-right">

              <input class="w3-radio" type="radio" name="notif_commentaire" <?= ($notif_commentaire == 'oui') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="oui">
              <label>Oui</label>

              <input class="w3-radio" type="radio" name="notif_commentaire" <?= ($notif_commentaire == 'non') ? "checked='checked'" : ''; ?> onchange="setupnotif(this.name)" value="non">
              <label>Non</label>

              </span>

            </p>

<!-- supprimer compte -->

            <h3>Supprimer mon compte</h3>

             <hr>

              <span class="w3-opacity">Effacer mon compte supprimera tous mes tweets, message, partage, commentaire,... </span>

             <hr>

             <p class="w3-center">

               <a href="/twittux/deleteaccount" onclick="return confirm(
        'Etes vous sur de vouloir supprimer votre compte ? Cette action est irréversible.'
    );"class="w3-button w3-red"><i class="fas fa-trash-alt"></i> Supprimer mon compte</a>

             </p>

        </div>
