<?php
use \App\Model\Entity\User;
$user = new User; // utilisé dans le formulaire d'inscription pour bénéficier de la validation du modèle User
?>
 <div class="w3-third w3-container">

    <p class="home">Rejoignez Twittux aujourd'hui et découvrez ce qui se passe dans le monde en temps réel.</p>

<ul class="list_accueil">
      <li><i class="fas fa-user"></i> Crée votre profil.</li>
      <li><i class="fas fa-eye"></i> Connectez-vous aux personnes partageant les mêmes centres d'intérêt.</li>
      <li><i class="fas fa-comments"></i> Discussion en temps réel.</li>
      <li><i class="fas fa-pen"></i> Réagissez aux sujets qui vous intéressent.</li>
</ul>

    </div>

   <div class="w3-third w3-container">

     <div class="w3-center">

        <h2>Inscription</h2>

    </div>
<!-- formulaire d'inscription -->
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

          </div>

          <p>
              En cliquant sur Inscription, vous acceptez nos <a href="#">Conditions générales</a>.
          </p>

            <?= $this->Form->end() ?>

              <br />
              <!--zone de notification sur l'état de l'inscription -->
                <div id="alert-area" class="alert-area"></div>
              <!--fin zone de notification sur l'état de l'inscription -->
  </div>

  <div class="w3-third w3-container">

<!-- zone connexion -->

   <div class="w3-center">

     <h2>Connexion</h2>

   </div>

 <!-- formulaire de connexion -->

 <?= $this->Form->create(null, [
     'url' => '/login'
 ]);
  ?>

   <!-- nom d'utlisateur -->

                <?= $this->Form->control('username',['class' =>'w3-input w3-border','label' =>'','id'=>'username','placeholder'=>'nom d\'utilisateur']) ?>

             <br />

   <!-- mot de passe -->

                <?= $this->Form->control('password',['class' =>'w3-input w3-border','label' =>'','id'=>'password','placeholder'=>'mot de passe']) ?>

            <br  />

                <?= $this->Form->control('remember_me', ['class' =>'w3-check','type' => 'checkbox']); ?>

   <!-- bouton d connexion -->

 <div class="w3-center">

   <p>

     <button class="w3-btn w3-blue-grey">Connexion</button></p>

   <p>

       <a onclick="document.getElementById('modalforgotpassword').style.display='block'" style="cursor:pointer;">Mot de passe oublié ?</a>

   </p>

 </div>

           <?= $this->Form->end() ?>

   <!-- fin formulaire de connexion -->

 </div>
