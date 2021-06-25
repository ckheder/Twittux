<!--

 * modalforgotpassword.php
 *
 * Fenêtre modale affichant un formulaire pour rentrer son adresse mail d'inscription pour recevoir un message contenant un lien pour réinitialiser son mot de passe
 *
 */ -->

<div id="modalforgotpassword" class="w3-modal">

  <div class="w3-modal-content w3-card-4 w3-animate-top" style="max-width:600px">

    <header class="w3-container w3-black w3-center">

      <span onclick="document.getElementById('modalforgotpassword').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Fermer">&times;</span>

        <h3>Mot de passe oublié ?</h3>

    </header>

    <div class="w3-row-padding">

      <div class="w3-col m12">

          <div class="w3-container w3-padding">

            <!-- formulaire  -->

            <?= $this->Form->create(null, ['url' => ['controller' => 'Users', 'action' => 'forgotpassword'],
                                                    'id' => 'formforgetpassword']);?>


                      <div class="w3-container w3-panel w3-border w3-light-grey">

                            <p>

                                <i class="fas fa-info-circle"></i> Veuillez entrer l'adresse mail utilisée lors de votre inscription afin de vous envoyer un lien pour réinitialiser votre mot de passe.

                            </p>

                      </div>

                               </div>


                                <!-- input adresse mail -->

            <?= $this->Form->control('email',['class' =>'w3-input w3-border','label' =>'','id'=>'mail','placeholder'=>'adresse mail','required']) ?>

                    <div class="w3-center">

                        <br />

<!--bouton d'envoi -->

            <?= $this->Form->button('Envoyer',['class' =>'w3-button w3-blue w3-round']) ?>

                  </div>

                <br />

<?= $this->Form->end() ?>

          </div>

      </div>

    </div>

  </div>

</div>
