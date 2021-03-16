<?php
/**
 *  Vue contenant le formulaire de connexion
 */

// chargement de l'entité user qui va permettre d'utiliser les règles de validation d'un utilisateur

use \App\Model\Entity\User;

$user = new User;

?>

 <div class="w3-row" style="border: 1px solid #e6ecf0;">

   <?= $this->Flash->render();?>

   <div class="w3-half w3-container">

<!-- zone connexion -->

   	<div class="w3-center">

      <h2>Connexion</h2>

    </div>

  <!-- formulaire de connexion -->

            <?= $this->Form->create() ?>

    <!-- nom d'utlisateur -->

  	             <?= $this->Form->control('username',['class' =>'w3-input w3-border','label' =>'','id'=>'username','placeholder'=>'nom d\'utilisateur']) ?>

              <br />

    <!-- mot de passe -->

  	             <?= $this->Form->control('password',['class' =>'w3-input w3-border','label' =>'','id'=>'password','placeholder'=>'mot de passe']) ?>

    <!-- bouton d connexion -->

	<div class="w3-center">

		<p>

      <button class="w3-btn w3-blue-grey">Connexion</button></p>

		<p>

        <a href="#">Mot de passe oublié ?</a>

    </p>

	</div>

            <?= $this->Form->end() ?>

    <!-- fin formulaire de connexion -->

  </div>

<!-- fin zone connexion -->

<!-- zone inscription -->

   <div class="w3-half w3-container">

      <div class="w3-center">

        <h2>Inscription</h2>

      </div>

      <!-- formulaire inscription -->

        <?= $this->Form->create($user, [
                                        'url' => ['controller' => 'Users', 'action' => 'add']
                                        ]);?>

        <!-- input username -->

    <?= $this->Form->control('username',['class' =>'w3-input w3-border','label' =>'','id'=>'username','placeholder'=>'nom d\'utilisateur']) ?>

    <div class="help-block">Entre 5 et 20 caractères, les caractères spéciaux ne sont pas autorisés.</div>

        <!-- input mot de passe -->

    <?= $this->Form->password('password',['class' =>'w3-input w3-border','label' =>'','id'=>'password','placeholder'=>'mot de passe']) ?>

    <div class="help-block">Conseils : 8 caractères minimum, utilisez des chiffres, des lettres majuscules et minuscules et des caractères spéciaux (#,@,$,...)</div>

        <!-- input adresse mail -->

    <?= $this->Form->control('email',['class' =>'w3-input w3-border','label' =>'','id'=>'mail','placeholder'=>'adresse mail']) ?>

    <br />

     <div class="w3-center">

    <?= $this->Form->button('Inscription',['class' =>'w3-btn w3-blue-grey']) ?>

    </div>

          <p>
              En cliquant sur Inscription, vous acceptez nos <a href="#">Conditions générales</a>.
          </p>

            <?= $this->Form->end() ?>

    <!-- fin formulaire inscription -->

              <br />
  </div>

</div>

<!-- fin zone inscription -->
