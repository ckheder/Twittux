/**
 * messagerie.js
 *
 * Traitement des actions de la messagerie : liste des ocnversations, affichage d'une conversation, envoi de message
 *
 */

 /** variable **/

let iasmessage; // variable contenant la construction de l'Infinite Ajax Scroll

let form_message = document.querySelector('#form_message') // récupération du formulaire

let form_addtoconv = document.querySelector('#form_addtoconv'); // formulaire d'jout à une conversation

const textarea_message = document.querySelector('#textarea_message'); // textarea de rédaction d'un tweet

const menuemojimessage = document.getElementById("menuemojimessage"); //div contentant la liste des emojis

const listconv = document.getElementById("listconv"); // div contenant la liste des conversations

const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

const spaninputadduser = document.querySelector('#inputadduser'); // zone située dans le fenetre modale d'envoi d'invitation à rejojndre une conversation, remplie dynamiquement en javascript

var gettypeconv; // duo/multiple utilisé lors de la mise à jour de la fonction onclick suite à une désactivation/activation de conversation

var usersinmyconvs = []; // stock tous les utilisateurs faisant parti de mes conversations actives


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

    // mise à jour des pastilles de connexion/déconnexion

      function updatestatut(data, statut)
    {
      // mise à jour pastilles du rouge au vert

          let updatestatus = document.querySelectorAll('.'+data+'');

            if(statut === 'online')
          {
              updatestatus.forEach(function(logo)
            {

              logo.className = logo.className.replace("red", ""); // suppression du rouge sur la pastille

              logo.className += " green"; // affectation du vert

              logo.setAttribute('title', 'connecté(e)'); // mise à jour du titre de la pastille

            })

          }
            else if (statut === 'offline')
          {

            // mise à jour pastilles du vert au rouge

                updatestatus.forEach(function(logo)
              {

                logo.className = logo.className.replace("green", "");

                logo.className += " red";

                logo.setAttribute('title', 'déconnecté(e)');

              });
          }
    }

    // mettre à jour la liste des conversations en cas de nouveaux messages envoyés depuis l'index ou depuis une conversation

    function updatelistconv(data)
  {

    var spanlastmessage = document.querySelector('.idconv[data_idconv="'+data.message['conversation']+'"] span[class="lastmessage"]'); // span contenant le dernier message de la conversation

    var auteurlastmessage = document.querySelector('.idconv[data_idconv="'+data.message['conversation']+'"] span[class="auteurmessager"]'); // span contenant l'auteur du dernier message

    spanlastmessage.innerHTML=' : '+data.message['message']+' - 1 seconde'; //mise à jour du dernier message de la conversation

      if(data.user_message === authname) // si je suis l'auteur du dernier message : affichage 'Vous' pour moi
    {
      auteurlastmessage.innerHTML='<strong>Vous</strong>';
    }
      else
    {
      auteurlastmessage.innerHTML=''+data.message['user_message']+''; // sinon affichage de l'auteur pour moi
    }

      document.querySelector('.idconv[data_idconv="'+data.message['conversation']+'"]').setAttribute("style", "font-weight: bolder;"); // mise en couleur CSS pour signifier un nouveau message

      document.querySelector('#listconv').prepend(document.querySelector('.idconv[data_idconv="'+data.message['conversation']+'"]')); // la conversation monte en haut pour signlaer un message récent postée
  }

