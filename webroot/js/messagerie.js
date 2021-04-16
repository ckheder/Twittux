/**
 * messagerie.js
 *
 * Traitement des actions de la messagerie : liste des ocnversations, affichage d'une conversation, envoi de message
 *
 */

 /** variable **/

let form_message = document.querySelector('#form_message') // récupération du formulaire

let form_addtoconv = document.querySelector('#form_addtoconv'); // formulaire d'jout à une conversation

const textarea_message = document.querySelector('#textarea_message'); // textarea de rédaction d'un tweet

const menuemojimessage = document.getElementById("menuemojimessage"); //div contentant la liste des emojis

const listconv = document.getElementById("listconv"); // div contenant la liste des conversations

const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

const spaninputadduser = document.querySelector('#inputadduser'); // zone située dans le fenetre modale d'envoi d'invitation à rejojndre une conversation, remplie dynamiquement en javascript

// detetction mobile

    var hasTouchScreen = false;
    if ("maxTouchPoints" in navigator) {
        hasTouchScreen = navigator.maxTouchPoints > 0;
    } else if ("msMaxTouchPoints" in navigator) {
        hasTouchScreen = navigator.msMaxTouchPoints > 0;
    } else {
        var mQ = window.matchMedia && matchMedia("(pointer:coarse)");
        if (mQ && mQ.media === "(pointer:coarse)") {
            hasTouchScreen = !!mQ.matches;
        } else if ('orientation' in window) {
            hasTouchScreen = true; // deprecated, but good fallback
        } else {
            // Only as a last resort, fall back to user agent sniffing
            var UA = navigator.userAgent;
            hasTouchScreen = (
                /\b(BlackBerry|webOS|iPhone|IEMobile)\b/i.test(UA) ||
                /\b(Android|Windows Phone|iPad|iPod)\b/i.test(UA)
            );
        }
    }

// AFFICHER / MASQUER / CHARGER UNE CONVERSATION

//chargement de la liste des conversation

listconv.addEventListener("load", loadconv());

function loadconv() {

spinner.removeAttribute('hidden'); // affichage du spinner de chargement

  fetch('/twittux/messagerie/listconv', { // URL à charger dans la div précédente

              headers: {
                          'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                        }
            })

  .then(function (data) {
                          return data.text();
                        })
  .then(function (html) {

spinner.setAttribute('hidden', ''); // disparition du spinner

listconv.innerHTML = html; // chargement de la réponse dans la div précédente

//rejoindre depuis les notifications ou depuis le profil si conversation existante

var strContenu = localStorage.getItem("idconv");

if (strContenu !== null) {

  if(document.querySelector('div[data_idconv="'+strContenu+'"]')) // si la div contenant l'id de conversation existe
{

document.querySelector('div[data_idconv="'+strContenu+'"]').click();

document.querySelector('div[data_idconv="'+strContenu+'"]').className += " w3-grey";

}

  else // la conversation n'existe pas dans la liste
{

  alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de charger cette conversation, soit elle n\'existe pas soit vous n\'êtes pas autorisée à la voir.</p>'+
                    '</div>.');

}

    localStorage.removeItem("idconv");

}

// surlignage

// ajout d'un écouteur de clique sur chaque lien du menu

const navAnchor = listconv.querySelectorAll('.idconv'); // liste de tous les liens du menu pour permettre de surligner le lien actif

navAnchor.forEach(anchor => {
  anchor.addEventListener('click', addActive);
})

// on enlève la classe w3-red à l'item qui la possède pour la donner à l'élément cliqué

  function addActive(e)
{
  const current = listconv.querySelector('.w3-grey');

  if(current)
  {
    current.className = current.className.replace("w3-grey", "");
    e.target.className += " w3-grey";
  }
    else
  {
    e.target.className += " w3-grey";
  }

}

})

  // affichage d'erreur si besoin

  .catch(function(err) {
                         console.log(err);
  });

}

