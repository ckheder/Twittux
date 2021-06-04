<!--
* view.php
*
* affichage des personnes aimant un post (chargé via modale)
-->

            <?php foreach ($username_like as $username_like): ?>

                <div class="w3-round w3-margin itemlike">

                  <!-- avatar -->

                    <?= $this->Html->image('/img/avatar/'.$username_like->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>' w3-circle', 'style'=>'width:50px', 'title' => ''.h($username_like->username).'')) ?>

                  <!-- lien profil -->

                   <strong>

                     <?= $this->Html->link(''.h($username_like->username).'','/'.h($username_like->username).'') ?>

                  <?php

                     // test de mon abonnement  (uniquement si je ne suis pas la personne qui like)

                        if($username_like->username != $authName)
                      {

                        ?>

                   <!-- test abonnnement -->

                    <span class="zone_abo_like w3-right" data_username="<?= $username_like->username;?>">

                     <?= $this->cell('Abonnements::testabo', [$authName, $username_like->username]); ?>

                   </span>

                <?php

                     }

                      ?>

                   </strong>

                </div>

            <?php endforeach; ?>

            <!--lien pagination -->

            <!-- spinner de chargement des données par Infinite Ajax Scroll -->

            <div hidden id="spinnerajaxscroll"></div>

            <?php

                if ($this->Paginator->hasNext())
              {

            ?>

                <div class="paginationlike">

                  <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

                </div>

            <?php

            }

            ?>
