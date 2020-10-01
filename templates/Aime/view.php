<!--
* view.php
*
* affichage des personnes aimant un post (chargé via modale)
-->

            <?php foreach ($username_like as $username_like): ?>

                <div class="w3-container w3-round w3-margin">
            
                  <!-- avatar -->

                    <?= $this->Html->image('/img/avatar/'.$username_like->username.'.jpg', array('alt' => 'image utilisateur', 'class'=>' w3-circle', 'style'=>'width:50px', 'title' => ''.h($username_like->username).'')) ?>

                  <!-- lien profil -->

                   <strong><?= $this->Html->link(''.h($username_like->username).'','/'.h($username_like->username).'') ?></strong>
                    
                </div>

            <?php endforeach; ?>

            <!-- lien temporaire de test de la pagination -->

                <div id="pagination">

                  <?= $this->Paginator->numbers() ?>

                  <?= $this->Paginator->next('Next page'); ?>

                  <?= $this->Paginator->counter() ?>

                </div>