// afficher une conversation

  function loadConversation(idconv, visible, typeconv)
{

  	document.getElementById("displayconv").innerHTML = ""; // on vide la div d'affichage de la conversation

    // si la conversation est masquée

      if(visible == 'non')
    {

      // mise à jour du texte d'en-tête

      document.querySelector('.headmessage').textContent="Conversation désactivée";

      form_message.setAttribute('hidden', ''); // formulaire masqué

      btnoptionconv.setAttribute('hidden', ''); // disparition du bouton d'option d'une conversation

      // affichage d'un message d'information et d'un lien de réactivation

      document.getElementById("displayconv").innerHTML = '<div class="w3-container"><strong>Cette conversation est actuellement désactivée. Vous pouvez la réactivée en cliquant sur le bouton suivant : </strong><div class="w3-center"><br /><a href="#" class="w3-bar-item w3-button w3-round w3-teal editconv"  data-id_conv = '+idconv+' data_visible="oui" onclick="return false;"> Afficher cette conversation</a></div></div>';
    }
      else
    {

    form_message.removeAttribute('hidden'); // affichage du formulaires si il était masqué

    spinnerconv.removeAttribute('hidden'); // affichage du spinner de chargement

    // mise à jour du texte d'en-tête

    document.querySelector('.headmessage').textContent="Répondre";

    // on récupère tous les destinataires de la conversation cliquée

    var destinataire = document.querySelectorAll('div[data_idconv="'+idconv+'"] a');

    var destinataires = []; // tableau de tous les destinataires

    // stockage du ou des destinataires dans le tableau précédent

    destinataire.forEach(item => {

    result = item.getAttribute('data_username');

    destinataires.push(result);

  })

    fetch('/twittux/conversation-'+idconv+'', { // URL à charger dans la div précédente

                headers: {
                            'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                          }
              })

    .then(function (data) {
                            return data.text();
                          })
    .then(function (html) {

	   spinnerconv.setAttribute('hidden', ''); // disparition du spinner



     btnoptionconv.removeAttribute('hidden'); // affichage du bouton d'option dans une conversation

     // réactivation textarea_message puis autofocus

     textarea_message.disabled=false;

     textarea_message.focus();

     // mise à jour des informations du btnconv : identifiant pour masquer conversation / rejoindre une conversation

     document.querySelector('.editconv').dataset.id_conv = idconv;

     document.getElementById('joinconv').dataset.id_conv = idconv;

     // suppression du champ destinataire et du bouton submit si on vient de la page de la messagerie

      if(document.getElementById('user_message'))
     {
       document.getElementById('user_message').remove();
     }

     // si on est pas sur mobile et que le bouton de submit existe, on l'efface

     if (hasTouchScreen === false)
      if(form_message.querySelector('button[type="submit"]'))
          form_message.querySelector('button[type="submit"]').remove();


    // mise à jour du placeholder de la textarea

    textarea_message.placeholder=' Appuyez sur Entrée pour envoyer votre message...'

    // si l'input caché contenant l'id de conversation existe , on met à jour avec l'actuel sinon, si on vient de la messagerie, on le crée.

      if(form_message.querySelector('input[name="conversation"]'))
     {

       form_message.querySelector('input[name="conversation"]').value = idconv;

     }

    // si on vient de la messagerie, on crée l'input caché contenant l'id de conversation
      else
    {

      let inputconv = document.createElement("input");
       inputconv.type = "hidden";
       inputconv.name = "conversation";
       inputconv.value = idconv;
       form_message.appendChild(inputconv);

    }

// GESTION DESTINATAIRE

    // suppression des anciens destinataire si on change de conversation

      if(form_message.querySelector('input[class="destinataire"]'))
    {

      var formerdestinataire = form_message.querySelectorAll('input[class="destinataire"]');

      formerdestinataire.forEach(item => {

      item.remove();

   })

 }


 //ajout des nouveaux destinataire en prenant le tableau du début de fonction

  destinataires.forEach(function(item, array) {
   //console.log(item, index);
   let inputdestinataire = document.createElement("input");
  inputdestinataire.type = "hidden";
  inputdestinataire.name = "destinataire[]";
  inputdestinataire.value = item;
  inputdestinataire.className = 'destinataire';
  form_message.appendChild(inputdestinataire);
 });

// GESTION INVITER A REJOINDRE

 // on vérifie si il  n'y a pas d'id de conversation pour l'invitation, si oui on met à jour la valeur de l'id de conversation

 if(form_addtoconv.querySelector('input[name="conversation"]'))
{

 form_addtoconv.querySelector('input[name="conversation"]').value = idconv;

}

//sinon on crée cet id de conversatio caché
 else
{

   let inputconv = document.createElement("input");
  inputconv.type = "hidden";
  inputconv.name = "conversation";
  inputconv.value = idconv;
  form_addtoconv.appendChild(inputconv);

}

// envoi du type de conversation : CakePHP mettre à jour en base de donnée le type de conversation vers mutiple en cas de conversation duo et que le membre accepte de rejoindre

// mise à jour si il existe

  if(form_addtoconv.querySelector('input[name="typeconv"]'))
{

  form_addtoconv.querySelector('input[name="typeconv"]').value = typeconv;

}

// création si il n'existe pas

  else
{

  let inputconv = document.createElement("input");
 inputconv.type = "hidden";
 inputconv.name = "typeconv";
 inputconv.value = typeconv;
 form_addtoconv.appendChild(inputconv);

}

// affichage de la conversation

document.getElementById("displayconv").innerHTML = html;

})

    // affichage d'erreur si besoin

    .catch(function(err) {
  	                       console.log(err);
  	});
}
}

// menu dropdown de chaque conversations

function openconvoption() {

   document.querySelector('#convoption').classList.toggle("show");
}

//fermeture du menu dropdown si je clique dehors

window.onclick = function(event) {
  if (!event.target.matches('.btnconv')) {
    var dropdowns = document.getElementsByClassName("w3-dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}

// afficher/masquer une conversation

document.addEventListener('click',function(e){

    if(e.target && e.target.getAttribute('data_visible')) // si le lien cliqué possède l'attribut 'data_visible'
  {

    var action = e.target.getAttribute('data_visible'); // oui / non

    var idconv = e.target.getAttribute('data-id_conv'); // identifiant de la conversation

  var data = { idconv: idconv, action: action }; // tableau de données

    let response = fetch('/twittux/conversation/update', {
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(data)
    })
.then(function(response) {
    return response.text(); // récupération des données au format texte
  })
    .then(function(Data) {

  if(Data == 'pasupdate') // impossible de mettre à jour
{
          alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de mettre à jour le statut de cette conversation.</p>'+
                    '</div>.');
}
  else if(Data == 'updateok')
{

  loadconv(); // on recharge la liste des conversations

  // si je vient de masquer une conversation

  if(action == 'non')
{

// mise à jour du titre de la page

  document.querySelector('.headmessage').textContent="Conversation désactivée";

// désactivation textarea

form_message.setAttribute('hidden', ''); // formulaire masqué

//textarea_message.disabled=true;

btnoptionconv.setAttribute('hidden', ''); // disparition du bouton d'option d'une conversation

//affichage d'un message et d'un lien de réactivation

document.getElementById("displayconv").innerHTML = '<div class="w3-container"><strong>Cette conversation est actuellement désactivée. Vous pouvez la réactivée en cliquant sur le bouton suivant : </strong><div class="w3-center"><br /><a href="#" class="w3-bar-item w3-button w3-round w3-teal editconv"  data-id_conv = '+idconv+' data_visible="oui" onclick="return false;"> Afficher cette conversation</a></div></div>';

//notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Cette conversation est désormais inactive. Vous pouvez toujours recevoir des messages dans cette conversation mais elle sera masquée.</p>'+
                    '</div>.');

}

  else if (action == 'oui')
{



  // réaffichage du bouton d'option d'une conversation

  btnoptionconv.removeAttribute('hidden');

  // mise à jour du titre de la page

  document.querySelector('.headmessage').textContent="Répondre";

  loadConversation(idconv); // on charge la conversation précédemment masquée

  alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Conversation activée.</p>'+
                    '</div>.');

setTimeout(function(){ document.querySelector('div[data_idconv="'+idconv+'"]').className += " w3-grey"; }, 1000);



}
}
    }).catch(function(err) {

      console.log(err);

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Un problème technique empêche la mise à jour du statut de cette conversation.</p>'+
                    '</div>.');

    });
       }
})

