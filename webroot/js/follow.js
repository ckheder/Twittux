/**
 * follow.js
 *
 * Traitement des actions effectuées sur la page des abonnements, abonnés et demande et affichage des notifications correspondantes
 *
 */

 /** variable **/

 var nb_following = document.querySelector('.nb_following'); // récupération du nombre d'abonnement
 var nb_attente = document.querySelector('.nb_attente'); // récupération du nombre de demande en attente

 /** page abonnement **/

document.addEventListener('click',function(e){

    if(e.target && e.target.className == 'unfollow') // clique sur un bouton pour ne plus usivre une personne
  {
        var data = {
                    "username": e.target.getAttribute('data_username') // username de la personne que je ne veut plus suivre
                    }

    let response = fetch('/twittux/abonnement/delete', {

      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authnetifié mon action depuis le site
                },
                method: "POST",

      body: JSON.stringify(data)
    })
.then(function(response) {
    return response.json(); // récupération des données au format texte
  })
    .then(function(Data) {

    	var div_following = document.querySelector('.w3-container[data-username="'+ data.username+'"]'); // on récupère la div contenant la personne que je ne souhaite plus suivre

  switch(Data.Result)
{

    //abonnement supprimé

    case "abonnementsupprime": alertbox.show('<div class="w3-panel w3-green">'+
                              '<p>Abonnement supprimer.</p>'+
                              '</div>.');

		div_following.parentNode.removeChild(div_following); // suppression de la div contenant la personne à ne plus suivre

		// décrémentation du nombre d'abonnement

		nb_following.textContent --;

    break;

    //Impossible de supprimer un abonnement

    case "abonnementnonsupprime": alertbox.show('<div class="w3-panel w3-red">'+
                                  '<p>Impossible de supprimer cet abonnement.</p>'+
                                  '</div>.');

    break;

}

    }).catch(function(err) {

      console.log(err);

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Un problème est survenu lors du traitement de votre demande.Veuillez réessayer plus tard.</p>'+
                    '</div>.');

    });
       }
})

 /** fin page abonnement **/

 /** page abonné **/

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

    // ajout d'un abonnement

    case "abonnementajoute": alertbox.show('<div class="w3-panel w3-green">'+ // notification
                                        '<p>Abonnement ajouté.</p>'+
                                        '</div>.');
    // nouveau bouton

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-red w3-round"><a class="follow" href="#" onclick="return false;" data_action="delete" data_username="'+ data.username +'">Ne plus suivre</a></button>';

    break;

    // impossible d'ajouter un nouvel abonnement

    case "abonnementnonajoute": alertbox.show('<div class="w3-panel w3-red">'+ // notification
                                        '<p>Impossible d\'ajouter cet abonnement.</p>'+
                                        '</div>.');

    break;

    //suppression d'un abonnement

    case "abonnementsupprime": alertbox.show('<div class="w3-panel w3-green">'+
                              '<p>Abonnement supprimer.</p>'+
                              '</div>.');

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'">Suivre</a></button>';

    break;

    //Impossible de supprimer un abonnement

    case "abonnementnonsupprime": alertbox.show('<div class="w3-panel w3-red">'+
                                  '<p>Impossible de supprimer cet abonnement.</p>'+
                                  '</div>.');

    break;

    //abonnement existant

    case "dejaabonne": alertbox.show('<div class="w3-panel w3-red">'+
                        '<p>Vous suivez déjà ' + data.username +' .</p>'+
                        '</div>.');


    break;

    // envoi d'une demande d'abonnement

    case "demandeok": alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Demande d\'abonnement envoyée.</p>'+
                      '</div>.');

    // bouton pour annuler ma demande d'abonnement

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-orange w3-round"><a class="follow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'">Annuler</a></button>';

    break;

    //annulation d'une demande d'abonnement

    case "demandeannule": alertbox.show('<div class="w3-panel w3-green">'+
                          '<p>Demande d\'abonnemment annulée.</p>'+
                          '</div>.');

    // bouton pour suivre ultérieurement

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'">Suivre</a></button>';

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

 /** fin page abonné **/

 /** page demande **/

//répondre à une demande

document.addEventListener('click',function(e){

  if(e.target && e.target.className == 'followrequest'){

  	var data = {
                  "action": e.target.getAttribute('data_action'), // accept -> accepter une demande d'abonnement, refuse -> refuser une demande d'abonnement
                  "username": e.target.getAttribute('data_username') // username de la personne qui demande
                }

    let response = fetch('/twittux/abonnement/request', {
    	      headers: {
                  "X-Requested-With": "XMLHttpRequest", // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  "X-CSRF-Token": csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
    	 method: "POST",

      body: JSON.stringify(data)
    })
.then(function(response) {
    return response.json(); // récupération des données au format JSON
  })
    .then(function(Data) {

    	var div_ask = document.querySelector('.w3-container[data-username="'+ data.username+'"]'); // div contenant les informations sur la personne souhaitant s'abonner

  switch(Data.Result)
{

  // demande d'abonnement acceptée

  case "accept": alertbox.show('<div class="w3-panel w3-green">'+ // notification
                                        '<p>'+ data.username+' fais désormais parti de vos abonnés.</p>'+
                                        '</div>.');

  // suppression de la personne de la page

  div_ask.parentNode.removeChild(div_ask);

  // décrémentation du nombre de demande

  nb_attente.textContent --;

  break;

  // problème pour accepter une demande, ligne SQL inexistante ?

  case "noaccept": alertbox.show('<div class="w3-panel w3-red">'+
                                '<p>Impossible d\'accepter cette demande d\'abonnement.</p>'+
                                '</div>.');

  break;

  // demande d'abonnement refusée

  case "refuse": alertbox.show('<div class="w3-panel w3-green">'+
                                '<p>Demande d\'abonnement refusé.</p>'+
                                '</div>.');

  // suppression de la personne de la page

  div_ask.parentNode.removeChild(div_ask);

  // décrémentation du nombre de demande

  nb_attente.textContent --;

  break;

  // problème pour refuser une demande, ligne SQL inexistante ?

  case "norefuse": alertbox.show('<div class="w3-panel w3-red">'+
                                    '<p>Impossible de refuser cette demande d\'abonnement.</p>'+
                                    '</div>.');
  break;

}

    }).catch(function(err) {

    	console.log(err);

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Un problème est survenu lors du traitement de votre demande.Veuillez réessayer plus tard.</p>'+
                    '</div>.');

    });
       }
})

 /** fin page demande **/

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
