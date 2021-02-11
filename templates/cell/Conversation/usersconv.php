<!--

 * userconv.php
 *
 * Affichage des membres d'une conversation (sans l'utilisateur courant)
 *
 */ -->

<?php

	foreach ($usersconv as $usersconv): ?>

     		<!--avatar -->

     		<?=  $this->Html->image('/img/avatar/'.$usersconv->user_conv.'.jpg', array('alt' => 'image utilisateur', 'class'=>'w3-circle w3-margin-left w3-margin-top', 'width'=>60)); ?>

     		<!--lien vers profil -->

				<strong>

					<?= $this->Html->link(''.h($usersconv->user_conv).'','/'.h($usersconv->user_conv).'',['class'=>"w3-text-blue",'data_username' => $usersconv->user_conv, 'title' =>'Voir le profil de '.$usersconv->user_conv.'']) ?>

				</strong>

<?php

	endforeach ;

?>
