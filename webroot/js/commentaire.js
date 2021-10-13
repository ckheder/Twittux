/**
 * commentaire.js
 *
 * Gestion des commentaires
 *
 */

// variables

var menuemojie = document.getElementById("menuemojie"); //div contentant la liste des emojis

var nb_comm = document.querySelector('.nbcomm'); // récupération du nombre de commentaire afin d'incrémenter ou de décrémenter le compteur

var textarea_comm = document.querySelector('#textarea_comm'); // textarea de rédaction d'un commentaire

var titlepagecomm = document.title;// titre de la page

let form_comm = document.querySelector('#form_comm') // récupération du formulaire

const socket = io("http://localhost:8083"); // connexion à Node JS avec Socket IO

// ## Node Js ## //

// connexion au serveur Node JS

socket.emit("connexion", idtweet); // on transmet mon username et toutes les conversationc précédemment stockés au serveur

// évènement ajout d'un commentaire

  socket.on('addcomm', function(data)
{

  var testlink; // lien qui s'afficheront suivant les différents scénarios

    if(data.auttweet == authname) // si je suis l'auteur du tweet...
  {

      if(data.username != authname) // ... mais pas l'auteur du comm : affichage d'un lien de suppression et de blocage
    {
      testlink = '<a class="blockuser" href="" onclick="return false;" data_username="'+data.username+'">Bloquer '+data.username+'</a>'+
                  '<a class="signalcomm" href="" onclick="return false;"> Signaler</a>';
    }
      else // ... et je suis l'auteur du commentaire
    {
      testlink = '<a class="updatecomment" href="" onclick="return false;" data_idcomm="'+ data.id_comm+'">Modifier</a>';
    }

    // dans tous les cas, création d'un lien de suppression du commentaire

      testlink += '<a class="deletecomm" href="#" onclick="return false;" data_idcomm="'+ data.id_comm+'"> Supprimer</a>';
  }

    else if (data.username == authname)
  {
    testlink = '<a class="updatecomment" href="" onclick="return false;" data_idcomm="'+ data.id_comm+'">Modifier</a>'+
                '<a class="deletecomm" href="#" onclick="return false;" data_idcomm="'+ data.id_comm+'"> Supprimer</a>';
  }


    else // pour tous les autres  : personne qui lit le commentaire et qui n'en ai ni l'auteur ni le propriétaire du tweet
  {
    testlink = '<a class="blockuser" href="" onclick="return false;" data_username="'+data.username+'">Bloquer '+data.username+'</a>'+

                '<a class="signalcomm" href="" onclick="return false;"> Signaler</a>';
  }


  //insertion du nouveau commentaire au tout début de la div

  document.querySelector("#list_comm").insertAdjacentHTML('afterbegin', '<div class="itemcomm" style="word-wrap: break-word;" id="comm'+ data.id_comm+'"><br>'+
          		'<img src="/twittux/img/avatar/'+ data.username+'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/>'+
              '<div class="dropdown">'+
              '<button onclick="opencommoption('+ data.id_comm+')" class="dropbtn">...</button>'+
              '<div id="btncomm'+ data.id_comm+'" class="dropdown-content">'+
              ''+testlink+''+
              '</div>'+
              '</div>'+
          		'<h4><a href="/twittux/'+ data.username+'">'+ data.username+'</h4>'+
              '<span class="w3-opacity">à l\'instant</span>'+
          		'<p class="commcontent'+ data.id_comm+'">'+ data.commentaire+'</p><hr>'+
        			'</div>');

  //mise à jour nombre de commentaire

  nb_comm.textContent ++;

  //on vide la formulaire

  form_comm.reset();
})

// mise à jour d'un commentaire

  socket.on('updatecomm', function(data)
{

  // mise à jour du contenu du commentaire

  document.querySelector('.commcontent'+data.idcomm+'').innerHTML = data.comment;

  // ajout de la mention modifié

  var datecomm = document.querySelector('#comm'+data.idcomm+' span[class="w3-opacity"]');

    if(!datecomm.textContent.includes(" · modifié"))  // si la mention existe déjà (un commentaire modifié est re modifié), on n'ajoute pas la mention
  {
    datecomm.insertAdjacentText("beforeend", " · modifié");
  }

    if(data.authcomm == authname) // si je suis l'auteur du commentaire, reinitialisation du formulaire
  {

    // reset du formulaires

    textarea_comm.value = '';

    // mise à jour texte du bouton d'envoi

    form_comm.querySelector('button[type=submit]').textContent = 'Commenter';

    // modification de l'url de destination

    form_comm.action = '/twittux/commentaire/add';

    // suppression du champ caché

    form_comm.removeChild(document.querySelector('input[name="commtoupdate"]'));

    // disparition du lien d'annulation de modification d'un commentaire

    document.querySelector('a[name="linkcancelupdatecomment"]').remove();
  }

})

