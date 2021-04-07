<!--

 * index.php
 *
 * Affichage de tous les hashtags paginés par 10
 *
 */ -->

<div class="w3-panel w3-border w3-light-grey">

  <h4 class="w3-center"><i class="fas fa-globe"></i> Tendances</h4>

    <?php foreach ($hashtags as $hashtags): ?>

        <p>
            <strong>

              <!-- nom du hashtag -->

                		<a href="/twittux/search/hashtag/%23<?= $hashtags->hashtag ?>" class="w3-text-blue">#<?= $hashtags->hashtag ?></a>

            </strong>

                      <br />

              <!-- nombre de post contenant le hashtag -->

                        <span class="w3-opacity"><?= $hashtags->nb_post_hashtag ?> Tweets</span>

        </p>

                <?php endforeach; ?>

              <!--lien pagination -->

                <div id="pagination">

                  <?= $this->Paginator->numbers() ?>

                  <?= $this->Paginator->next('Next page'); ?>

                  <?= $this->Paginator->counter() ?>

                </div>

</div>
