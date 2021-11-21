/**
 * follow.js
 *
 * Traitement des actions effectuées sur la page des abonnements, abonnés et demande et affichage des notifications correspondantes
 *
 */

 /** variable **/

 var iasfollow; // variable contenant la construction de l'Infinite Ajax Scroll

 var URL; // URL à charger suivant l'onglet cliqué

 var DIVIAS; // Div ou sera chargé les données IAS suivant la page

 var ITEMS; // servira à crée une instance d'Infinite Ajax Scroll si cette variable est supérieur à zéro

 const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

 //**Connexion NODE JS */

  socket.emit("connexion", {authname: authname}); // on transmet mon username au serveur

 //à l'ouverture de la page on charge la page des abonnements en AJAX

  document.querySelector("#list_following").addEventListener("load", loadSocialItem(currentuser,'following'));

 // rejoindre depuis la page abonné depuis le profil

 // si l'item existe, c'est qu'on a cliqué sur le lien abonné d'un profil

  if(localStorage.getItem("followlink"))
 {

   // on appelle la fonction loadSocialItem pour charger le lien dans la div

   loadSocialItem(localStorage.getItem("followlink"),'followers');

   // suppression de l'item local 'username'

   localStorage.removeItem("followlink");

 }

 // rejoindre la page des demandes d'abonnement depuis une notification

 // si l'item existe, c'est qu'on a cliqué sur une notification de demande d'abonnement

  if(localStorage.getItem("requestlink"))
 {

   // on appelle la fonction loadSocialItem pour charger le lien dans la div

   loadSocialItem(authname,'requests');

   // suppression de l'item local 'username'

   localStorage.removeItem("requestlink");

 }

 // chargement des différentes page d'abonnements, abonné , demande et utilisateur bloqué au clique et par AJAX

 function loadSocialItem(currentuser = null, item) // currentuser : utilisateur actuel (soit le connecté soit u profil qu'on visitait et on a cliqué sur le lien de ses abonnements et abonnés),item : page à charger
{

   		switch(item)
   	{
   		case "following": // page des abonnements d'une personne

                        URL = '/twittux/social/'+currentuser+'';

                        DIVIAS = 'list_following'; // Nom de la Div à utiliser pour InfiniteAjaxScroll

                        ITEMS = 'nb_following';

   							break;

   		case "followers": // page des abonnés d'une personne

                        URL = '/twittux/abonnes/'+currentuser+'';

                        DIVIAS = 'list_followers'; // Nom de la Div à utiliser pour InfiniteAjaxScroll

                        ITEMS = 'nb_follower';

   							break;

   		case "requests": // page des demandes d'abonnement d'une personne

                        URL = '/twittux/abonnement/demande';

                        DIVIAS = 'list_request'; // Nom de la Div à utiliser pour InfiniteAjaxScroll

                        ITEMS = 'nb_attente';

   							break;

       case "usersblocks": // page des utilisateurs bloqués

                        URL = '/twittux/userblock';

                        DIVIAS = 'list_userblock'; // Nom de la Div à utiliser pour InfiniteAjaxScroll

                        ITEMS = 'nb_user_block';

             		break;

       default: return;
   	}

   	document.getElementById("socialsinfos").innerHTML = ""; // on vide la div d'affichage des résultats

     spinner.removeAttribute('hidden'); // affichage du spinner de chargement

     fetch(URL, { // URL à charger dans la div précédente

                 headers: {
                             'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                           }
               })

     .then(function (data) {
                             return data.text();
                           })
     .then(function (html) {

 	   spinner.setAttribute('hidden', ''); // disparition du spinner

     // suppression de la classe w3-red sur le bouton ayant cette classe

      document.querySelector('.linkso.w3-red').className = document.querySelector('.linkso.w3-red').className.replace("w3-red", "");

      // ajout de la classe w3-red sur l'item cliqué

      document.getElementById(item).className += " w3-red";

      document.getElementById("socialsinfos").innerHTML = html; // chargement de la réponse dans la div précédente

        if(document.querySelector('.itemsocial')) // si au moins un résultat est retourné, on instancie IAS
      {
          // si il y'a déjà une instance InfiniteAjaxScroll (visite d'une autre page social), on la vide

          if(iasfollow)
        {
          iasfollow = null;
        }

          // création d'une nouvelle instance InfiniteAjaxScroll

          iasfollow = new InfiniteAjaxScroll('#'+DIVIAS+'', {
           item: '.itemsocial',
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

         iasfollow.on('last', function() {

           document.querySelector('.no-more').style.opacity = '1';
         })
      }
  })

     // affichage d'erreur si besoin

     .catch(function(err) {
   	                       console.log(err);
   	});

 }

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

		document.querySelector('.nb_following').textContent --;

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

  if(e.target && e.target.className == 'actionfollow'){

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

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-red w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="delete" data_username="'+ data.username +'"><i class="fas fa-user-minus"></i> Ne plus suivre</a></button>';

      if(Data.notifabo == 'oui')
    {
      socket.emit('newabo', data.username);
    }

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

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-blue w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-orange w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'"><i class="fas fa-user-times"></i> Annuler</a></button>';

    if(Data.notifabo == 'oui')
    {
      socket.emit('newabo', data.username);
    }

    break;

    //annulation d'une demande d'abonnement

    case "demandeannule": alertbox.show('<div class="w3-panel w3-green">'+
                          '<p>Demande d\'abonnemment annulée.</p>'+
                          '</div>.');

    // bouton pour suivre ultérieurement

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-blue w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

  document.querySelector('.nb_attente').textContent --;

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

  document.querySelector('.nb_attente').textContent --;

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

/** BLOCAGE **/

// création d'un blocage

// au click sur le bouton, on redirige vers une action du controlleur qui và vérifier si je n'ai pas déjà bloquer cette personne
// si non on crée un blocage et , si oui, on le notifie

document.addEventListener('click',function(e){

    if(e.target && e.target.className == 'blockuser') // clique sur le bouton avec la classe 'blokuser'
  {

    var data = {
                "username": e.target.getAttribute('data_username') // username de la personne que je veut bloquer
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

      // si je block depuis la page abonné ou demande, je supprime l'item

      if(document.querySelector('.w3-container[data-username="'+ data.username+'"]'))
    {
      document.querySelector('.w3-container[data-username="'+ data.username+'"]').parentNode.removeChild(document.querySelector('.w3-container[data-username="'+ data.username+'"]')); // suppression de la div contenant la personne à ne plus suivre

      // décrémentation du nombre d'abonné si je bloque depuis la page abonné

        if(document.querySelector('.nb_follower'))
      {
        document.querySelector('.nb_follower').textContent --;
      }

      // décrémentation du nombre de demande d'abonnement si je bloque depuis la page demande

        else if (document.querySelector('.nb_attente'))
      {
        document.querySelector('.nb_attente').textContent --;
      }

    }
      else // mise à jour du bouton de blocage
    {
      document.querySelector('.zone_blocage[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-black w3-round"><a class="deblockuser" href="" onclick="return false;" data_username="'+ data.username+'"><i class="fas fa-unlock"></i> Débloquer </a></button>';
    }

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
// suppression d'un blocage

// au click sur le bouton, on redirige vers une action du controlleur qui và supprimer ce blocage d'utilisateur

document.addEventListener('click',function(e){

    if(e.target && e.target.className == 'deblockuser') // clique sur le bouton avec la classe 'deblockuser'
  {

    var data = {
                "username": e.target.getAttribute('data_username') // username de la personne à qui je veut envoyer un message
                }

    let response = fetch('/twittux/deblockuser', {
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(data)
    })
    .then(function(response)
{
    return response.json(); // récupération des données au format JSON
}

)
    .then(function(Data) {

    // blocage supprimé avec succès

      if(Data.Result == "blocagesupprime")
     {

    // affichage notification

       alertbox.show('<div class="w3-panel w3-green">'+
                     '<p>Utilisateur débloqué.</p>'+
                     '</div>.');

    // débloquage depuis la page abonnés, profil ou moteur de recherche  -> mise à jour du bouton de blocage

            if(document.querySelector('.zone_blocage[data_username="'+ data.username+'"]'))
          {
            document.querySelector('.zone_blocage[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-black w3-round"><a class="blockuser" href="" onclick="return false;" data_username="'+ data.username+'"><i class="fas fa-lock"></i> Bloquer </a></button>';
          }

    // déblocage depuis la page des utilisateurs bloqués -> suppression de la div utilisateur

            else if (document.querySelector('div[data-username="'+ data.username+'"]'))
          {

            document.querySelector('div[data-username="'+ data.username+'"]').parentNode.removeChild(document.querySelector('div[data-username="'+ data.username+'"]'));

    // décrémentation du nombre d'utilisateur bloqué

            document.querySelector('.nb_user_block').textContent --;

          }

     }

       // impossible de supprimer le blocage

       else if (Data.Result == "blocagenonsupprime")
      {

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de débloqué cet utilisateur.</p>'+
                    '</div>.');
      }


    }).catch(function(err) {

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de débloqué cet utilisateur</p>'+
                    '</div>.');

    });

}
})