// suppression d'un commentaire

  socket.on('deletecomm', function(data)
{

  var divcomm = document.querySelector('#comm'+data); // on récupère la div contenant le commentaire

  divcomm.parentNode.removeChild(divcomm); // suppression de la div contenant le commentaire

  // mise à jour nombre de commentaire : décrémentation

  nb_comm.textContent --;


})

// commentaires activées

  socket.on('allowcomment', function()
{
  // mise à jour du champ caché du formulaire

  form_comm.querySelector('input[name="allowcomm"]').value = 0; // 0 -> commentaires autorisés

  // affichage du bouton d'envoi d'un commentaire

  document.querySelector('#allow_submit_comm').innerHTML = '<button class="w3-button w3-blue w3-round" type="submit">Commenter</button>';

  // activation de la textarea pour rédiger un commentaire

  textarea_comm.disabled=false;

})

// commentaires désactivée

  socket.on('disablecomment', function()
{
     // mise à jour du champ caché du formulaire

    form_comm.querySelector('input[name="allowcomm"]').value = 1; // 1 -> commentaire désactivé

    // suppression du  bouton d'envoi de formulaire

    form_comm.querySelector('button[type=submit]').remove();

    // désactivation de la textarea pour rédiger un commentaire

    textarea_comm.disabled=true;

    // disparition du lien d'annulation de modification d'un commentaire si un utilisateur est en train de modifié un commentaire

      if(document.querySelector('a[name="linkcancelupdatecomment"]'))
    {
        document.querySelector('a[name="linkcancelupdatecomment"]').remove();

        document.querySelector('#allow_submit_comm').innerHTML = '<div class="w3-container w3-panel w3-border w3-red">'+
                                                                  '<p>'+
                                                                  '<i class="fas fa-info-circle"></i> Les commentaires sont désactivés pour ce tweet. Vous ne pouvez pas modifier votre commentaire.'+
                                                                  '</p>';

        textarea_comm.value = '';
    }
      else
    {
      // affichage d'un message comme quoi les commentaires sont désactivés

     document.querySelector('#allow_submit_comm').innerHTML = '<div class="w3-container w3-panel w3-border w3-red">'+
                                                               '<p>'+
                                                               '<i class="fas fa-info-circle"></i> Les commentaires sont désactivés pour ce tweet.'+
                                                               '</p>';
    }


})

// ## Fin Node Js ##

//ouverture des menu de comm : soit celui de désactivation des comm (sans id) soit celui de suppression des comms (avec id)

function opencommoption(id) {

    if(id === undefined) // si je clique sur le bouton de désactivation des commentaires
  {
    document.getElementById("commoption").classList.toggle("show");
  }
    else // bouton de suppression de commentaire
  {
    document.getElementById("btncomm"+id).classList.toggle("show");
  }
}

// Fermeture du bouton si je clique hors du menu déroulant

window.addEventListener("click", function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
})

// MODIFIER UN COMMENTAIRE

// préparation formulaire et textarea

document.addEventListener('click',function(e){

  if(e.target && e.target.className == 'updatecomment'){

    // récupère le contenu du commentaire

    var commvalue = document.querySelector('.commcontent'+e.target.getAttribute('data_idcomm')+'');

    // on remplace tous les emoji par leur alt

      // on clone d'abord le contenu du commentaire

    var clonecommcontent = commvalue.cloneNode(true);

      // pour chaque élément 'img' (emoji) on le remplace par son alt

    clonecommcontent.querySelectorAll('img').forEach(
      el =>
      {
        el.replaceWith(document.createTextNode(el.alt))
      }
    );

    // on insère le contenu du commentaire dans le textarea

    textarea_comm.value = clonecommcontent.innerHTML;

    // on ajoute l'identifiant du commentaire pour la mise à jour en BDD

      // si il existe déjà, on met sa valeur à jour

    if(form_comm.querySelector('input[name="commtoupdate"]'))
   {

    form_comm.querySelector('input[name="commtoupdate"]').value = e.target.getAttribute('data_idcomm');

   }

      // sinon on crée un nouvel input caché avec cette valeur

    else
   {
     let inputcomm = document.createElement("input");

     inputcomm.type = "hidden";

     inputcomm.name = "commtoupdate";

     inputcomm.value = e.target.getAttribute('data_idcomm');

     form_comm.appendChild(inputcomm);
   }

    // on scroll en haut

    window.scrollTo(0,0);

    // focus sur la textarea

    textarea_comm.focus();

    // modification du titre de bouton submit

    form_comm.querySelector('button[type=submit]').textContent = 'Modifier mon commentaire';

    // modification de l'url de destination

    form_comm.action = '/twittux/commentaire/update';

    // création d'un bouton d'annulation si il n'existe pas

      if(!document.querySelector('a[name="linkcancelupdatecomment"]'))
    {
      let linkcancelupdatecomment = document.createElement("a");

      linkcancelupdatecomment.name = "linkcancelupdatecomment";

      linkcancelupdatecomment.href = "#";

      linkcancelupdatecomment.innerHTML = "Annuler";

      linkcancelupdatecomment.className = 'w3-text-red';

      form_comm.appendChild(linkcancelupdatecomment);
    }
  }
});

