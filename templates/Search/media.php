<!--

 * media.php
 *
 * Mise en page des résultats d'une recherche classique sur les tweets contenant un média
 *
 */ -->

 <?php

  use Cake\Utility\Text; // utilitaire de manipulation d'une chaîne de caractère


          if(count($resultat_tweet_media) === 0) // aucun résultat au comptage des résultats dans le tableau
        {

          echo '<div class="w3-container w3-blue">Aucun tweet ne correspond à cette recherche.</div>';

        }
          else
        {

          foreach ($resultat_tweet_media as $resultat_tweet_media): ?>

            <div style="word-wrap: break-word;" class="w3-container w3-card w3-white">

              <br>

        <!--avatar -->

            <?=  $this->Html->image('/img/avatar/'.$resultat_tweet_media->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>


        <!--menu déroulant : signaler un post (visible uniquement si je ne suis pas le résultat de recherche) -->

        <?php

        if($resultat_tweet_media->username != $authName)
      {

        ?>

    <div class="dropdown">

      <button onclick="openmenutweet(<?= $resultat_tweet_media->id_tweet ?>)" class="dropbtn">...</button>

        <div id="btntweet<?= $resultat_tweet_media->id_tweet ?>" class="dropdown-content">

            <a href="#">Signaler ce post </a>

        </div>

    </div>

    <?php

      }

    ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($resultat_tweet_media->username).'','/'.h($resultat_tweet_media->user_tweet).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $resultat_tweet_media->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p> <!-- mise en surbrillance du mot clé de recherche dans les tweets -->

          <?= Text::highlight(
                                $resultat_tweet_media->contenu_tweet,
                                $this->request->getParam('query'),
                                ['format' => '<b>\1</b>']
                              );

          ?>

        </p>

        <hr class="w3-clear">

        <!-- zone d'affichage du nombre de like, commentaire et de partage -->

<span class="w3-opacity">

        <!-- affichage du nombre de like -->

  <a onclick="openmodallike(<?= $resultat_tweet_media->id_tweet ?>)" style="cursor: pointer;"><span class="nb_like_<?= $resultat_tweet_media->id_tweet ?>"><?= $resultat_tweet_media->nb_like ;?></span> J'aime</a>

        <!-- affichage du nombre de commentaire -->

  - <?= $resultat_tweet_media->nb_commentaire;?> Commentaire(s)

        <!-- affichage du nombre de partage -->

  - Partagé <span class="nb_share_<?= $resultat_tweet_media->id_tweet ?>"><?= $resultat_tweet_media->nb_partage ;?></span> fois</span>

<hr>

<!--boutons like, commentaire et partage -->

<p>

        <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="like" data_id_tweet="<?= $resultat_tweet_media->id_tweet ?>"><i class="fa fa-thumbs-up"></i> J'aime</a>
        &nbsp;
        <a href="/twittux/statut/<?= $resultat_tweet_media->id_tweet ;?>" class="w3-margin-bottom"><i class="fa fa-comment"></i> Commenter</a>

        <?php

              if($resultat_tweet_media->username != $authName) // si je ne suis pas l'auteut du tweet, on affiche le lien de partage
            {
              ?>
        &nbsp;
                <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="share" data_auttweet = "<?= $resultat_tweet_media->username ?>" data_id_tweet="<?= $resultat_tweet_media->id_tweet ?>"><i class="fas fa-retweet"></i> Partager</a>
        <?php
            }
      ?>

      </p>

</div>

            <?php endforeach; ?>

            <!--lien pagination -->

            <div id="pagination">

              <?= $this->Paginator->numbers() ?>

                        <?= $this->Paginator->options([

                                          'url' => array('controller' => '/search/'.$this->request->getParam('query').'')

                                        ]);?>

            <?= $this->Paginator->next('Next page'); ?>

            <?= $this->Paginator->counter() ?>

            </div>

 <?php

        }

  ?>