<?php
/**
 * Abonnement.ctp
 *
 * Affichage des abonnements du profil en cours de visite
 *
 */
?>
<div class="w3-col m7">

      <div class="w3-row-padding">

        <div class="w3-col m12">

          <div class="w3-card w3-round w3-white">

            <div class="w3-container w3-padding">

              <div class="w3-center">

                <header class="w3-container w3-teal">

                  <h4><span class="nb_following"><?= $this->Paginator->params()['count'] ?></span> abonnement(s)</h4><!-- // nombre d'abonnements -->

                </header>

            </div>

              <!--zone de notification sur l'état de la suppression d'un abonnement -->
                <div id="alert-area" class="alert-area"></div>
              <!--fin zone de notification sur l'état de de la suppression d'un abonnement -->

              <div id="list_following">

                <?php foreach ($abonnement_valide as $abonnement_valide): ?>

                  <div class="w3-container itemsocial" data-username="<?= $abonnement_valide->user['username'] ;?>">

                  <!-- avatar -->

                  <p>

                    <?= $this->Html->image('/img/avatar/'.$abonnement_valide->user['username'].'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($abonnement_valide->user['username']).'')) ?>

                  <!-- lien profil -->

                    <b>

                      <?= $this->Html->link(''.h($abonnement_valide->user['username']).'','/'.h($abonnement_valide->user['username']).'') ?>

                    </b>

                      <br />

                        <span class="w3-opacity"><?= $abonnement_valide->user['description']; ?></span>

                  </p>

              <?php

                    if($abonnement_valide->user['username'] != $authName) // quand je visite les abonnements d'une personne qui n'est pas moi et que j'en fais partie
                  {

              ?>

                      <button class="w3-button w3-red w3-round"><a class="unfollow" href="" onclick="return false;" data_action="delete" data_username="<?= $abonnement_valide->user['username'] ?>">Ne plus suivre</a></button>

              <?php

                  }

              ?>

                  <hr>

            </div>

            <?php endforeach; ?>

            </div>

            <!-- spinner de chargement des données par Infinite Ajax Scroll -->

            <div hidden id="spinnerajaxscroll"></div>

            <!-- pagination -->

            <?php

              if ($this->Paginator->hasNext())
            {

             ?>

              <div class="pagination">

                <?= $this->Paginator->options(['url' => ''.$this->request->getParam('username').'']); ?> <!-- url modifiée pour la seconde page -->

                <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

              </div>

            <?php

            }

            ?>

            <!-- affichage d'un message une fois atteint le bas de page ou le chargement de tous les éléments -->

            <div class="w3-center">

              <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des abonnements</div>

            </div>

          </div>

        </div>

      </div>

    </div>

</div>
