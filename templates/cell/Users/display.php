<!--

 * display.ctp
 *
 * Affichagde la div de gauche concenant les informations utilisateurs :sur tweet (index,view)
 *
 */ -->

<?php foreach ($usersinfos as $usersinfos): ?>

    <div class="w3-col m3">
<!-- informations utilisateurs -->
      <div class="w3-card w3-round w3-white">
        <div class="w3-container">
         <h4 class="w3-center"><?= $username ?></h4>
         <p class="w3-center">
           <?=  $this->Html->image('/img/avatar/'.$username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle', 'width'=>106, 'height'=>106)); // avatar?> 
         </p>
         <hr>
         <p><i class="fas fa-briefcase fa-fw w3-margin-right w3-text-theme"></i> <?= $usersinfos->description ?></p>
         <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> <?= $usersinfos->lieu ?></p>
         <p><i class="fas fa-calendar-alt fa-fw w3-margin-right w3-text-theme"></i> Membre depuis le <?= $usersinfos->created->i18nformat('dd MMMM YYYY');?></p>
        </div>
      </div>
      <br>
      <?php endforeach ;?>

      <div class="w3-card w3-round">
<!-- affichage média -->
        <div class="w3-white">
          <button onclick="myFunction('Demo3')" class="w3-button w3-block w3-theme-l1 w3-left-align"><i class="fa fa-users fa-fw w3-margin-right"></i> My Photos</button>
          <div id="Demo3" class="w3-hide w3-container">
         <div class="w3-row-padding">
         <br>
           <div class="w3-half">
             <img src="/w3images/lights.jpg" style="width:100%" class="w3-margin-bottom">
           </div>
           <div class="w3-half">
             <img src="/w3images/nature.jpg" style="width:100%" class="w3-margin-bottom">
           </div>
           <div class="w3-half">
             <img src="/w3images/mountains.jpg" style="width:100%" class="w3-margin-bottom">
           </div>
           <div class="w3-half">
             <img src="/w3images/forest.jpg" style="width:100%" class="w3-margin-bottom">
           </div>
           <div class="w3-half">
             <img src="/w3images/nature.jpg" style="width:100%" class="w3-margin-bottom">
           </div>
           <div class="w3-half">
             <img src="/w3images/snow.jpg" style="width:100%" class="w3-margin-bottom">
           </div>
         </div>
          </div>
        </div>      
      </div>
      <br>
      
      <br>    

    </div>