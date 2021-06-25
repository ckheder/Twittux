<?php
/**
 *  Vue contenant l'affichage d'un formulaire pour rentrer et confirmer un nouveau mot de passe
 */
?>

<?= $this->element('offlinenavbar') ?>

        <div class="w3-container w3-content" style="margin-top:60px;">

          <div class="w3-col m4 w3-light-grey w3-padding">

            <div class="w3-center">

            <h4><i class="fas fa-info-circle"></i> Conseils</h4>

              </div>

              <!-- liset affichage de conseil sur les mots de passe fort -->

              <ul class="w3-ul">

                <li>
                    8 caractères minimum
                </li>

                <li>
                    Utilisez des chiffres
                </li>

                <li>
                    Utilisez des lettres majuscules et minuscules et des caractères spéciaux (#,@,$,...)
                </li>

                <li>
                    Évitez les informations personnelles (date de naissance, identifiant, nom d’une proche…), les suites de caractères du clavier (azerty, 12345), des mots du dictionnaire ou des paroles de chanson.
                </li>

                <li>
                    Utilisez un générateur de mot de passe fort : <a href="https://www.lastpass.com/fr/password-generator" class="w3-text-blue" target="_blank">Last Pass</a> ou <a href="https://www.dashlane.com/fr/features/password-generator" class="w3-text-blue" target="_blank">DashLane</a>
                </li>

              </ul>

          </div>

          <div class="w3-col m5">

          <div class="w3-center">

            <header class="w3-container w3-teal">

              <h4>Nouveau mot de passe</h4>

            </header>

        </div>

        <br  />

<?php echo $this->Flash->render() ?>

<!-- formulaire mot de passe -->

<?= $this->Form->create() ?>

  <?= $this->Form->password('password',['class' =>'w3-input w3-border','label' =>'','placeholder'=>'Nouveau mot de passe']) ?>

<br />

  <?= $this->Form->password('confirmpassword',['class' =>'w3-input w3-border','label' =>'','placeholder'=>'Confirmer nouveau mot de passe']) ?>

<div class="w3-center">

  <br />

<!--bouton d'envoi -->

  <?= $this->Form->button('Valider',['class' =>'w3-button w3-blue w3-round']) ?>

</div>

<br />

<?= $this->Form->end() ?>

<div class="w3-panel w3-amber">

<p>

  Une fois validé, vous serez invité à vous connecter avec votre nouveau mot de passe.

</p>

</div>

</div>

</div>

<!-- modification du titre de page -->

<script>

  document.title = 'Réinitialiser mon mot de passe Twittux';

</script>