// gestion de l'affichage d'un message envoyé depuis l'index  : affichage si je suis sur l'index ou dans une conversation

    function addmessage(data)
  {

    var auteurmessage; // va déterminer si on affiche 'Vous' pour l'expediteur ou le nom de l'auteur du message

    var avatarauteurmessage; // va déterminer quel sera l'avatar de l'auteur du dernier message

    var etatconnexion; // va déterminer si la personne à qui j'envoi le message est connecté ou pas

      if(!document.querySelector('div[data_idconv="'+data.message['conversation']+'"]')) // conversation inexistante à gauche
    {

      // suppression de la div bleue "Aucune conversation en cours" si elle existe

        if(document.querySelector('.noconv'))
      {
        document.querySelector('.noconv').parentNode.removeChild(document.querySelector('.noconv'));
      }

      // affichage des informations suivant le cas

    auteurmessage = (data.message['user_message'] === authname) ? "Vous" : data.message['user_message']; // si je suis l'auteur du message -> 'Vous' sinon l'expediteur

    avatarauteurmessage = (data.message['user_message'] === authname) ? data.message['destinataire'] : data.message['user_message']; // mon avatar si je suis l'auteur sinon celui de l'expediteur

    etatconnexion = (data.message['user_message'] === authname) ? data.message['etatconnexion'] : "green"; // verte si mon destinataire est connecté rouge si il ne l'ais pas

      // affichage du message envoyé

    listconv.insertAdjacentHTML('afterbegin','<div class="idconv w3-padding-16" style="font-weight: bolder" onclick="loadConversation(\''+data.message['conversation']+'\', \'oui\', \'duo\')" data_idconv="'+data.message['conversation']+'">'+
                                '<span class="userconvsin'+data.message['conversation']+'">'+
                                '<img src="/twittux/img/avatar/'+avatarauteurmessage+'.jpg" alt="image utilisateur" class="w3-circle w3-margin-left" width="60">&nbsp;'+
                                '<a href="/twittux/'+avatarauteurmessage+'" class="w3-text-blue" data_username="'+avatarauteurmessage+'" title="Voir le profil de '+avatarauteurmessage+'">'+avatarauteurmessage+'</a> <i title="connecté(e)" class="'+avatarauteurmessage+' fas fa-circle '+etatconnexion+'"></i>'+
                                '</span><br />'+
                                '&nbsp;&nbsp;<span class="w3-opacity w3-margin-top"><i class="far fa-comment-dots"></i>&nbsp;<span class="auteurmessager">'+auteurmessage+' </span>: <span class="lastmessage">'+data.message['message']+' -  à l\'instant</span>&nbsp;</span>'+
                                '<br />'+
                                '</div>');

    // test si je suis connecté à la conversation et connexion si ce n'est pas le cas

          socket.emit('checkconnconv', data.message['conversation']);

    // ajout de mon destinataire (si il n'existe pas) dans le tableau des utilisateurs avec qui j'ai une conversation

            if(data.message['user_message'] === authname)
          {
              if(usersinmyconvs.indexOf(data.message['destinataire']) === -1)
            {
              usersinmyconvs.push(data.message['destinataire']);
            }
          }

    // ajout pour mon destinataire (si je n'existe pas) dans le tableau des utilisateurs avec qui il a une conversation
            else
          {
              if(usersinmyconvs.indexOf(data.message['user_message']) === -1)
            {
              usersinmyconvs.push(data.message['user_message']);
            }
          }

        }
          else
        {

          // conversation existante -> mise à jour à gauche

          updatelistconv(data);

          // affichage du message dans la conversation

            if(document.getElementById("conv" + data.message['conversation']))
          {

              if(typeof data.message['expediteur'] !== "undefined")
            {
              avatarauteurmessage = data.message['expediteur'];
            }
            else
            {
              avatarauteurmessage = data.message['user_message'];
            }

              document.getElementById("conv" + data.message['conversation']).insertAdjacentHTML('afterbegin', '<div style="word-wrap: break-word;margin-bottom : 15px;" class="w3-container w3-white"><br />'+
                                                                                      '<span class="w3-opacity w3-right"><i class="far fa-clock"></i> à l\'instant</span>'+
                                                                                      '<img src="/twittux/img/avatar/'+ avatarauteurmessage+'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/>'+
                                                                                      '<p>'+ data.message['message']+'</p><br />'+
                                                                                      '</div>');

              // réinitilisation du formulaire d'envoi de message

              form_message.reset();
          }

        }
}

    // réception d'un message : conversation / index

      socket.on('newmessage', function(data)
    {

      addmessage(data);

    });

    // arrivée d'un nouvel utilisateur dans une conversation

      socket.on('newuserconv', function (data)
    {

        if(usersinmyconvs.indexOf(data.authname) === -1) // si cet utilisateur n'est pas dans ma liste de destinataire, on l'ajoute
      {
        usersinmyconvs.push(data.authname);
      }

      // si je suis dans une conversation

        if(document.getElementById("conv" + data.idconv))
      {
        // affichage d'un message indiquant qu'un utilisateur à rejoint la conversation
        document.getElementById("conv" + data.idconv).insertAdjacentHTML('afterbegin', '<div style="word-wrap: break-word;margin-bottom : 15px;" class="w3-container w3-cyan"><br />'+
                                                                        '<span class="w3-opacity w3-right"><i class="far fa-clock"></i> à l\'instant</span>'+
                                                                        '<img src="/twittux/img/avatar/'+ data.authname+'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/>'+
                                                                        '<p>'+ data.authname+' à rejoint la conversation.</p><br />'+
                                                                        '</div>');

        // ajout de l'utilisateur dans la liste en haut

        // test si il existe déjà ou pas ( cas du clique plusieurs fois sur la notification)

                  if(document.querySelector('.headmessage').querySelector('a[data_username="'+data.authname+'"]') === null)
                {

                // ajout du nom en haut + pastille de connexion

                  document.querySelector('.headmessage').insertAdjacentHTML('afterbegin', '<img src="/twittux/img/avatar/'+ data.authname+'.jpg" alt="image utilisateur" class="w3-circle w3-margin-left w3-margin-top" width="60"/> '+
                  '<a href="/twittux/'+ data.authname+'" class="w3-text-blue" data_username="'+ data.authname+'" title="Voir le profil de '+ data.authname+'">'+ data.authname+'</a> <i title = "connecté(e)" class="'+data.authname+' fas fa-circle"></i>');
                }
      }

    // ajout dans la liste de gauche de l'utilisateur à la liste des participants à cette conversation

    document.querySelector('.userconvsin'+data.idconv).insertAdjacentHTML('afterbegin', '<p><img src="/twittux/img/avatar/'+ data.authname+'.jpg" alt="image utilisateur" class="w3-circle w3-margin-left" width="60"/> '+
    '<a href="/twittux/'+ data.authname+'" class="w3-text-blue" data_username="'+ data.authname+'" title="Voir le profil de '+ data.authname+'">'+ data.authname+'</a> <i title = "connecté(e)" class="'+data.authname+' fas fa-circle"></i></p>');

  });

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

    if (strContenu !== null)
  {

    if(document.querySelector('div[data_idconv="'+strContenu+'"]')) // si la div contenant l'id de conversation existe
  {

    socket.emit('joinbyinv', {authname: authname, idconv: strContenu}); // émission d'un évènement pour les utilisateurs dans la conversation

    // chargement de la conversation

    window.setTimeout(function()

                                { document.querySelector('div[data_idconv="'+strContenu+'"]').click();

                                }, 300);

  }

  else // la conversation n'existe pas dans la liste
{

  alertbox.show('<div class="w3-panel w3-red">'+
                '<p>Impossible de charger cette conversation, soit elle n\'existe pas soit vous n\'êtes pas autorisée à la voir.</p>'+
                '</div>.');

}

    localStorage.removeItem("idconv");

}

