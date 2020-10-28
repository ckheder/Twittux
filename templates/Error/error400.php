<?php
/**
 * @var \App\View\AppView $this
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';
$this->set('title', 'Erreur : page non trouvée');

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', 'pas trouvé');
    $this->assign('templateName', 'error400.php');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?= $this->element('auto_table_warning') ?>
<?php

$this->end();
endif;
?>

<h2><i class="fas fa-unlink"></i> Page non trouvée</h2>

  <p>  <?= __d('cake', 'L\'adresse  {0} est peut être non valide ou la page a peut-être été supprimée. Vérifiez si le lien que vous essayez d’ouvrir est correct.', "<strong>'{$url}'</strong>") ?>
