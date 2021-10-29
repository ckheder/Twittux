<!--

 * index.php
 *
 * Affichage de tous les hashtags paginés par 10
 *
 */ -->

 <!--zone de notification -->

 <div id="alert-area" class="alert-area"></div>

<!--fin zone de notification  -->

<div class="list_hashtag w3-panel w3-border w3-light-grey">

  <h4 class="w3-center"><i class="fas fa-globe"></i> Tendances</h4>

    <?php foreach ($hashtags as $hashtags): ?>

        <p class="itemhashtag" id="<?= $hashtags->hashtag ?>">

            <strong>

              <!-- nom du hashtag -->

                		<a href="/twittux/search/hashtag/%23<?= $hashtags->hashtag ?>" class="w3-text-blue">#<?= $hashtags->hashtag ?></a>

            </strong>

                      <br />

              <!-- nombre de post contenant le hashtag -->

                    <span class="w3-opacity"><span class="nbtweets"><?= $hashtags->nb_post_hashtag ?></span> Tweets</span>

        </p>

                <?php endforeach; ?>

                  <!-- spinner de chargement des données par Infinite Ajax Scroll -->

  <div hidden id="spinnerajaxscroll"></div>

<!-- pagination -->

<?php

  if ($this->Paginator->hasNext())
{

 ?>

  <div class="pagination">


    <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

</div>

<?php

}

?>

<!-- affichage d'un message une fois atteint le bas de page ou le chargement de tous les éléments -->

<div class="w3-center">

  <div class="no-more w3-btn w3-round w3-blue-grey disabled">Fin des résultats</div>

</div>

</div>
