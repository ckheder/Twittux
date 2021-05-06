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

          <p class="w3-center">

         <?php

          if($authName) // si je suis authentifié , test de l'abonnement , envoi de message et blocage
         {

           if($this->request->getParam('username') != $authName) // si je suis pas sur mon profil
         {
            if($no_see != 1) // si cette variable ne vaut pas 0 (profil autorisé) ou 2 (profil privé mais non abonné)
           {
         ?>
         <!-- bouton envoyer un message -->

                 <button class="w3-button w3-indigo w3-round"><a class="sendmessage" href="" onclick="return false;" data_username="<?= $this->request->getParam('username') ?>"><i class="far fa-envelope"></i> Message </a></button>

              <hr />

                  <span id="zone_abo">

                      <!-- test de l'abonnement au profil visité -->

                      <?= $this->cell('Abonnements::testabo', [$authName,$this->request->getParam('username')]); ?>

                    </span>


         <?php

         }

         ?>
         <span id="zone_blocage">

           <?= $this->cell('Blocage', [$authName,$this->request->getParam('username')]); ?>

         </span>

         <hr />

       </p>

         <?php
       }
     }

         ?>

         <!-- information utilisateur -->

         <p class="desc_user"><i class="fas fa-briefcase fa-fw w3-margin-right w3-text-theme"></i> <?= $usersinfos->description ?></p>

         <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> <?= $usersinfos->lieu ?></p>

         <p><i class="fas fa-desktop fa-fw w3-margin-right w3-text-theme"></i>

           <!-- conversion du site web vers un lien cliquable si il est au bon format -->

           <?php

           $pattern_link = '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\\\\\\\w]*))?)/';

           $usersinfos->website = preg_replace($pattern_link, '<a href="$1" class="w3-text-blue" target="_blank">$1</a>', $usersinfos->website);

            ?>

           <?= $usersinfos->website ?></p>

         <p>

           <i class="fas fa-calendar-alt fa-fw w3-margin-right w3-text-theme"></i> Membre depuis le <?= $usersinfos->created->i18nformat('dd MMMM YYYY');?>

         </p>

         <!-- affichage du nombre d'abonnemens et d'abonnés -->

            <?= $this->cell('Abonnements::infosabo', [$this->request->getParam('username')]); ?>

        </div>

      </div>

      <br>

      <?php endforeach ;

      if($no_see === 0) // si cette variable ne vaut pas 0 (profil autorisé) ou 2 (profil privé mais non abonné)
     {
   ?>

      <div class="w3-card w3-round">

<!-- affichage média -->

        <div class="w3-white">

           <div class="w3-row-padding">

          <?php // parcours du dossier contenant les emojis et affichage dans la div

          $dir = WWW_ROOT . 'img/media/'.$this->request->getParam('username').''; // chemin du dossier

          $array_media = array(); // tableau de stockage des médias par date

          $counter = 0; // initialisation d'un compteur de média : on limite aux 4 derniers médias postés

          // change le dossier en répertoire

          chdir($dir);

          // tri du tableau multi dimensionnels  : tous les fichiers sont triés selon leur date de dernière modification

          array_multisort(array_map('filemtime', ($files = glob("*.*"))), SORT_DESC, $files);

            foreach($files as $filename)
          {
            $array_media[] = $filename;

            $counter++;

            // si il y'a 4 médias maximum on arrête le parcours du tableau

              if ($counter >= 4)
            {
              break;
            }

          }

          // on affiche les médias issus du tableau précédent

            if(count($array_media) > 0)
           {

            foreach($array_media as $media_user)
          {

            $img_media_user_no_extension = substr($media_user, 0, strrpos($media_user, '.')); // on supprime l'extension du fichier pour crée un lien vers le post contenant le média

            echo '<a href="./statut/'.$img_media_user_no_extension.'"><img src="/twittux/img/media/'.$this->request->getParam('username').'/'.$media_user.'" class="media_user" /></a>';

          }

        }
          else
        {
          echo '<div class="w3-panel w3-border w3-light-grey"><p>

               Cette utilisateur n\'a partagé aucun média.</p></div>';
        }

          ?>

         </div>

        </div>

      </div>

    <?php } ?>

      <br>

      <br>

    </div>
