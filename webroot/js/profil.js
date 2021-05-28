/**
 * tweet.js
 *
 * Gestion des tweet, abonnement à un profil en cours de visite, affichage de notifications,like
 *
 */

  const zone_abo = document.querySelector('#zone_abo'); // zone contentna t les boutons d'abonnement, suppression ou demande

  const zone_blocage = document.querySelector('#zone_blocage'); // zone contentant les boutons de blocage : ajout ou suppression

  var URL; // URL à atteindre suivant le type de suppression d'un tweet : tweet personnel ou tweet partagé

  const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

  const navAnchor = document.querySelectorAll('.tablinktweet'); // liste de tous les liens du menu pour permettre de surligner le lien actif

  let iastweet; // variable contenant la construction de l'Infinite Ajax Scroll pour les tweets et les like

  var url_tweet; // URL de recherche à charger suivant l'onglet cliqué

  // surlignage

  // ajout d'un écouteur de clique sur chaque lien du menu

  navAnchor.forEach(anchor => {
    anchor.addEventListener('click', addActive);
  })

  // on enlève la classe w3-red à l'item qui la possède pour la donner à l'élkément cliqué

  function addActive(e) {
    const current = document.querySelector('.tablinktweet.w3-red');
    current.className = current.className.replace("w3-red", "");
    e.target.className += " w3-red";
  }

// chargement par AJAX des tweets sans média

  document.querySelector("#list_tweet_"+username+"").addEventListener("load", loadTweetItem('showtweets'));

 //menu déroulant tweet

 function openmenutweet(id) {

    document.getElementById("btntweet"+id).classList.toggle("show");
}

// Fermeture du bouton si je clique hors du menu déroulant des tweets

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

// accéder à la page abonnés depuis le lien de profil

  document.querySelector("#usersfollowers").addEventListener('click',function(e)
{

// création d'un item local contenant le nom du profil en cours de visite

  localStorage.setItem("followlink", e.target.getAttribute('data_username'));

// redirection vers la page social

  window.location.href = '/twittux/social/'+ e.target.getAttribute('data_username')+'';

})

// naviguer entre les tweet et les tweets avec media

  function loadTweetItem(itemtweet)
{

    if(itemtweet == 'showtweets') // URL d'affichage de tous les tweets d'une personne
  {

    url_tweet = '/twittux/'+username+'';

  }
    else if (itemtweet === 'showmediatweets') // URL d'affichage de tous les tweets d'une personne contenant un média uploadé
  {

    url_tweet = '/twittux/'+username+'/media';

  }
    else
  {
      return;
  }

  	document.getElementById("list_tweet_"+username+"").innerHTML = ""; // on vide la div d'affichage des tweets

    spinner.removeAttribute('hidden'); // affichage du spinner de chargement

    fetch(url_tweet, { // URL à charger dans la div précédente

                headers: {
                            'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                          }
              })

    .then(function (data) {
                            return data.text();
                          })
    .then(function (html) {

      // si il y'a déjà une instance InfiniteAjaxScroll (visite d'une autre page tweet), on la vide

        if(iastweet)
      {
        iastweet = null;
      }

	  spinner.setAttribute('hidden', ''); // disparition du spinner

    document.getElementById("list_tweet_"+username+"").innerHTML = html; // chargement de la réponse dans la div précédente

    // création d'une nouvelle instance InfiniteAjaxScroll

    iastweet = new InfiniteAjaxScroll('.usertweets', {
      item: '.item',
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

    iastweet.on('last', function() {

      document.querySelector('.no-more').style.opacity = '1';
    })


    })

    // affichage d'erreur si besoin

    .catch(function(err) {
  	                       console.log(err);
  	});

}

// supprimer un tweet

document.addEventListener('click',function(e){

  if(e.target && e.target.className == 'deletetweet'){

      if(e.target.getAttribute('data_type') == 0) // pas un tweet partagé
    {
      URL = '/twittux/tweet/delete'; // URL de suppression d'une entité TWEET
    }
      else
    {
      URL = '/twittux/share/delete'; // URL de suppression d'une entité PARTAGE
    }

  var idtweet = e.target.getAttribute('data_idtweet');// on récupère l'id du commentaire associé au lien cliqué

    let response = fetch(URL, {
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(idtweet)
    })
.then(function(response) {
    return response.text(); // récupération des données au format texte
  })
    .then(function(Data) {

  if(Data == 'tweetnonsupprime') // impossible de supprimer de tweet
{
          alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de supprimer ce tweet.</p>'+
                    '</div>.');
}
  else if(Data == 'tweetsupprime')
{

var divtweet = document.querySelector('#tweet'+idtweet); // on récupère la div contenant le tweet

divtweet.parentNode.removeChild(divtweet); // suppression de la div contenant le tweet

//notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Tweet supprimé.</p>'+
                    '</div>.');

}
    }).catch(function(err) {

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de supprimer ce tweet.</p>'+
                    '</div>.');

    });
       }
})

// ABONNEMENT

// crée / supprimer un abonnement