// rejoindre une converation depuis un profil sans conversation existante

var usernameconv = localStorage.getItem("username");

  if (usernameconv !== null)
{

  // on pré-remplie la valeur de l'input de destinataire

  document.getElementById("user_message").setAttribute('value', usernameconv);

  // autofocus sur le textarea de rédaction d'un message

  textarea_message.focus();

  // suppression de l'item local

  localStorage.removeItem("username");

}


// FIN AFFICHER / MASQUER / CHARGER CONVERSATION

// ENVOI DE MESSAGE

//emojis

//ouverture menu des emojis

function openemojimenu() {

 if (menuemojimessage.className.indexOf("w3-show") == -1) {
        menuemojimessage.className += " w3-show";
    } else {
        menuemojimessage.className = menuemojimessage.className.replace(" w3-show", "");
    }
}

//ajout au textarea de l'emoji choisi

document.addEventListener('click',function(e){
  // récupération élément
  if(e.target && e.target.className == 'emoji'){
    var code = e.target.getAttribute('data_code');
    //suppression de l'extension du fichier
    code  = code.replace(/\.[^/.]+$/, "");
    code = ' :'+code+': ';
    //ajout au textarea
  textarea_message.value += code;
  menuemojimessage.className = menuemojimessage.className.replace(" w3-show", "");
  textarea_message.focus();
}
});

