<!--
* view.php
*
* affichage des personnes aimant un post (chargé via modale)
-->

            <?php foreach ($username_like as $username_like): ?>

                <div class="w3-container w3-round w3-margin itemlike">

                  <!-- avatar -->

                    <?= $this->Html->image('/img/avatar/'.$username_like->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>' w3-circle', 'style'=>'width:50px', 'title' => ''.h($username_like->username).'')) ?>

                  <!-- lien profil -->

                   <strong>

                     <?= $this->Html->link(''.h($username_like->username).'','/'.h($username_like->username).'') ?>

                   </strong>

                </div>

            <?php endforeach; ?>

            <!--lien pagination -->

            <!-- spinner de chargement des données par Infinite Ajax Scroll -->

            <div hidden id="spinnerajaxscroll"></div>

            <?php

                if ($this->Paginator->hasNext())
              {

            ?>

                <div class="paginationlike">

                  <?= $this->Paginator->next('Next page'); ?> <!-- lien vers la ou les seconde(s) page(s) -->

                </div>

            <?php

            }

            ?>
