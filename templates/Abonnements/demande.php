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

                  <header class="w3-container w3-light-grey">

                      <h4> <span class="nb_attente"><?= count($abonnement_attente) ; ?></span> demande(s) d'abonnement en attente.</h4>           
                  </header>
              </div>

            <!-- liste d'abonnement -->

            <div id="listabovalide">

                            <!--zone de notification-->
                <div id="alert-area" class="alert-area"></div>
                            <!--fin zone de notification-->

              <br />

            <?php foreach ($abonnement_attente as $abonnement_attente): ?>

                <div class="w3-container" data-username="<?= $abonnement_attente->Users['username'] ;?>">

                  <!-- avatar -->

                  <p>

                    <?= $this->Html->image('/img/avatar/'.$abonnement_attente->Users['username'].'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($abonnement_attente->Users['username']).'')) ?>

                  <!-- lien profil -->

                    <b><?= $this->Html->link(''.h($abonnement_attente->Users['username']).'','/'.h($abonnement_attente->Users['username']).'') ?></b>

                    <br />

                  <span class="w3-opacity"><?= $abonnement_attente->Users['description']; ?></span>

                  </p>

                  <!-- Bouton d'accepatin ou de refus d'une demande d'abonnement -->

                  <button class="w3-button w3-green w3-round"><a class="followrequest" href="" onclick="return false;" data_action="accept" data_username="<?= $abonnement_attente->Users['username'] ?>">Accepter</a></button>

                  &nbsp;&nbsp;

                  <button class="w3-button w3-red w3-round"><a class="followrequest" href="" onclick="return false;" data_action="refuse" data_username="<?= $abonnement_attente->Users['username'] ?>">Refuser </a></button>

                     <hr>

                </div>

            <?php endforeach; ?>

          </div>

        <!-- pagination -->

          <div id="pagination">

        <!-- lien personnaliser -->

              <?= $this->Paginator->options([
                                              'url' => array('controller' => '/abonnement/'.$this->request->getParam('username').'')
                                            ]);

              ?>

            <?= $this->Paginator->next('Next page'); ?>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>
