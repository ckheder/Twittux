<!--

 * display.php
 *
 * Affichage des hashtag les plus populaires
 *
 */ -->

<?php

			foreach ($list_hashtag as $list_hashtag):?>

  <p>
				<strong>

				<a href="/twittux/search/hashtag/%23<?= $list_hashtag->hashtag ?>" class="w3-text-blue">#<?= $list_hashtag->hashtag ?></a>

				</strong>

        <br />

        <span class="w3-opacity"><?= $list_hashtag->nb_post_hashtag ?> Tweets</span>

  </p>

<?php

			endforeach;

?>

<!-- lien pour accéder à la page trending contenant tous les hashtags -->

<div class="w3-panel w3-light-grey w3-center">

	<a href="/twittux/trending">Voir plus</a>

</div>
