<!--

 * searchusers.php
 *
 * Affichage des résultats d'une recherche classique sur les utilisateurs
 *
 */ -->

<div class="query_users">

  <?php

          if(count($query_users) === 0) // rien à afficher
        {

         echo '<div class="w3-container w3-blue">Aucun utilisateur ne correspond à cette recherche.</div></div>';

        }
          else
        {

          foreach ($query_users as $query_users): ?>

              <div class="w3-container w3-card w3-white itemsearch">

                  <p>

                  <!-- avatar -->

                    <?= $this->Html->image('/img/avatar/'.$query_users->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($query_users->username).'')) ?>


                  <!-- lien profil -->

                    <b><?= $this->Html->link(''.h($query_users->username).'','/'.h($query_users->username).'') ?></b>

                    <br />

                  <!-- description utilisateur -->

                    <span class="w3-opacity"><?= $query_users->description; ?></span>

                  </p>

                  <?php

                    if($authName) // si je suis authentifié, on fais le test si je suis abonné/blloqué ou non
                  {

                    if($query_users->username != $authName) // test de mon abonnement / blocage (uniquement si le résultat n'est pas le profil courant)
                  {

                    ?>

                  <!-- test abonnnement -->

                   <span class="zone_abo" data_username="<?= $query_users->username;?>">

                    <?= $this->cell('Abonnements::testabo', [$authName, $query_users->username]); ?>

                  </span>

                  <!-- test blocage -->

                   <span class="zone_blocage" data_username="<?= $query_users->username;?>">

                    <?= $this->cell('Blocage', [$authName, $query_users->username]); ?>

                  </span>

                  <?php

                    }
                  }

                                  ?>

                <br />

                <br />

            </div>

          <?php endforeach;

 }

 ?>

<!-- spinner de chargement des données par Infinite Ajax Scroll -->

 <div hidden id="spinnerajaxscroll"></div>

 <!-- pagination -->

 <?php

   if ($this->Paginator->hasNext())
 {

  ?>

  <div class="pagination">

    <?= $this->Paginator->options(['url' => array('controller' => '/search/users/'.$this->request->getParam('query').'')]); ?> <!-- url modifiée pour la seconde page -->

    <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

  </div>

 <?php

 }

 ?>

<!-- affichage d'un message une fois atteint le bas de page ou le chargement de tous les éléments -->

 <div class="w3-center">

   <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des résultats pour <?= $this->request->getParam('query') ?></div>

 </div>

 </div>