document.addEventListener('click',function(e){

  if(e.target && e.target.className == 'follow'){

    var action = e.target.getAttribute('data_action'); // add -> crée un abonnement, cancel -> annuler une demande d'abonnement, delete -> supprimer un abonnement

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
    return response.json(); // récupération des données au format json
  })
    .then(function(Data) {

  switch(Data.Result)
{

    // ajout d'un abonnement

    case "abonnementajoute": alertbox.show('<div class="w3-panel w3-green">'+ // notification
                                        '<p>Abonnement ajouté.</p>'+
                                        '</div>.');

    // nouveau bouton de suppression d'abonnement

    zone_abo.innerHTML = '<button class="w3-button w3-red w3-round"><a class="follow" href="#" onclick="return false;" data_action="delete" data_username="'+data.username +'"><i class="fas fa-user-minus"></i> Ne plus suivre</a></button>';

    break;

    // impossible d'ajouter un abonnement

    case "abonnementnonajoute": alertbox.show('<div class="w3-panel w3-red">'+ // notification
                                        '<p>Impossible d\'ajouter cet abonnement.</p>'+
                                        '</div>.');

    //suppression d'un abonnement

    case "abonnementsupprime": alertbox.show('<div class="w3-panel w3-green">'+
                              '<p>Abonnement supprimer.</p>'+
                              '</div>.');

    // nouveau bouton d'abonnement

    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

    // nouveau bouton pour annuler une demande d'abonnement

    zone_abo.innerHTML = '<button class="w3-button w3-orange w3-round"><a class="follow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'"><i class="fas fa-user-times"></i> Annuler</a></button>';

    break;

    //annulation d'une demande d'abonnement

    case "demandeannule": alertbox.show('<div class="w3-panel w3-green">'+
                          '<p>Demande d\'abonnemment annulée.</p>'+
                          '</div>.');

    // nouveau bouton pour s'abonner

    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

/** notifications **/

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

/** fin affichage de notifications **/

/** LIKE **/

/** traitement des like **/

// ajout/suppression like

document.addEventListener('click',function(e){

  if(e.target && e.target.getAttribute('data_action') == 'like'){

    var idtweet = e.target.getAttribute('data_id_tweet');

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

    // ajout d'un like -> mise à jour du nombre de like

    case "addlike": document.querySelector('.nb_like_'+idtweet).textContent ++;

    break;

    // suppression d'un like -> mise à jour du nombre de like

    case "dislike": document.querySelector('.nb_like_'+idtweet).textContent --;

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

/** fin traitement des like **/

/** PARTAGE **/

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

    // ajout d'un partage -> mise à jour du nombre de partage

    case "addshare": document.querySelector('.nb_share_'+data.idtweet).textContent ++;

                      alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Post partagé.</p>'+
                    '</div>.');

    break;

    // suppression d'un like -> mise à jour du nombre de like

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

/** fin traitement des partages **/

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

      // utilisateur bloqué

      if(Data.Result == "addblock")
     {

       // affichage notification

       alertbox.show('<div class="w3-panel w3-green">'+
                     '<p>Utilisateur bloqué.</p>'+
                     '</div>.');

      // mise à jour du bouton de blocage

        zone_blocage.innerHTML = '<button class="w3-button w3-black w3-round"><a class="deblockuser" href="" onclick="return false;" data_username="'+ data.username+'"><i class="fas fa-unlock"></i> Débloquer </a></button>';

     }

       // utilisateur déjà bloqué

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

      // mise à jour du bouton de blocage

        zone_blocage.innerHTML = '<button class="w3-button w3-black w3-round"><a class="blockuser" href="" onclick="return false;" data_username="'+ data.username+'"><i class="fas fa-lock"></i> Bloquer </a></button>';

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

/** MESSAGERIE **/

// au click sur le bouton, on redirige vers une action du controlleur qui và vérifier si j'ai déjà une conversation avec la personne
// si oui on redirige vers cette conversation sinon on redirige vers la messagerie avec le destinataire pré-remplie

document.addEventListener('click',function(e){

    if(e.target && e.target.className == 'sendmessage') // clique sur le bouton avec la classe 'sendmessage'
  {

    var data = {
                "username": e.target.getAttribute('data_username') // username de la personne à qui je veut envoyer un message
                }

    let response = fetch('/twittux/messagerie/messagefromprofil', { // on ajoute l'id à l'URL
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authentifier mon action
                },
                method: "POST",

      body: JSON.stringify(data)
    })
.then(function(response) {
    return response.json(); // récupération des données au format texte
  })
    .then(function(Data) {

      // Data.new_conv == 1 -> conversation existante

      if(Data.new_conv == 1)
     {

       // création d'un item localStorage avec l'identifiant de la conversation

       localStorage.setItem("idconv", Data.conversation);

     }

       // Data.new_conv == 0 -> pas de conversation existante

       else if (Data.new_conv == 0)
      {

        // création d'un item localStorage avec l'identifiant de la personne à qui je veut envoyer un message

      localStorage.setItem("username", Data.username);

      }

  //redirection vers la messagerie

  window.location.href = '/twittux/messagerie';

    }).catch(function(err) {

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de rejoindre cette conversation.</p>'+
                    '</div>.');

    });

}
})
