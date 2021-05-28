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
var titlepage = document.title;// titre de la page
let form_comm = document.querySelector('#form_comm') // récupération du formulaire


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

    form_comm.querySelector('input[name="allowcomm"]').value = 0; // mise à jour du champ caché du formulaire

    document.querySelector('.optioncomm').textContent="Désactiver les commentaires"; // mise à jour du texte du lien du menu déroulant

    document.querySelector('#allow_submit_comm').innerHTML = '<button class="w3-button w3-blue w3-round" type="submit">Commenter</button>'; // affichage du bouton d'envoi d'un commentaire

    //notification de réussite

    alertbox.show('<div class="w3-panel w3-green">'+
                  '<p>Commentaires activés.</p>'+
                  '</div>.');

  }

    else // les commentaires sont désactivés
  {

    document.querySelector('.optioncomm').dataset.actioncomm = 0; // mise à jour du paramètre du lien du menu déroulant

    document.querySelector('.optioncomm').textContent="Activer les commentaires"; // mise à jour du texte du lien du menu déroulant

    form_comm.querySelector('input[name="allowcomm"]').value = 1; // mise à jour du champ caché du formulaire

    form_comm.querySelector('button[type=submit]').remove();// suppression du  bouton d'envoi de formulaire

     // affichage d'un message comme quoi les commentaires sont désactivés

    document.querySelector('#allow_submit_comm').innerHTML = '<div class="w3-container w3-panel w3-border w3-red">'+
                                                              '<p>'+
                                                              '<i class="fas fa-info-circle"></i> Les commentaires sont désactivés pour ce tweet.'+
                                                              '</p>';

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

  if(titlepage.match(/<img [^>]*src="[^"]*"[^>]*>/gm))
{

    var imgTags = titlepage.replace(/<img [^>]*src="[^"]*"[^>]*>/gm, '[media]');

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

})

//ajout d'un commentaire

  if(no_see == 0) // variable envoyée depuis le layout pour le test ou non d'un tweet privé si 0 -> tweet public
{

  form_comm.addEventListener('submit', function (e) { // on capte l'envoi du formulaire

  e.preventDefault();

  let button_submit_comm = form_comm.querySelector('button[type=submit]') // récupération du bouton d'envoi

  let buttonTextSubmitComm = button_submit_comm.textContent // récupération du texte du bouton

  button_submit_comm.disabled = true // désactivation du bouton

  button_submit_comm.textContent = 'Chargement...' // mise à jour du texte du bouton

  let data = new FormData(this) // on récupère les données du formulaire

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
    .then(function(jsonData) {

        if(jsonData.result == 'commblock') // commentaires désactivés
      {
        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Les commentaires sont désactivés pour ce tweet.</p>'+
                      '</div>.');
      }

      else if(jsonData.result == 'nocomm') // echec envoi du commentaire
    {
      alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Impossible de commenter ce tweet.</p>'+
                    '</div>.');
    }
      else
    {

      var el = document.getElementById("list_comm"); // récupération de la div ou l'on va insérer le nouveau commentaire

//insertion du nouveau commentaire au tout début de la div

el.insertAdjacentHTML('afterbegin', '<div class="w3-container w3-card w3-round w3-margin w3-white" id="comm'+ jsonData.id_comm+'"><br>'+
        		'<img src="/twittux/img/avatar/'+ jsonData.username+'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/>'+
            '<div class="dropdown">'+
            '<button onclick="opencommoption('+ jsonData.id_comm+')" class="dropbtn">...</button>'+
            '<div id="btncomm'+ jsonData.id_comm+'" class="dropdown-content">'+
            '<a class="deletecomm" href="#" onclick="return false;" data_idcomm="'+ jsonData.id_comm+'"> Supprimer</a>'+
            '</div>'+
            '</div>'+
        		'<h4>'+ jsonData.username+'</h4>'+
            '<span class="w3-opacity">à l\'instant</span>'+
        		'<hr class="w3-clear">'+
        		'<p>'+ jsonData.commentaire+'</p>'+
      			'</div>');

//mise à jour nombre de commentaire

nb_comm.textContent ++;

//on vide la formulaire

form_comm.reset()

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

var divcomm = document.querySelector('#comm'+data['idcomm']); // on récupère la div contenant le commentaire

divcomm.parentNode.removeChild(divcomm); // suppression de la div contenant le commentaire

//mise à jour nombre de commentaire : décrémentation

nb_comm.textContent --;

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
