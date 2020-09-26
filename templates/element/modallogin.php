<!-- modallogin 
  Fenêtre modal de connexion -->

<div id="modallogin" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-top" style="max-width:600px">
      <header class="w3-container w3-black w3-center"> 
        <span onclick="document.getElementById('modallogin').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Fermer">&times;</span>
        <h3>Connexion</h3>
      </header>
      <div class="w3-container">

        <br />
<?= $this->Form->create(null, [
    'url' => '/login'
]);
 ?>

    <?= $this->Form->control('username',['class' =>'w3-input w3-border w3-light-grey','label' =>'','id'=>'username','placeholder' =>'nom d\'utilisateur']) ?>

    <br />

    <?= $this->Form->password('password',['class' =>'w3-input w3-border w3-light-grey','label' =>'','id'=>'password','placeholder' =>'mot de passe']) ?>


    <div class="w3-center">

        <p><button class="w3-btn w3-blue-grey">Connexion</button></p>

    </div>

<?= $this->Form->end() ?>


      </div>
          <footer class="w3-container w3-black">
<p class="w3-right">Mot de passe <a href="#">oublié?</a></p>
</footer>
    </div>
  </div>