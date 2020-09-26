<!--

 * searchusers.php
 *
 * Affichage des résultats d'une recherche classique sur les utilisateurs
 *
 */ -->
  <?php

          if(count($query_users) === 0) // rien à afficher
        {

         echo '<div class="w3-container w3-blue">Aucun utilisateur ne correspond à cette recherche.</div>';

        }
          else
        {

          foreach ($query_users as $query_users): ?>

              <div class="w3-container w3-card w3-white">

                  <p>

                  <!-- avatar -->

                    <?= $this->Html->image('/img/avatar/'.$query_users->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($query_users->username).'')) ?>

                    
                  <!-- lien profil -->

                    <b><?= $this->Html->link(''.h($query_users->username).'','/'.h($query_users->username).'') ?></b>

                    <br />

                  <!-- description utilisateur -->

                    <span class="w3-opacity"><?= $query_users->description; ?></span>

                  </p>

                  <!-- test de mon abonnnement (uniquement si le résultat n'est pas le profil courant) -->

                   <span class="zone_abo" data_username="<?= $query_users->username;?>">

                  <?php

                    if($query_users->username != $authName)
                  {

                    echo $this->cell('Abonnements::testabo', [$authName, $query_users->username]); 

                  }

                ?>

                  </span>

                <br />

                <br />

            </div>

          <?php endforeach; ?>


                        <!--lien pagination -->

            <div id="pagination">

              <?= $this->Paginator->numbers() ?>

                        <?= $this->Paginator->options([

                                          'url' => array('controller' => '/search/users/'.$this->request->getParam('query').'')

                                        ]);?>

            <?= $this->Paginator->next('Next page'); ?>

            <?= $this->Paginator->counter() ?>

            </div>

<?php

 }

 ?>