// stockage de tous les utilisateurs avec qui j'ai une conversation

var getusersinmyconvs = listconv.querySelectorAll('a');

  getusersinmyconvs.forEach(item => {

    result = item.getAttribute('data_username'); // on récupère le data_username de chaque item

      if(usersinmyconvs.indexOf(result) === -1) // si il n'existe pas , on l'ajoute dans le tableau
    {
      usersinmyconvs.push(result);
    }

  });

// récupération de toutes les conversations actives

var room = listconv.querySelectorAll('div.idconv:not(.w3-brown)'); // w3-brown signifie conversation masquée donc on ne la rejoint pas

var rooms = []; // tableau de toutes mes conversations

// stockage du ou des destinataires dans le tableau précédent

room.forEach(item => {

  result = item.getAttribute('data_idconv');

  rooms.push(result);

})


// émission d'un évènement de connexion

socket.emit("connexion", {rooms: rooms, authname: authname, usersinmyconvs: usersinmyconvs}); // on transmet mon username et toutes les conversationc précédemment stockés au serveur

// on test si mon ou mes destinataires avec qui j'ai une conversation sont connectés

  socket.on('testusers',function(users) // users contient tous les utilisateurs connectés
{

  usersinmyconvs.forEach(function(elem) // pour chacun de mes contacts
{

    if(users.find(o => o == elem)) // connecté
  {

      document.querySelectorAll('a[data_username="'+elem+'"]').forEach(function(element) // affecte une pastille verte à côté du nom de l'utilisateur que ce soit dans la liste de gauche ou sur l'en-tête d'une conversation que je peut être potentiellement en train de consulter
    {

      element.insertAdjacentHTML('afterend', ' <i title = "connecté(e)" class="'+elem+' fas fa-circle green"></i>');
    })
  }
    else // déconnecté
  {
      document.querySelectorAll('a[data_username="'+elem+'"]').forEach(function(element)
    {

      element.insertAdjacentHTML('afterend', ' <i title = "déconnecté(e)"  class="'+elem+' fas fa-circle red"></i>');

    })
}
})

});


// je me connecte au chat : mise à jour de mes pastilles de connexion pour les autres

  socket.on('joinconv', function(data)
{

  updatestatut(data,'online');

});

