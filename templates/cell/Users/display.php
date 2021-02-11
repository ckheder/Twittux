<!--

 * display.ctp
 *
 * Affichagde la div de gauche concenant les informations utilisateurs :sur tweet (index,view)
 *
 */ -->

<?php foreach ($usersinfos as $usersinfos): ?>

    <div class="w3-col m3" style="margin-top:16px">
<!-- informations utilisateurs -->
      <div class="w3-card w3-round w3-white">
        <div class="w3-container">
         <h4 class="w3-center"><?= $username ?></h4>
         <p class="w3-center">
           <?=  $this->Html->image('/img/avatar/'.$username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle', 'width'=>106, 'height'=>106)); // avatar?>
         </p>
         <hr />

         <?php

           if($this->request->getParam('username') != $authName) // si je suis pas sur mon profil
         {

         ?>
         <p class="w3-center">
                 <button class="w3-button w3-indigo w3-round"><a class="sendmessage" href="" onclick="return false;" data_username="<?= $this->request->getParam('username') ?>"><i class="far fa-envelope"></i> Message </a></button>
               </p>
                  <hr />
                  <p class="w3-center">
         <span id="zone_abo">

         <!-- test de l'abonnement au profil visité -->

           <?= $this->cell('Abonnements::testabo', [$authName,$this->request->getParam('username')]); ?>

         </span>

         <!-- affichage d'un bouton de blocage -->

         <span id="zone_blocage">

           <button class="w3-button w3-red w3-round"><a class="followrequest" href="" onclick="return false;" data_action="refuse" data_username="<?= $this->request->getParam('username') ?>"><i class="fas fa-user-lock"></i> Bloquer </a></button>

         </span>
</p>
<hr />
         <?php

         }

         ?>


         <p class="desc_user"><i class="fas fa-briefcase fa-fw w3-margin-right w3-text-theme"></i> <?= $usersinfos->description ?></p>
         <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> <?= $usersinfos->lieu ?></p>
         <p><i class="fas fa-desktop fa-fw w3-margin-right w3-text-theme"></i>

           <!-- conversion du site web vers un lien cliquable si il est au bon format -->

           <?php

           $pattern_link = '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\\\\\\\w]*))?)/';

           $usersinfos->website = preg_replace($pattern_link, '<a href="$1" class="w3-text-blue" target="_blank">$1</a>', $usersinfos->website);

            ?>

           <?= $usersinfos->website ?></p>
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
