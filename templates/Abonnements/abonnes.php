<?php
/**
 * Abonne.php
 *
 * Affichage des abonnés du profil en cours de visite
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

                  <h4><span class="nb_follower"><?= $this->Paginator->params()['count'] ?></span> abonné(s)</h4> <!-- // nombre d'abonnés -->

                </header>

              </div>

                            <!--zone de notification -->
              <div id="alert-area" class="alert-area"></div>
                            <!--fin zone de notification -->

              <br />

              <!-- liste des abonnés -->

              <div id="list_followers">

                <?php foreach ($abonne_valide as $abonne_valide): ?>

                  <div class="w3-container itemsocial" data-username="<?= $abonne_valide->Users['username'] ;?>">

                  <!-- avatar -->

                    <p>

                      <?= $this->Html->image('/img/avatar/'.$abonne_valide->Users['username'].'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-margin-right', 'style'=>'width:60px', 'title' => ''.h($abonne_valide->Users['username']).'')) ?>

                  <!-- lien profil -->

                    <b>

                      <?= $this->Html->link(''.h($abonne_valide->Users['username']).'','/'.h($abonne_valide->Users['username']).'') ?>

                    </b>

                    <br />

                    <span class="w3-opacity"><?= $abonne_valide->Users['description']; ?></span>

                  </p>

                  <span class="zone_abo" data_username="<?= $abonne_valide->Users['username'];?>">

                  <?php // si le résultat n'est pas moi, chargement de la cell de test d'abonnement

                    if($abonne_valide->Users['username'] != $authName) // cas ou je visite une liste d'abonné et que je suis dedans
                  {

                    echo $this->cell('Abonnements::testabo', [$authName, $abonne_valide->Users['username']]);
                  }

                  ?>

                </span>

                <!-- affichage d'un bouton de blocage -->

                  <span class="zone_blocage" data_username="<?= $abonne_valide->Users['username'];?>">

                    <?php

                      if($abonne_valide->Users['username'] != $authName) // cas ou je visite une liste d'abonné et que je suis dedans
                    {

                      echo $this->cell('Blocage', [$authName, $abonne_valide->Users['username']]);

                    }

                    ?>

                  </span>

                  </div>

            <hr>

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

              <?= $this->Paginator->options(['url' => ''.$this->request->getParam('username').'']); ?> <!-- url modifiée pour la seconde page -->

              <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

            </div>

            <?php

            }

            ?>

            <!-- affichage d'un message une fois atteint le bas de page ou le chargement de tous les éléments -->

            <div class="w3-center">

              <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des abonnés</div>

            </div>

        </div>

      </div>

    </div>

  </div>

</div>
