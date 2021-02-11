<!--

 * view.php
 *
 * Visualisation d'une conversation
 *
 */ -->

<?php

use Cake\I18n\Time;

?>

<div id="conv<?= $this->request->getParam('idconv');?>">

<!-- test si on peut voir une conversation -->

<?php

  if(isset($no_see)) // conversation dont je ne fais pas partie
{
?>
  <div class="w3-container">

    <div class="w3-panel w3-red">

      <p>Cette conversation est privée.</p>

    </div>

    <!-- bouton de retour -->

      <div class="w3-center">

        <button onclick="goBack()">Retour</button>

        <script>

          function goBack()
        {
          window.history.back();
        }

        </script>

      </div>

  </div>

<?php

}

  else
{

 foreach ($message as $message): ?>

    <div style="word-wrap: break-word;margin-bottom : 15px;" class="w3-container w3-white">

  <br />

  <!-- date du message -->

  <span class="w3-opacity w3-right"><i class="far fa-clock"></i>

    <?php

    $date_msg = new Time($message->created);

    $date_msg =  $date_msg->timeAgoInWords(['format' => 'd MMMM YYY','end' => '+1 year']);

    echo $date_msg;

      ?>

  </span>

<!--avatar -->

<?=  $this->Html->image('/img/avatar/'.$message->user_message.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-left w3-circle w3-margin-right', 'width'=>60)); ?>

<!-- corps du message -->

<p>

  <?= $message->message ;?>

</p>

<br />

</div>

<?php

endforeach;

?>

<!-- pagination -->

<div id="pagination">

  <?= $this->Paginator->numbers() ?>

  <?= $this->Paginator->next('Next page'); ?>

  <?= $this->Paginator->counter() ?>

</div>

</div>

<?php

}

 ?>