// je quitte le chat : mise à jour de mes pastilles de connexion pour les autres

  socket.on('leaveconv', function(data)
{

  updatestatut(data,'offline');

});

})

  // affichage d'erreur si besoin

  .catch(function(err) {
                         console.log(err);
  });

}



// afficher une conversation

  function loadConversation(idconv, visible, typeconv)
{

  //suppression de cette class (w3-pale-blue) sur une autre évetuelle conversation lue précédemment

    if(document.querySelector('.w3-pale-blue'))
  {
      document.querySelector('.w3-pale-blue').className = document.querySelector('.w3-pale-blue').className.replace("w3-pale-blue", "");
  }

  // ajout d'un fond bleu pale montrant quelle conversation on lit

   document.querySelector('.idconv[data_idconv="'+idconv+'"]').className += " w3-pale-blue";

  // on vérifie si on est bien connecté à la conversation

    socket.emit('checkconnconv', idconv);

// on retire font-weight:bold (message non lue)

    if(document.querySelector('.idconv[data_idconv="'+idconv+'"]').getAttribute("style")!=null)
  {
    document.querySelector('.idconv[data_idconv="'+idconv+'"]').style.removeProperty("font-weight");
  }

// on vide la div d'affichage de la conversation

  	document.getElementById("displayconv").innerHTML = "";

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

    document.querySelector('.headmessage').innerHTML = document.querySelector('.userconvsin'+idconv).innerHTML.replace(/<p[^>]*>/g, "").replace(/<\/?p[^>]*>/g, "");

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

     // suppression du champs caché indiquant que l'on ne vient pas de la page d'accueuil de la messagerie

     if(document.getElementById('indexmess'))
    {
      document.getElementById('indexmess').remove();
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

    destinataires.forEach(function(item, array)
  {

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

//sinon on crée cet id de conversation caché

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

gettypeconv = typeconv;

// affichage de la conversation

document.getElementById("displayconv").innerHTML = html;

// si il y'a déjà une instance InfiniteAjaxScroll (visite d'une autre conversation), on la vide

  if(iasmessage)
{
  iasmessage = null;
}

// création d'une nouvelle instance InfiniteAjaxScroll

      iasmessage = new InfiniteAjaxScroll('.listmessage', {
       item: '.itemmessage',
       logger: false,
       next: '.next',
       spinner: {

        // element qui sera le spinner de chargement des données

        element: document.querySelector('#spinnerajaxscroll'),

        // affichage du spinner

        show: function(element) {
           element.removeAttribute('hidden');
         },

        // effacement du spinner

         hide: function(element) {
           element.setAttribute('hidden', '');
         }
       },
       pagination: '.pagination'
     });

     // action lors du chargement de toutes les données : affichage d'une div annoncant qu'il n'y a plus rien à charger

      iasmessage.on('last', function()
     {
       document.querySelector('.no-more').style.opacity = '1';
     })

})
    // affichage d'erreur si besoin

    .catch(function(err) {
  	                       console.log(err);
  	});
}
}

