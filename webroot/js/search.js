/**
 * search.js
 *
 * Gestion des actions lors d'une recherche
 *
 */

// variable

// la variable currenturl est générée par le layout search pour déterminer si on ait une recherche classqieu ou via hashtag

const regexp = /#(\S)/g; // expression régulière qui va servir à ôter le hashtag # sur la génération d'une URL de recherche hashtag

const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

var URL; // URL de rercherche à charger suivant l'onglet cliqué

var iassearch; // variable contenant la construction de l'Infinite Ajax Scroll

var DIVIAS; // Div ou sera chargé les données IAS suivant la page

let zone_abo; // variable utilisée pour contenir une div existant dans la fenêtre modale pour mettre à jour le bouton d'abonnement

//**Connexion NODE JS */

socket.emit("connexion", {authname: authname,rooms: 'searchpage'}); // on transmet mon username au serveur

//** like */

// ajout /suppression d'un like/

  socket.on('actionlike', function(data)
{

    if(data.action == 'add') // ajout d'un like
  {
    document.querySelector('.nb_like_'+data.idtweet).textContent ++;

    // si le nombre de like vaut 0 (donc pas de fonction onclick() pour ouvrir la modale des like), on crée désormais un lien vers une modale contenant le nombre de like

      if(document.querySelector('.modallike_'+data.idtweet).onclick == null)
    {
        document.querySelector('.modallike_'+data.idtweet).setAttribute('onclick', 'openmodallike('+data.idtweet+');');
    
        document.querySelector('.modallike_'+data.idtweet).style.cursor = "pointer";
    }
  }
    else if(data.action == 'remove') // suppression d'un like
  {
    document.querySelector('.nb_like_'+data.idtweet).textContent --;

    // si le nombre de like vaut 0 , on supprime l'attribut onclik() et on revient à un curseur initial

      if(document.querySelector('.nb_like_'+data.idtweet).textContent == 0)
    {
        document.querySelector('.modallike_'+data.idtweet).removeAttribute('onclick');
        
        document.querySelector('.modallike_'+data.idtweet).style.cursor = "initial";
    }
  }
})

//** Partage */

// nouveau partage

  socket.on('addshare', function(idtweet)
{
  if(document.querySelector('.nb_share_'+idtweet))
  {
    document.querySelector('.nb_share_'+idtweet).textContent ++;
  }
})

//** Fin NODE JS */

// chargement par AJAX des résultats de recherche

document.querySelector("#result_search").addEventListener("load", loadSearchItem('searchtweets'));