// traitement bouton d'annulation de modification

document.addEventListener('click',function(e){

    if(e.target && e.target.name == 'linkcancelupdatecomment')
  {

    // vidage du textarea

    textarea_comm.value = '';

    // suppression du champ caché

    form_comm.removeChild(document.querySelector('input[name="commtoupdate"]'));

    // mise à jour de l'action du formulaire

    form_comm.action = '/twittux/commentaire/add';

    // mise à jour du texte du bouton submit

    form_comm.querySelector('button[type=submit]').textContent = 'Commenter';

    // disparition du lien

    e.target.remove();
  }

});

// FIN MODIFIER UN COMMENTAIRE

//désactivation des commentaires

document.addEventListener('click',function(e){

  if(e.target && e.target.className == 'optioncomm'){

    var data = {
                  "action": e.target.getAttribute('data-actioncomm'), // 0 -> activation des commentaires, 1 -> désactivation des commentaires
                  "idtweet": e.target.getAttribute('data_idtweet') // identifiant du tweet à traité
                }

    let response = fetch('/twittux/commentaire/actioncomm', { // on ajoute l'id à l'URL
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(data)
    })
    .then(function(response)
    {
      return response.text(); // récupération des données au format texte
    }
  )
    .then(function(Data) {

  if(Data == 'updatecommnotok') // impossible de mettre à jour les préférences de commentaires
{

          alertbox.show('<div class="w3-panel w3-red">'+
                        '<p>Impossible de modifier les paramètres de ce tweet.</p>'+
                        '</div>.');
}
  else if (Data == 'updatecommok') // mise à jour réussie
{

    if(e.target.getAttribute('data-actioncomm') == 0) // les commentaires sont activés
  {

    document.querySelector('.optioncomm').dataset.actioncomm = 1; // mise à jour du paramètre du lien du menu déroulant

    document.querySelector('.optioncomm').textContent="Désactiver les commentaires"; // mise à jour du texte du lien du menu déroulant

    socket.emit('userallowcomment', idtweet);

    //notification de réussite

    alertbox.show('<div class="w3-panel w3-green">'+
                  '<p>Commentaires activés.</p>'+
                  '</div>.');

  }

    else // les commentaires sont désactivés
  {

    document.querySelector('.optioncomm').dataset.actioncomm = 0; // mise à jour du paramètre du lien du menu déroulant

    document.querySelector('.optioncomm').textContent="Activer les commentaires"; // mise à jour du texte du lien du menu déroulant

    socket.emit('userdisablecomment', idtweet);

    //notification de réussite

    alertbox.show('<div class="w3-panel w3-green">'+
                  '<p>Commentaires désactivés.</p>'+
                  '</div>.');

  }

}
    }).catch(function(err) {

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de supprimer ce commentaire.</p>'+
                      '</div>.');

    });

  }
})

