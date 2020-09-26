<!--

 * userhashtag.php
 *
 * Mise en page des résultats d'une recherche avec hashtag sur les description d'utilisateur
 *
 */ -->

  <?php

          if(count($resultat_users) === 0) // rien à afficher
        {

         echo '<div class="w3-container w3-blue">Aucun utilisateur ne correspond à cette recherche.</div>';

        }
          else
        {

          foreach ($resultat_users as $resultat_users): ?>

              <div class="w3-container w3-card w3-white">

                  <!-- avatar -->

                  <p>

                    <?= $this->Html->image('/img/avatar/'.$resultat_users->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($resultat_users->username).'')) ?>

                  <!-- lien profil -->

                    <b><?= $this->Html->link(''.h($resultat_users->username).'','/'.h($resultat_users->username).'') ?></b>

                    <br />

                  <span class="w3-opacity"><?= $resultat_users->description; ?></span>

                  </p>

                  <!-- test de mon abonnnement (uniquement si le résultat n'est pas le profil courant) -->

                   <span class="zone_abo" data_username="<?= $resultat_users->username;?>">

                <?php

                    if($resultat_users->username != $authName)
                  {

                    echo $this->cell('Abonnements::testabo', [$authName, $resultat_users->username]); 

                  }

                ?>

                  </span>

                  <hr>

            </div>

                        <!--lien pagination -->

            <div id="pagination">

              <?= $this->Paginator->numbers() ?>

                        <?= $this->Paginator->options([

                                          'url' => array('controller' => '/search/hashtag/users/'.$this->request->getParam('query').'')

                                        ]);?>

            <?= $this->Paginator->next('Next page'); ?>

            <?= $this->Paginator->counter() ?>

            </div>

<?php

endforeach;
        }

?>

<!-- FIN RESULTAT UTILISATEUR -->