// chargement de donné via lien

    function loadSearchItem(itemsearch)
  {


  		switch(itemsearch)
  	{
  		case "searchusers": // recherche d'utilisateurs
                          if(currenturl === 'search') // page de recherche classiqie
                        {
  							           URL = '/twittux/search/users/'+keyword+'';
                           DIVIAS = 'query_users'; // Nom de la Div à utiliser pour InfiniteAjaxScroll
                        }
                          else // page de recherche hashtag sur la description
                        {

                          keyword = keyword.replace(regexp, '$1');

                          URL = '/twittux/search/hashtag/users/'+keyword+'';
                          DIVIAS = 'resultat_users'; // Nom de la Div à utiliser pour InfiniteAjaxScroll

                        }



  							break;

  		case "searchtweets": // recherche dans les tweets
                          if(currenturl === 'search') // page de recherche classiqie
                        {
  							           URL = '/twittux/search/'+keyword+'';
                           DIVIAS = 'query_tweet'; // Nom de la Div à utiliser pour InfiniteAjaxScroll
                        }
                          else // page de recherche hashtag sur le contenu des tweets
                        {

                          keyword = keyword.replace(regexp, '$1');

                          URL = '/twittux/search/hashtag/'+keyword+'';
                          DIVIAS = 'resultat_tweet'; // Nom de la Div à utiliser pour InfiniteAjaxScroll

                        }



  							break;

  		case "searchmostrecent": // tri sur la date des tweets (les plus récents)
                              if(currenturl === 'search') // page de recherche classiqie
                            {
  							               URL = '/twittux/search/'+keyword+'?sort=created&direction=desc';
                               DIVIAS = 'query_tweet'; // Nom de la Div à utiliser pour InfiniteAjaxScroll
                            }
                              else // page de recherche hashtag sur le contenu des tweets
                            {
                              keyword = keyword.replace(regexp, '$1');

                              URL = '/twittux/search/hashtag/'+keyword+'?sort=created&direction=desc';
                              DIVIAS = 'resultat_tweet'; // Nom de la Div à utiliser pour InfiniteAjaxScroll
                            }


  							break;

      case "searchmediapics": // tweets avec média
                              if(currenturl === 'search') // page de recherche classiqie
                            {
            							      URL = '/twittux/search/media/'+keyword+'';
                                DIVIAS = 'resultat_tweet_media'; // Nom de la Div à utiliser pour InfiniteAjaxScroll
                            }
                              else // page de recherche hashtag sur le contenu des tweets avec media
                            {
                              keyword = keyword.replace(regexp, '$1');

                              URL = '/twittux/search/hashtag/media/'+keyword+'';
                              DIVIAS = 'resultat_tweet_hashtag_media'; // Nom de la Div à utiliser pour InfiniteAjaxScroll
                            }


            							break;

      default: return;
  	}

  	document.getElementById("result_search").innerHTML = ""; // on vide la div d'affichage des résultats

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

      document.querySelector('.linksearch.w3-red').className = document.querySelector('.linksearch.w3-red').className.replace("w3-red", "");

      // ajout de la classe w3-red sur l'item cliqué

      document.getElementById(itemsearch).className += " w3-red";

      document.getElementById("result_search").innerHTML = html; // chargement de la réponse dans la div précédente

        if(document.querySelector('.itemsearch')) // si il y'a au minimum 1 résultat de recherche, on instancie IAS
      {

        iassearch = null;

        // création d'une nouvelle instance InfiniteAjaxScroll

          iassearch = new InfiniteAjaxScroll('.'+DIVIAS+'', {
           item: '.itemsearch',
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
               element.setAttribute('hidden', '');
             }
           },
           pagination: '.pagination'
         });

         // action lors du chargement de toutes les données : affichage d'une div annoncant qu'il n'y a plus rien à charger

          iassearch.on('last', function() {

           document.querySelector('.no-more').style.opacity = '1';
         })

        }

    })

    // affichage d'erreur si besoin

    .catch(function(err) {
  	                       console.log(err);
  	});

}

