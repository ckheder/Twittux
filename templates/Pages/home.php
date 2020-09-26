<?php
use \App\Model\Entity\User;
$user = new User; // utilisé dans le formulaire d'inscription pour bénéficier de la validation du modèle User
?>

<!-- navbar -->
     <div class="w3-bar w3-black w3-left-align w3-large">

<!-- lien affichage navbar responsive --> 
      <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-left w3-hover-white w3-large" href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a> 
      <!-- lien actualité -->
        <a href="#" title="Actualités" class="w3-bar-item w3-button w3-hide-small w3-padding-large"><i class="fa fa-globe"></i></a>
<!-- lien d'ouverture de la modale login -->
        <a onclick="document.getElementById('modallogin').style.display='block'" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white w3-right"><i class="fas fa-sign-in-alt"></i></a>
      </div>
<!-- Navbar on small screens -->
      <div id="smallscreensnav" class="w3-bar-block w3-hide w3-large">
<!-- lien actualité -->
        <a href="#" class="w3-bar-item w3-button w3-padding-large"><i class="fa fa-globe"></i> Actualités</a>
<!-- lien d'ouverture de la modale login -->
        <a onclick="document.getElementById('modallogin').style.display='block'" class="w3-bar-item w3-button w3-padding-large"><i class="fas fa-sign-in-alt"></i> Connexion</a>
      </div>

 <div class="w3-row" style="border: 1px solid #e6ecf0;">

   <div class="w3-half w3-container">

    <p class="home">Rejoignez Twittux aujourd'hui et découvrez ce qui se passe dans le monde en temps réel.</p>

<ul class="list_accueil">
      <li><i class="fas fa-user"></i> Crée votre profil.</li>
      <li><i class="fas fa-eye"></i> Connectez-vous aux personnes partageant les mêmes centres d'intérêt.</li>
      <li><i class="fas fa-comments"></i> Discussion en temps réel.</li>
      <li><i class="fas fa-pen"></i> Réagissez aux sujets qui vous intéressent.</li>
</ul>

    </div>

   <div class="w3-half w3-container">

      <?= $this->Flash->render() ?>

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

            <?= $this->Form->button('Inscription',['class' =>'w3-btn w3-blue-grey']) ?>

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

</div> 

