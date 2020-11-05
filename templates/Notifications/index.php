<?php
/**
 * index.php
 *
 * Affichage des notifications
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

                  <h4><span class="nb_notification"><?= count($notifications) ?></span> notification(s)</h4><!-- // nombre de notification -->

                </header>

            </div>

              <!--zone de notification sur le résultat de traitement des notifications -->
                <div id="alert-area" class="alert-area"></div>
              <!--fin zone de notification sur le résultat de traitement des notifications -->

              <br />

              <div id="list_notif">

            <?php foreach ($notifications as $notifications): ?>

                <div style="margin-bottom: 20px" data_id_notif="<?= $notifications->id_notif ;?>" <?= ($notifications->statut == 0) ? "class=\"w3-container w3-light-grey\">" : "class=\"w3-container w3-sand\">" ;?>

                    <p>

                      <!--date de la notification -->

                      <span class="w3-opacity w3-right"><?= $notifications->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

                      <!-- corps de la notification -->

                      <?= $notifications->notification ;?>

                      <br />

                      <br />

                      <hr />

                      <p>

                        <!-- lien de changement de statut de la notification -->

                        <a class="actnotif" href="#" onclick="return false;" data_statut="<?= $notifications->statut ?>" data_id_notif="<?= $notifications->id_notif ?>"><?= ($notifications->statut == 0) ? "<i class=\"fas fa-eye\"></i> Marquer comme lue</a>" : "<i class=\"fas fa-eye-slash\"></i> Marquer comme non lue</a>"; ?>

                        &nbsp;

                        <!-- lien de suppression de la notification -->

                        <a href="" onclick="return false;" class="w3-margin-bottom actnotif" data_statut="deletenotif" data_id_notif="<?= $notifications->id_notif ?>"><i class="fas fa-trash-alt"></i> Effacer</a>

                      </p>

                    </p>

            </div>

            <?php endforeach; ?>

            </div>

        <!-- pagination -->

            <div id="pagination">

        <!-- lien personnaliser -->

            <?= $this->Paginator->next('Next page'); ?>

            </div>

          </div>

        </div>

      </div>

    </div>

</div>
