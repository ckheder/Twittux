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

                <header class="w3-container w3-light-grey">

                  <h4><span class="nb_following"><?= count($abonnement_valide) ?></span> abonnement(s)</h4><!-- // nombre d'abonnements -->

                </header>

            </div>

            <!-- liste d'abonnement -->

            <div id="listabovalide">

              <!--zone de notification sur l'état de la suppression d'un abonnement -->
                <div id="alert-area" class="alert-area"></div>
              <!--fin zone de notification sur l'état de de la suppression d'un abonnement -->

              <br />

            <?php foreach ($abonnement_valide as $abonnement_valide): ?>

                <div class="w3-container" data-username="<?= $abonnement_valide->user['username'] ;?>">

                  <!-- avatar -->

                  <p>

                    <?= $this->Html->image('/img/avatar/'.$abonnement_valide->user['username'].'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($abonnement_valide->user['username']).'')) ?>

                  <!-- lien profil -->

                    <b><?= $this->Html->link(''.h($abonnement_valide->user['username']).'','/'.h($abonnement_valide->user['username']).'') ?></b>

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

        <!-- pagination -->

            <div id="pagination">

        <!-- lien personnaliser -->

              <?= $this->Paginator->options([
                                              'url' => array('controller' => '/abonnement/'.$this->request->getParam('username').'')
                                            ]); ?>

            <?= $this->Paginator->next('Next page'); ?>

            </div>

          </div>

        </div>

      </div>

    </div>

</div>
