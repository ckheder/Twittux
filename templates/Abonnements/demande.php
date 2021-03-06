<?php
/**
 * demande.php
 *
 * Affichage de la liste de mes demandes d'abonnement
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

                      <h4>

                        <span class="nb_attente"><?= $this->Paginator->params()['count'] ?></span> demande(s) d'abonnement en attente.

                      </h4>

                  </header>

              </div>

            <!--zone de notification-->

                <div id="alert-area" class="alert-area"></div>

            <!--fin zone de notification-->

              <br />

            <!-- liste des demandes -->

                <div id="list_request">

                  <?php foreach ($abonnement_attente as $abonnement_attente): ?>

                    <div class="w3-container itemsocial" data-username="<?= $abonnement_attente->Users['username'] ;?>">

                  <!-- avatar -->

                  <p>

                      <?= $this->Html->image('/img/avatar/'.$abonnement_attente->Users['username'].'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($abonnement_attente->Users['username']).'')) ?>

                  <!-- lien profil -->

                    <b>

                      <?= $this->Html->link(''.h($abonnement_attente->Users['username']).'','/'.h($abonnement_attente->Users['username']).'') ?>

                    </b>

                    <br />

                    <span class="w3-opacity"><?= $abonnement_attente->Users['description']; ?></span>

                  </p>

                  <!-- Bouton d'accepatin ou de refus d'une demande d'abonnement ainsi qu'un bouton de blocage-->

                  <button class="w3-button w3-green w3-round"><a class="followrequest" href="" onclick="return false;" data_action="accept" data_username="<?= $abonnement_attente->Users['username'] ?>"><i class="fas fa-check"></i> Accepter</a></button>

                  &nbsp;&nbsp;

                  <button class="w3-button w3-red w3-round"><a class="followrequest" href="" onclick="return false;" data_action="refuse" data_username="<?= $abonnement_attente->Users['username'] ?>"><i class="fas fa-times"></i> Refuser </a></button>

                  &nbsp;&nbsp;

                  <button class="w3-button w3-black w3-round"><a class="blockuser" href="" onclick="return false;" data_username="<?= $abonnement_attente->Users['username'] ?>"><i class="fas fa-lock"></i> Bloquer </a></button>

                     <hr>

                </div>

            <?php endforeach; ?>

          </div>

          <!-- pagination -->

          <!-- spinner de chargement des données par Infinite Ajax Scroll -->

          <div hidden id="spinnerajaxscroll"></div>

          <?php

            if ($this->Paginator->hasNext())
          {

          ?>

          <div class="pagination">

            <?= $this->Paginator->options(['url' => array('controller' => '/abonnement/demande')]); ?> <!-- url modifiée pour la seconde page -->

            <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

          </div>

        <?php

          }

        ?>

        <!-- affichage d'un message une fois atteint le bas de page ou le chargement de tous les éléments -->

          <div class="w3-center">

            <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des demandes d'abonnements</div>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>