// comme le titre de page reprend le début des tweets, remplacement des médias par [media]

  if(titlepagecomm.match(/<img [^>]*src="[^"]*"[^>]*>/gm))
{

    var imgTags = titlepagecomm.replace(/<img [^>]*src="[^"]*"[^>]*>/gm, '[media]');

    document.title = imgTags;

}

//emoji

function openemojimenu() {

 if (menuemojie.className.indexOf("w3-show") == -1) {
        menuemojie.className += " w3-show";
    } else {
        menuemojie.className = menuemojie.className.replace(" w3-show", "");
    }
}

// traitement des emoji dans le textarea des commentaires

document.addEventListener('click',function(e){
  // si on détecte un emoji avec la class emoji
  if(e.target && e.target.className == 'emoji'){
    //on récupère le code emoji
    var code = e.target.getAttribute('data_code');
    //suppression de l'extension du fichier
    code  = code.replace(/\.[^/.]+$/, "");
    code = ' :'+code+': ';
    //ajout au textarea
  textarea_comm.value += code;
  //fermeture de la liste des emojis
  menuemojie.className = menuemojie.className.replace(" w3-show", "");
  //focus sur la textarea
  textarea_comm.focus();
}
});

// Infinite AJAX scroll de la liste des commentaires

  if(nb_comm.textContent > 0) // si le nombre de commentaire est supérieur à 0, on active IAS
{

let ias = new InfiniteAjaxScroll('#list_comm', {
  item: '.itemcomm',
  next: '.next',
  logger: false,
  spinner: {

    // element qui sera le spinner de chargement des données

    element: document.querySelector('#spinnerajaxscroll'),

    // affichage du spinner

    show: function(element) {
      element.removeAttribute('hidden');
    },

    // effacement du spinner

    hide: function(element) {
      element.setAttribute('hidden', ''); // default behaviour
    }
  },
  pagination: '.pagination'
});

// action lors du chargement de toutes les données : affichage d'une div annoncant qu'il n'y a plus rien à charger

ias.on('last', function() {

  document.querySelector('.no-more').style.opacity = '1';

});

// en cas d'erreur : suppression du spinner de chargement et affichage de la div de fin des commentaires

ias.on('error', function() {

  document.querySelector('#spinnerajaxscroll').setAttribute('hidden', '');

  document.querySelector('.no-more').style.opacity = '1';

});

}

//ajout d'un commentaire

  if(no_see == 0) // variable envoyée depuis le layout pour le test ou non d'un tweet privé si 0 -> tweet public
{

  form_comm.addEventListener('submit', function (e) { // on capte l'envoi du formulaire

  e.preventDefault();

  let button_submit_comm = form_comm.querySelector('button[type=submit]') // récupération du bouton d'envoi

  let buttonTextSubmitComm = button_submit_comm.textContent // récupération du texte du bouton

  button_submit_comm.disabled = true // désactivation du bouton

  button_submit_comm.textContent = 'Chargement...' // mise à jour du texte du bouton

  let data = new FormData(this); // on récupère les données du formulaire

    let response = fetch(form_comm.getAttribute('action'), { // on récupère l'URL d'envoi des données
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

      // commentaires désactivés

      case 'commblock': alertbox.show('<div class="w3-panel w3-red">'+
                            '<p>Les commentaires sont désactivés pour ce tweet.</p>'+
                            '</div>.');

      break;

      // echec envoi du commentaire

      case 'nocomm':    alertbox.show('<div class="w3-panel w3-red">'+
                          '<p>Impossible de commenter ce tweet.</p>'+
                          '</div>.');

      break;

      // commentaire mis à jour avec succès

      case 'updatecommok':  alertbox.show('<div class="w3-panel w3-green">'+
                              '<p>Commentaire mis à jour.</p>'+
                              '</div>.');

                            // émission d'un event Node JS pour affichher le comm à jour  pour tous ceux qui regarde

                            socket.emit('userupdatecomm', {idtweet: idtweet, comment: jsonResult.commupdated, idcomm: jsonResult.idcomm, authcomm: jsonResult.authcomm});


      break;

      // commentaire non mis à jour

      case 'updatecommnotok':   alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de modifier ce commentaire : soit vous n\'avez rien modifié soit ce commentaire ne vous appartient pas.</p>'+
                      '</div>.');


      break;

      // par défaut : émission d'un event Node JS de création avec succès d'un nouveau commentaire et affichage poiur ceux qui regardent

      default: socket.emit('newcomm', {idtweet: idtweet, comm: jsonResult});

//notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
  							'<p>Commentaire posté.</p>'+
								'</div>.');

}
    }).catch(function(err) {

// notification d'échec

    	  alertbox.show('<div class="w3-panel w3-red">'+
  										'<p>Impossible de commenter.</p>'+
										'</div>.');

    });
  button_submit_comm.disabled = false // on réactive le bouton
  button_submit_comm.textContent = buttonTextSubmitComm // on remet le texte initial du bouton
})

//supprimer un commentaire

