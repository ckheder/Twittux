<?php
/**
 * index.php
 *
 * Affichage des utilisateurs que j'ai bloqué
 *
 */
?>
<div class="w3-col m7">

      <div class="w3-row-padding">

        <div class="w3-col m12">

          <div class="w3-card w3-round w3-white">

            <div class="w3-container w3-padding">

              <div class="w3-center">

                <header class="w3-container w3-border w3-teal">

                  <h4><span class="nb_user_block"><?= $this->Paginator->params()['count'] ?></span> utilisateur(s) bloqué(s)</h4><!-- // nombre d'utilisateur bloqués -->

                </header>

            </div>

            <!-- liste d'abonnement -->

              <div class="w3-panel w3-border w3-light-grey">

                   <p>

                     Vous trouverez ici tous les utilisateurs que vous avez bloqués. Ceux-ci ne peuvent pas voir votre profil, vos tweets, s'abonner à vous ou vous envoyer un message.
                   <br />

                   <br />

                    <i class="fas fa-info-circle"></i> Cependant, les utilisateurs bloqués qui font partie d'une conversation à plusieurs avec vous peuvent toujours vous envoyer des messages mais uniquement dans cette conversation.

                  </p>

              </div>

              <!--zone de notification sur l'état de la suppression d'un blocage -->

                <div id="alert-area" class="alert-area"></div>

              <!--fin zone de notification sur l'état de de la suppression d'un blocage -->

              <!-- liste des utilisateurs bloqués -->

              <div id="list_userblock">

              <?php

                if(count($user_block) === 0) // aucun utilisateur bloqué pour le moment
              {
                echo '<div class="w3-panel w3-border w3-light-grey">

                        <p>

                            Aucun utilisateur bloqué pour le moment.

                        </p>

                     </div>';

              }
                else
              {

            // affichage de la liste des utilisateurs bloqués

             foreach ($user_block as $user_block): ?>

                <div class="w3-container itemsocial" data-username="<?= $user_block->bloque ;?>">

                  <!-- avatar -->

                  <p>

                    <?= $this->Html->image('/img/avatar/'.$user_block->bloque.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($user_block->bloque).'')) ?>

                  <!-- lien profil -->

                    <b>

                      <?= $this->Html->link(''.h($user_block->bloque).'','/'.h($user_block->bloque).'') ?>

                    </b>

                    <br />

                  </p>

                  <!-- bouton de déblocage -->

                    <button class="w3-button w3-black w3-round"><a class="deblockuser" href="" onclick="return false;" data_username="<?= $user_block->bloque ?>"><i class="fas fa-unlock"></i> Débloquer </a></button>

                  <hr>

            </div>

          <?php

                endforeach;

                ?>

                </div>

                <?php

              }

        ?>

        <!-- pagination -->

        <!-- spinner de chargement des données par Infinite Ajax Scroll -->

        <div hidden id="spinnerajaxscroll"></div>

        <?php

          if ($this->Paginator->hasNext())
        {

         ?>

         <div class="pagination">

          <?= $this->Paginator->options(['url' => array('controller' => '/userblock/')]); ?> <!-- url modifiée pour la seconde page -->

          <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

        </div>

        <?php

        }

        ?>

        <!-- affichage d'un message une fois atteint le bas de page ou le chargement de tous les éléments -->

              <div class="w3-center">

                <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des utilisateurs bloqués</div>

              </div>

          </div>

        </div>

      </div>

    </div>

</div>
