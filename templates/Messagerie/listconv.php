<!--

 * listconv.php
 *
 * Affichage des conversations
 *
 */ -->

<?php

use Cake\I18n\Time;

  if(count($conv) === 0) // si il n'y as pas de conversation
{
  echo '<br  /><div class="w3-container w3-blue w3-padding w3-round">Aucune conversation en cours.</div>';
}
  else
{

 foreach ($conv as $conv): ?>

 <!-- si la conversation est masquée, on applique le css w3-brown -->

 <!-- paramètres de la fonction onclick : id de conversation, visible ou non et le type de conversation -->

    <div class="idconv <?= ($conv['visible'] == 'non') ? "w3-brown" : ''; ?>"  onclick="loadConversation(<?= $conv['conversation'] ;?>, '<?= $conv['visible'] ;?>', '<?= $conv['type_conv'] ;?>')" data_idconv="<?= $conv['conversation'] ;?>">

<?php

// affichage du message tronqué, grâce au helper Text

       $lastmessage = $this->Text->truncate($conv['message'], 110,
                                                                  [
                                                                    'ellipsis' => '...',
                                                                    'exact' => false,
                                                                    'html' => true
                                                                  ]
                                            );


  $lastmessage = str_replace('<br />', '', $lastmessage);

  ?>

  <?php

  // utilisation de la class Time pour calculer la différence entre maintenant et le moment ou le dernier message à été posté

  $date_conv = new Time($conv['created']);

  $date_conv =  $date_conv->timeAgoInWords([

                                            'end' => '+1 year'
                              ]); ?>

<!-- affichage de la cell contenant les participants à la conversation -->

<?= $this->cell('Conversation::usersconv', ['conv' => $conv['conversation'], 'authname' => $authName]) ; ?>

  <br  />

<!--affichage du nom de celui à posté le dernier message puis le message -->

  &nbsp;&nbsp;<span class="w3-opacity w3-margin-top"><?= ($conv['visible'] == 'non') ? "<i class=\"fas fa-comment-slash\"></i>" : "<i class=\"far fa-comment-dots\"></i>" ;?>&nbsp;<?= ($conv['visible'] == 'non') ? "<i>Conversation désactivée</i>" : (($conv['user_message'] == $authName) ? "<strong>Vous</strong>" : $conv['user_message']);
  echo ' : '.$lastmessage.' - '.$date_conv.''; ?>

&nbsp;</span>

<br  />



            </div>

          <?php endforeach; }?>