// menu dropdown de chaque conversations

  function openconvoption()
{
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

  // si je vient de masquer une conversation

  if(action == 'non') // désactivation réussie
{

  socket.emit('disableconv', idconv); // on quitte la room de la conversation

  // on change le fond de la div

  document.querySelector('.idconv[data_idconv="'+idconv+'"]').classList.replace("w3-pale-blue", "w3-brown");

  // mise à jour du onclick de la conversation

  document.querySelector('.idconv[data_idconv="'+idconv+'"]').setAttribute( 'onClick', 'loadConversation('+idconv+', \'non\', \''+gettypeconv+'\')');

  // mise à jour dernier message + icone

  document.querySelector('.idconv[data_idconv="'+idconv+'"] span[class="lastmessage"]').innerHTML='<i>Conversation désactivée</i>'

  // mise à jour du titre de la page

  document.querySelector('.headmessage').textContent="Conversation désactivée";

  form_message.setAttribute('hidden', ''); // formulaire masqué

  btnoptionconv.setAttribute('hidden', ''); // disparition du bouton d'option d'une conversation

  //affichage d'un message et d'un lien de réactivation

  document.getElementById("displayconv").innerHTML = '<div class="w3-container"><strong>Cette conversation est actuellement désactivée. Vous pouvez la réactivée en cliquant sur le bouton suivant : </strong><div class="w3-center"><br /><a href="#" class="w3-bar-item w3-button w3-round w3-teal editconv"  data-id_conv = '+idconv+' data_visible="oui" onclick="return false;"> Afficher cette conversation</a></div></div>';

  //notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
                '<p>Cette conversation est désormais inactive. Vous pouvez toujours recevoir des messages dans cette conversation mais elle sera masquée.</p>'+
                '</div>.');

}

  else if (action == 'oui') // activation avec succès
{

  // réaffichage du bouton d'option d'une conversation

  btnoptionconv.removeAttribute('hidden');

  loadConversation(idconv,'oui',gettypeconv); // on charge la conversation précédemment masquée

  // mise à jour des attribut de onclick de la fonction en ajoutant 'oui' signifiant que la conversation est visible

  document.querySelector('.idconv[data_idconv="'+idconv+'"]').setAttribute( 'onClick', 'loadConversation('+idconv+', \'oui\', \''+gettypeconv+'\')');

  alertbox.show('<div class="w3-panel w3-green">'+
                '<p>Conversation activée.</p>'+
                '</div>.');

  // mise à jour des informations sur la liste des conversations à gauche

                window.setTimeout(function()
              {
                document.querySelector('div[data_idconv="'+idconv+'"]').classList.replace("w3-brown", "w3-pale-blue");  // mise à jour class

                document.querySelector('.idconv[data_idconv="'+idconv+'"] span[class="auteurmessager"]').innerHTML = document.querySelector('.itemmessage p').getAttribute('data_auteurmessage'); // auteur du dernier message

                document.querySelector('.idconv[data_idconv="'+idconv+'"] span[class="lastmessage"]').innerHTML = " : " + document.querySelector('.itemmessage p').innerHTML+" - " + document.querySelector('.w3-opacity.w3-right').innerHTML; // on récupère le dernier message

              }, 1000);

  // test si le ou les destinataires de la conversation que je viens d'activer sont connectés ou non

  var testdest = document.querySelectorAll('div[data_idconv="'+idconv+'"] a');

  var destinatairesconv = [];

  testdest.forEach(item => {

  result = item.getAttribute('data_username');

  destinatairesconv.push(result);

});

  socket.emit('activateconv', destinatairesconv);

  socket.on('updateuserstatut',function(users) // users contient tous les utilisateurs connectés
{

  usersinmyconvs.forEach(function(elem) // pour chacun de mes contacts
{

    if(users.find(o => o == elem)) // connecté
  {

    updatestatut(elem,'online');

  }
    else // déconnecté
  {
    updatestatut(elem,'offline');
}
})



});

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

  function openemojimenu()
{

  if (menuemojimessage.className.indexOf("w3-show") == -1)
 {
    menuemojimessage.className += " w3-show";

  }

  else
  {
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

      switch(jsonResult.Result)
    {

      // envoi d'un message à soi même

      case 'sendtoyourself': alertbox.show('<div class="w3-panel w3-red">'+
                                            '<p>Vous ne pouvez vous envoyer de message à vous-même.</p>'+
                                            '</div>.');

                            form_message.reset();

      break;

      // utilisateur bloqué, affichage d'un message indiquant que l'envoi de message est impossible

      case 'userblock':     alertbox.show('<div class="w3-panel w3-red">'+
                                          '<p>Cet utilisateur vous à bloqué, vous ne pouvez pas lui envoyer de message.</p>'+
                                          '</div>.');

                            form_message.reset();

      break;

      // echec de l'envoi d'un message

      case 'msgnotok':  alertbox.show('<div class="w3-panel w3-red">'+
                                        '<p>Message non envoyé.</p>'+
                                        '</div>.');

                        form_message.reset();

      break;

      //envoi message réussi depuis l'index

      case 'msgok':       alertbox.show('<div class="w3-panel w3-green">'+
                                        '<p>Message envoyé !</p>'+
                                        '</div>.');

                          // on envoi les informations au serveur

                          socket.emit('messagefromindex', {destinataire: document.getElementById("user_message").value, conversation: jsonResult.conversation, user_message: jsonResult.user_message, message: jsonResult.message, notifmessage: jsonResult.notifnewmessage}); // Transmet le message aux autres

                          // réinitilisation du formulaire

                          document.getElementById("user_message").setAttribute('value', ''); // destinataire

                          form_message.reset();

      break;

      // envoi message dans une conversation : jsonResult.Result n'existe pas

      default:            socket.emit('messagefromconv', jsonResult); // Transmet le message aux autres
    }

  }).catch(function(err) {

    console.log(err);

// notification d'échec

      alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Impossible d\'envoyer ce message.</p>'+
                    '</div>.');

  });
}

// envoi message depuis une conversation avec appui sur la touche 'Entrée'

  form_message.addEventListener('keyup', function (e)
{
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

  socket.emit('notifinvittojoinconv', Data.notifjoinconv);
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