//envoi formulaire

  var submitForm = function(event)
{
  event.preventDefault();

  let data = new FormData(form_message) // on récupère les données du formulaire

  let response = fetch(form_message.getAttribute('action'), { // on récupère l'URL d'envoi des données
    method: 'POST',
    headers: {
                'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
              },
    body: data
  })
.then(function(response) {
  return response.json(); // récupération des données en JSON
})
  .then(function(jsonResult) {

      if(jsonResult.Result == 'userblock') // utilisateur bloqué, affichage d'un message indiquant que l'envoi de message est impossible
    {
      alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Cet utilisateur vous à bloqué, vous ne pouvez pas lui envoyer de message.</p>'+
                    '</div>.');
    }

      else if (jsonResult.Result == 'msgok')
    {

      //envoi message réussi depuis l'index

      alertbox.show('<div class="w3-panel w3-green">'+
                    '<p>Message envoyé !</p>'+
                    '</div>.');

      // réinitilisation du formulaire

      document.getElementById("user_message").setAttribute('value', '');

      form_message.reset();

      // on recharge la liste des conversation

      loadconv();

    }

    // echec de l'envoi d'un message

    else if (jsonResult.Result == 'msgnotok')
{

  alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Message non envoyé.</p>'+
                    '</div>.');
}

// envoi message dans une conversation : jsonResult.Result n'existe pas

  else
{
  // récupération de la div ou l'on va insérer le nouveau message

  var divmessage = document.getElementById("conv" +jsonResult.conversation);

  // insertion du nouveau message au tout début de la div

divmessage.insertAdjacentHTML('afterbegin', '<div style="word-wrap: break-word;margin-bottom : 15px;" class="w3-container w3-white"><br />'+
            '<span class="w3-opacity w3-right"><i class="far fa-clock"></i> à l\'instant</span>'+
            '<img src="/twittux/img/avatar/'+ jsonResult.user_message+'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/>'+
            '<p>'+ jsonResult.message+'</p><br />'+
            '</div>');

    // réinitilisation du formulaire

    form_message.reset();
}

  }).catch(function(err) {

// notification d'échec

      alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Impossible d\'envoyer ce message.</p>'+
                  '</div>.');

  });
}

// envoi message depuis une conversation avec appui sur la touche 'Entrée'

form_message.addEventListener('keyup', function (e) {

    if (e.keyCode === 13)
  {

    submitForm(e);

  }

    })

// envoi message depuis la page d'accueil de la messagerie

form_message.addEventListener('submit', submitForm, false);


// FIN ENVOI DE MESSAGE

