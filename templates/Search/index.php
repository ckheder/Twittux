<!--

 * index.php
 *
 * Mise en page des résultats d'une recherche classique sur les tweets
 *
 */ -->
 
 <?php

  use Cake\Utility\Text; // utilitaire de manipulation d'une chaîne de caractère


          if(count($query_tweet) === 0) // aucun résultat au comptage des résultats dans le tableau
        {

          echo '<div class="w3-container w3-blue">Aucun tweet ne correspond à cette recherche.</div>';

        }
          else
        {

          foreach ($query_tweet as $query_tweet): ?>

            <div style="word-wrap: break-word;" class="w3-container w3-card w3-white">

              <br>

        <!--avatar -->

            <?=  $this->Html->image('/img/avatar/'.$query_tweet->user_tweet.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

                        
        <!--menu déroulant : signaler un post (visible uniquement si je ne suis pas le résultat de recherche) -->

        <?php

        if($query_tweet->user_tweet != $authName)
      {

        ?>

    <div class="dropdown">

      <button onclick="openmenutweet(<?= $query_tweet->id_tweet ?>)" class="dropbtn">...</button>

        <div id="btntweet<?= $query_tweet->id_tweet ?>" class="dropdown-content">

            <a href="#">Signaler ce post </a>  

        </div>

    </div>

    <?php

      }

    ?>

        <!--nom d'utilisateur -->

        <h4><?= $this->Html->link(''.h($query_tweet->user_tweet).'','/'.h($query_tweet->user_tweet).'') ?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $query_tweet->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p> <!-- mise en surbrillance du mot clé de recherche dans les tweets -->

          <?= Text::highlight(
                                $query_tweet->contenu_tweet,
                                $this->request->getParam('query'),
                                ['format' => '<b>\1</b>']
                              );

          ?>
          
        </p>

        <hr class="w3-clear">

        <!-- zone d'affichage du nombre de like, commentaire et de partage -->

<span class="w3-opacity">

        <!-- affichage du nombre de like --> 

  <a onclick="openmodallike(<?= $query_tweet->id_tweet ?>)" style="cursor: pointer;"><span class="nb_like_<?= $query_tweet->id_tweet ?>"><?= $query_tweet->nb_like ;?></span> J'aime</a>

        <!-- affichage du nombre de commentaire -->

  - <?= $query_tweet->nb_commentaire;?> Commentaire(s) 

        <!-- affichage du nombre de partage -->

  - Partagé <span class="nb_share_<?= $query_tweet->id_tweet ?>"><?= $query_tweet->nb_partage ;?></span> fois</span>

<hr>

<!--boutons like, commentaire et partage -->

<p>

        <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="like" data_id_tweet="<?= $query_tweet->id_tweet ?>"><i class="fa fa-thumbs-up"></i> J'aime</a> 
        &nbsp;
        <a href="./statut/<?= $query_tweet->id_tweet ;?>" class="w3-margin-bottom"><i class="fa fa-comment"></i> Commenter</a>

        <?php 

              if($query_tweet->user_tweet != $authName) // si je ne suis pas l'auteut du tweet, on affiche le lien de partage
            {
              ?>
        &nbsp;
                <a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="share" data_id_tweet="<?= $query_tweet->id_tweet ?>"><i class="fas fa-retweet"></i> Partager</a> 
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