document.addEventListener('click',function(e){

  if(e.target && e.target.className == 'deletecomm'){

    var data = {
                  "idcomm": e.target.getAttribute('data_idcomm'), // identifiant du commentaire
                  "idtweet": document.querySelector('#form_comm').elements['id_tweet'].value // identifiant du tweet
                }


    let response = fetch('/twittux/commentaire/delete', { // on ajoute l'id à l'URL
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(data)
    })
.then(function(response) {
    return response.json(); // récupération des données au format JSON
  })
    .then(function(Data) {

//suppression du commentaire

  if(Data.Result == 'deletecommnotok') // impossible de supprimer un commentaire : mauvais utilisateur par exemple
{
          alertbox.show('<div class="w3-panel w3-red">'+
                        '<p>Impossible de supprimer ce commentaire.</p>'+
                        '</div>.');
}
  else if (Data.Result == 'deletecommok') // suppression réussie du commentaire
{

  // émission d'un event Node JS pour supprimer le commentaire pour ceux qui regardent la page

socket.emit('deletecommok', {idtweet: idtweet, idcomm: data['idcomm']})

//notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Commentaire supprimé.</p>'+
                    '</div>.');

}
    }).catch(function(err) {

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de supprimer ce commentaire.</p>'+
                    '</div>.');

    });

  }
})
}
 /** affichage des notifications **/

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

/** fin affichage des notifications **/

/**ABONNEMENT **/

/** abonnement si tweet privé **/

document.addEventListener('click',function(e){

 if(e.target && e.target.className == 'follow'){

   var action = e.target.getAttribute('data_action'); // follow -> crée un abonnement, delete -> supprimer un abonnement,cancel -> annuler une demande d'abonnement

          var data = {
                       "username": e.target.getAttribute('data_username') // username de la personne concerné par mon click sur un bouton
                     }

   let response = fetch('/twittux/abonnement/'+action+'', {
     headers: {
                 'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                 'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
               },
               method: "POST",

     body: JSON.stringify(data)

   })
.then(function(response) {
   return response.json(); // récupération des données au format JSON
 })
   .then(function(Data) {

 switch(Data.Result)
{

   // impossible d'ajouter un nouvel abonnement

   case "abonnementnonajoute": alertbox.show('<div class="w3-panel w3-red">'+ // notification
                                       '<p>Impossible d\'ajouter cet abonnement.</p>'+
                                       '</div>.');

   break;

   // envoi d'une demande d'abonnement

   case "demandeok": alertbox.show('<div class="w3-panel w3-green">'+
                     '<p>Demande d\'abonnement envoyée.</p>'+
                     '</div>.');

   // bouton pour annuler ma demande d'abonnement

   document.querySelector('.zone_abo').innerHTML = '<button class="w3-button w3-orange w3-round"><a class="follow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'">Annuler</a></button>';

   break;

   //annulation d'une demande d'abonnement

   case "demandeannule": alertbox.show('<div class="w3-panel w3-green">'+
                         '<p>Demande d\'abonnemment annulée.</p>'+
                         '</div>.');

  // bouton de suivi

  document.querySelector('.zone_abo').innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'">Suivre</a></button>';

  break;


   //impossible d'annuler une demande d'abonnement

   case "demandenonannule": alertbox.show('<div class="w3-panel w3-red">'+
                           '<p>Impossible d\'annuler la demande d\'abonnement.</p>'+
                           '</div>.');

   break;

}

   }).catch(function(err) {

// notification d'échec : problème technique, serveur,...

       alertbox.show('<div class="w3-panel w3-red">'+
                     '<p>Un problème est survenu lors du traitement de votre demande.Veuillez réessayer plus tard.</p>'+
                   '</div>.');

   });
      }
})

/** BLOCAGE **/

// création d'un blocage

// au click sur le bouton, on redirige vers une action du controlleur qui và vérifier si je n'ai pas déjà bloquer cette personne

// si non on crée un blocage et , si oui, on le notifie

document.addEventListener('click',function(e){

    if(e.target && e.target.className == 'blockuser') // clique sur le bouton avec la classe 'blockuser'
  {

    var data = {
                "username": e.target.getAttribute('data_username') // username de la personne à qui je veut envoyer un message
                }

    let response = fetch('/twittux/blockuser', {
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(data)
    })
.then(function(response) {

    return response.json(); // récupération des données au format JSON

  })
    .then(function(Data) {


        if(Data.Result == "addblock") // création d'un blocage réussie
     {

       // affichage notification

       alertbox.show('<div class="w3-panel w3-green">'+
                     '<p>Utilisateur bloqué.</p>'+
                   '</div>.');

     }

       else if (Data.Result == "existblock") // blocage existant
    {

      // affichage notification

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Cet utilisateur est déjà bloqué.</p>'+
                    '</div>.');
    }
        else // problème

    {

      // affichage notification

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de bloqué cet utilisateur.</p>'+
                    '</div>.');
    }


    }).catch(function(err) {

      // notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de bloqué cet utilisateur.</p>'+
                    '</div>.');

    });

}
})