// traitement des actions d'abonnement/demande/suppression sur les résultats utilisateurs du moteur de recherche

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

      if(document.querySelector('.zone_abo_like[data_username="'+ data['username']+'"]')) // traitement abonnement depuis la modale like
    {
      zone_abo = document.querySelector('.zone_abo_like[data_username="'+ data['username']+'"]');
    }
      else
    {
      zone_abo = document.querySelector('.zone_abo[data_username="'+ data.username+'"]'); // traitement abonnement depuis la page search user
    }

  switch(Data.Result)
{

    // ajout d'un abonnement

    case "abonnementajoute": alertbox.show('<div class="w3-panel w3-green">'+ // notification
                                          '<p>Abonnement ajouté.</p>'+
                                          '</div>.');
    // nouveau bouton

    zone_abo.innerHTML = '<button class="w3-button w3-red w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="delete" data_username="'+ data.username +'"><i class="fas fa-user-minus"></i> Ne plus suivre</a></button>';

      if(Data.notifabo == 'oui') // si l'utilisateur accepte les notifications d'abonnement, on émet un évènement Node JS
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

    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

    zone_abo.innerHTML = '<button class="w3-button w3-orange w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'"><i class="fas fa-user-times"></i> Annuler</a></button>';

      if(Data.notifabo == 'oui') // si l'utilisateur accepte les notifications d'abonnement, on émet un évènement Node JS
    {
      socket.emit('newabo', data.username);
    }

    break;

    //annulation d'une demande d'abonnement

    case "demandeannule": alertbox.show('<div class="w3-panel w3-green">'+
                                        '<p>Demande d\'abonnemment annulée.</p>'+
                                        '</div>.');

    // bouton pour suivre ultérieurement

    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

    break;

    //impossible d'annuler une demande d'abonnement

    case "demandenonannule": alertbox.show('<div class="w3-panel w3-red">'+
                            '<p>Impossible d\'annuler la demande d\'abonnement.</p>'+
                            '</div>.');

    break;

    //utilisateur bloqué

    case "userblock": alertbox.show('<div class="w3-panel w3-red">'+
                        '<p>' + data.username +' vous à bloqué.</p>'+
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

 //menu déroulant tweet

 function openmenutweet(id) {

    document.getElementById("btntweet"+id).classList.toggle("show");
}

window.onclick = function(event) {
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
}

/** traitement des like **/

// ajout/suppression like

document.addEventListener('click',function(e){

  if(e.target && e.target.getAttribute('data_action') == 'like'){

    var idtweet = e.target.getAttribute('data_id_tweet');
    var auttweet = e.target.getAttribute('data_auttweet');

    let response = fetch('/twittux/likecontent', {
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(idtweet)
    })
      .then(function(response)
      {
        return response.json(); // récupération des données au format json
      })

      .then(function(Data) {

  switch(Data.Result)
{

    // ajout d'un like -> envoi d'un évènement au serveur Node ( action add -> like)

    case "addlike": socket.emit('like', {idtweet: idtweet, auttweet: auttweet, action: 'add'});

    break;

    // suppression d'un like -> envoi d'un évènement au serveur Node ( action remove -> dislike)

    case "dislike": socket.emit('like', {idtweet: idtweet, auttweet: auttweet, action: 'remove'});

    break;

    // problème de création de like

    case "probleme": alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Un problème est survenu lors du traitement de votre demande.Veuillez réessayer plus tard.</p>'+
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



// fin affichage modal des like

/** fin traitement des like **/

/** traitement des partages **/

document.addEventListener('click',function(e){

  if(e.target && e.target.getAttribute('data_action') == 'share'){

    var data = {
      "idtweet": e.target.getAttribute('data_id_tweet'),
      "auttweet": e.target.getAttribute('data_auttweet')
    }

    let response = fetch('/twittux/share', {
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(data)
    })
      .then(function(response)
      {
        return response.json(); // récupération des données au format json
      })

      .then(function(Data) {

  switch(Data.Result)
{

    // ajout d'un partage -> envoi d'un évènement au serveur Node (nouveau partage)

    case "addshare": 

                      alertbox.show('<div class="w3-panel w3-green">'+
                                    '<p>Post partagé.</p>'+
                                    '</div>.');

                      socket.emit('newshare', {auttweet: data.auttweet,idtweet: data.idtweet, notifshare: Data.notifshare});

    break;

    // suppression d'un partage -> mise à jour du nombre de partage

    case "existshare": alertbox.show('<div class="w3-panel w3-red">'+
                                      '<p>Vous avez déjà partagé ce post.</p>'+
                                      '</div>.');

    break;

    // problème de création de like

    case "probleme": alertbox.show('<div class="w3-panel w3-red">'+
                                    '<p>Un problème est survenu lors du traitement de votre demande.Veuillez réessayer plus tard controller.</p>'+
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

    let response = fetch('/twittux/blockuser', { // on ajoute l'id à l'URL
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

      // blocage ajouté

      if(Data.Result == "addblock")
     {

       // affichage notification

       alertbox.show('<div class="w3-panel w3-green">'+
                     '<p>Utilisateur bloqué.</p>'+
                     '</div>.');

      // mise à jout bouton de blocage

        document.querySelector('.zone_blocage[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-black w3-round"><a class="deblockuser" href="" onclick="return false;" data_username="'+ data.username+'"><i class="fas fa-unlock"></i> Débloquer </a></button>';

     }

       // blocage existant

       else if (Data.Result == "existblock")
      {

        // affichage notification

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Cet utilisateur est déjà bloqué.</p>'+
                    '</div>.');

      }
        else
      {
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

    let response = fetch('/twittux/deblockuser', { // on ajoute l'id à l'URL
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

      // blocage supprimé

      if(Data.Result == "blocagesupprime")
     {

       // affichage notification

       alertbox.show('<div class="w3-panel w3-green">'+
                     '<p>Utilisateur débloqué.</p>'+
                   '</div>.');

        document.querySelector('.zone_blocage[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-black w3-round"><a class="blockuser" href="" onclick="return false;" data_username="'+ data.username+'"><i class="fas fa-lock"></i> Bloquer </a></button>';

     }

     // blocage non supprimé

       else if (Data.Result == "blocagenonsupprime")
      {

    // affichage notification

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
