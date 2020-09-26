<!--

 * hashtag.php
 *
 * Mise en page des résultats d'une recherche avec hashtag sur les tweets
 *
 */ -->
 
 <?php

 use Cake\Utility\Text; // utilitaire de manipulation d'une chaîne de caractère


          if(count($resultat_tweet) === 0) // aucun résultat au comptage des résultats dans le tableau
        {

         echo '<div class="w3-container w3-blue">Aucun tweet ne correspond à cette recherche.</div>';

        }

          else
        {

          foreach ($resultat_tweet as $resultat_tweet): ?>

            <div style="word-wrap: break-word;" class="w3-container w3-card w3-white" id="tweet<?= $resultat_tweet->id_tweet ;?>">

              <br>

        <!--avatar -->

        <?=  $this->Html->image('/img/avatar/'.$resultat_tweet->user_tweet.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

        <!--menu déroulant : suppression d'un abonnement / signaler un post -->

        <div class="dropdown">

          <button onclick="openmenutweet(<?= $resultat_tweet->id_tweet ?>)" class="dropbtn">...</button>

            <div id="btntweet<?= $resultat_tweet->id_tweet ?>" class="dropdown-content">

              <a href="#">Signaler ce post </a>

            </div>

        </div>

        <!--nom d'utilisateur -->

        <h4><?= $resultat_tweet->user_tweet ;?></h4>

        <!--date formatée -->

        <span class="w3-opacity"><?= $resultat_tweet->created->i18nformat('dd MMMM YYYY - HH:mm');?></span>

        <hr class="w3-clear">

        <!--corps du tweet -->

        <p><!-- mise en surbrillance du hashtag de recherche dans les tweets -->

          <?= Text::highlight(
                              $resultat_tweet->contenu_tweet,
                              $this->request->getParam('query'),
                              ['format' => '<b>\1</b>']
                              );
          ?>
  
        </p>

        <!--boutons like et commentaire -->

        <button type="button" class="w3-button w3-blue-grey w3-margin-bottom"><i class="fa fa-thumbs-up"></i> <?= $resultat_tweet->nb_like ;?>  Like</button> 

        <a href="./statut/<?= $resultat_tweet->id_tweet ;?>" class="w3-btn w3-grey w3-margin-bottom"><i class="fa fa-comment"></i> <?= $resultat_tweet->nb_commentaire;?>  commentaire(s)</a>
         
</div>

            <?php endforeach; ?>

            <!--lien pagination -->

            <div id="pagination">

              <?= $this->Paginator->options([

                                          'url' => array('controller' => '/search/hashtag/'.$this->request->getParam('query').'')

                                        ]);?>

            <?= $this->Paginator->next('Next page'); ?>

            </div>

 <?php

        } ?>
