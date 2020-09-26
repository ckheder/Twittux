<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Commentaire $commentaire
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Commentaires'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="commentaires form content">
            <?= $this->Form->create($commentaire) ?>
            <fieldset>
                <legend><?= __('Add Commentaire') ?></legend>
                <?php
                    echo $this->Form->control('commentaire');
                    echo $this->Form->control('id_tweet');
                    echo $this->Form->control('username');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