//INVITER A REJOINDRE UNE CONVERSATION


  function openmodalinvitconv()
{

  // on vide le contenu des input du formulaire

  document.getElementById('inputadduser').innerHTML = '';

  // puis ouverture de la fenêtre modale

  document.getElementById('modalinvitconv').style.display='block';

  // calcul du nombre actuel de destinataire de la conversation

  var nbdest = document.getElementsByName("destinataire[]").length;

  // si ce nombre est inférieur à 5 (limite du nombre de participants à une conversation)

  if(nbdest < 5)
{
  var nbtoadd = 5 - nbdest; // calcul du nombre de personnes que l'on peut ajoputer à la conversation

  // mise à jour du message d'information dans la modale

  document.getElementById('inputadduser').innerHTML = 'Vous pouvez inviter '+nbtoadd+' personne(s) à rejoindre cette conversation.';

  // création et ajout du nombre d'input en conséquence

    for (let i = nbdest; i < 5; i++)
  {
    var input = document.createElement('input');

    input.placeholder = "Utilisateur à ajouter";

    input.name = "userinvit[]";

    input.type = "text";

    input.className = "input-inv";

    spaninputadduser.append(input);

  }

}
}

// fermeture de la modale d'ajout à une conversation : on vide d'abord la span contenant les input précédent

  function closemodaleaddconv()
{
  document.getElementById('inputadduser').innerHTML = '';

  document.getElementById('modalinvitconv').style.display='none';

}

// traitement inviter à rejoindre

form_addtoconv.addEventListener('submit', async function (e) {

      e.preventDefault();

      //on récupère tous les input envoyés et on supprime les vides

      var allInputs = form_addtoconv.getElementsByTagName('input');

        for (var i = 0; i < allInputs.length; i++)
       {
             var input = allInputs[i];

             if (input.name && !input.value) {
             input.name = '';
         }
       }

      let data = new FormData(this) // on récupère les données du formulaire

      let response = fetch(form_addtoconv.getAttribute('action'), { // on récupère l'URL d'envoi des données
        method: 'POST',
        headers: {
                   'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  },
        body: data
      })
  .then(function(response) {
      return response.json(); // récupération des données en JSON
    })
      .then(function(Data) {

  // fermeture de la fenêtre modale

  closemodaleaddconv();

  // si au moins 1 invitation à était envoyée (CakePHP vérifiera si le ou les personnes ne sont pas déjà dans la conversation)

  if(Data.Result == 'invitok')
{
  alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Invitation(s) envoyée(s)</p>'+
                    '</div>.');
}

// si aucune invitation n'a était envoyée

else if (Data.Result == 'noinvit')

{

  alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Invitation(s) non envoyée(s) : soit la personne n\'existe pas soit elle est déjà dans la conversation.</p>'+
                    '</div>.');

}

      }).catch(function(err) {

  // notification d'échec

          alertbox.show('<div class="w3-panel w3-red">'+
                       '<p>Un problème technique empêche l\'invitation de nouvelles personnes à cette conversation.</p>'+
                     '</div>.');

      });

    })


// FIN INVITER A REJOINDRE UNE CONVERSATION


// NOTIFICATIONS

var AlertBox = function(id, option) {
 this.show = function(msg) {

     var alertArea = document.querySelector(id);
     var alertBox = document.createElement('DIV');
     var alertContent = document.createElement('DIV');
     var alertClose = document.createElement('A');
     var alertClass = this;
     alertContent.classList.add('alert-content');
     alertContent.innerHTML = msg;
     alertClose.classList.add('alert-close');
     alertClose.setAttribute('href', '#');
     alertBox.classList.add('alert-box');
     alertBox.appendChild(alertContent);
     if (!option.hideCloseButton || typeof option.hideCloseButton === 'undefined') {
       alertBox.appendChild(alertClose);
     }
     alertArea.appendChild(alertBox);
     alertClose.addEventListener('click', function(event) {
       event.preventDefault();
       alertClass.hide(alertBox);
     });
     if (!option.persistent) {
       var alertTimeout = setTimeout(function() {
         alertClass.hide(alertBox);
         clearTimeout(alertTimeout);
       }, option.closeTime);
     }

 };

 this.hide = function(alertBox) {
   alertBox.classList.add('hide');
   var disperseTimeout = setTimeout(function() {
     alertBox.parentNode.removeChild(alertBox);
     clearTimeout(disperseTimeout);
   }, 500);
 };
};

var alertbox = new AlertBox('#alert-area', {
 closeTime: 5000,
 persistent: false,
 hideCloseButton: false
});

// FIN NOTIFICATIONS
