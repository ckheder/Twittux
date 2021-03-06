// news.js
// Gestion des actions sur la page d'actualités
//

//** VARIABLE **//


const navAnchor = document.querySelectorAll('.tablinknews'); // liste de tous les liens du menu pour permettre de surligner le lien actif

const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

let iasnews; // variable contenant la construction de l'Infinite Ajax Scroll

let url_news; // URL charger suivant l'onglet cliqué : news les plus récentes ou news les plus commentés

let zone_abo = null; // variable utilisée pour contenir une div existant dans la fenêtre modale pour mettre à jour le bouton d'abonnement

//**NAVIGATION **//

// surlignage

// ajout d'un écouteur de clique sur chaque lien du menu

navAnchor.forEach(anchor => {
  anchor.addEventListener('click', addActive);
})

// on enlève la classe w3-red à l'item qui la possède pour la donner à l'élément cliqué

function addActive(e) {
  const current = document.querySelector('.tablinknews.w3-red');
  current.className = current.className.replace("w3-red", "");
  e.target.className += " w3-red";
}

// chargement de la page d'actualites au clique sur la page

  document.querySelector(".onlinenews").addEventListener("load", loadNewsItem('showtmostrecentweets'));

// naviguer entre les tweet et les tweets avec media

  function loadNewsItem(item)
{

    if(item == 'showtmostrecentweets') // URL d'affichage de tous les tweets les plus récents
  {

    url_news = '/twittux/actualites?sort=created&direction=desc';

  }
    else if (item === 'showtmostcommentsweets') // URL d'affichage de tous les tweets les plus commenté
  {

    url_news = '/twittux/actualites?sort=nb_commentaire&direction=desc';

  }
    else
  {

    return;

  }

    spinner.removeAttribute('hidden'); // affichage du spinner de chargement

    fetch(url_news, { // URL à charger dans la div précédente

                headers: {
                            'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                          }
              })

    .then(function (data) {
                            return data.text();
                          })
    .then(function (html) {

	   spinner.setAttribute('hidden', ''); // disparition du spinner

     window.scrollTo(0,0); // on retourne en haut de la page

     document.querySelector("#news").innerHTML = html; // chargement de la réponse dans la div précédente

    // Création de l'instance IAS si il y'a des résultats dans les news

    if(document.querySelector('.itemnews'))
  {

    iasnews = null;

    // création d'une nouvelle instance InfiniteAjaxScroll

    iasnews = new InfiniteAjaxScroll('.onlinenews', {
      item: '.itemnews',
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
          element.setAttribute('hidden', ''); // default behaviour
        }
      },
      pagination: '.pagination'
    });

     // action lors du chargement de toutes les données : affichage d'une div annoncant qu'il n'y a plus rien à charger

    iasnews.on('last', function() {

      document.querySelector('.no-more').style.opacity = '1';
    })

  }

    })

    // affichage d'erreur si besoin

    .catch(function(err) {
  	                       console.log(err);
  	});
}

//** TWEET **//

 // menu déroulant tweet

 function openmenutweet(id) {

    document.getElementById("btntweet"+id).classList.toggle("show");
}

// Fermeture du bouton si je clique hors du menu déroulant des tweets

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


// ** NOTIFICATIONS ** //

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


//** ABONNEMENT **//

document.addEventListener('click',function(e){

    if(e.target && e.target.className == 'follow') // clique sur un bouton pour ne plus suivre une personne
  {

        var action = e.target.getAttribute('data_action'); // follow -> crée un abonnement, delete -> supprimer un abonnement,cancel -> annuler une demande d'abonnement

        var data = {
                    "username": e.target.getAttribute('data_username') // username de la personne que je ne veut plus suivre
                    }

    let response = fetch('/twittux/abonnement/'+action+'', {

      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authnetifié mon action depuis le site
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


  switch(Data.Result)
{

  // ajout d'un abonnement

    case "abonnementajoute":

  // nouveau bouton

    zone_abo.innerHTML = '<button class="w3-button w3-red w3-round"><a class="follow" href="#" onclick="return false;" data_action="delete" data_username="'+ data.username +'"><i class="fas fa-user-minus"></i> Ne plus suivre</a></button>';

    break;

  // impossible d'ajouter un nouvel abonnement

    case "abonnementnonajoute": alertbox.show('<div class="w3-panel w3-red">'+ // notification
                                      '<p>Impossible d\'ajouter cet abonnement.</p>'+
                                      '</div>.');

    break;

    //abonnement supprimé

    case "abonnementsupprime":

    if(zone_abo !== null) // suppression d'un abonnement depuis la fenêtre modale des like
  {
    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';
  }
    else // affichage d'une alerte si on supprime depuis le menu déroulannt de l'actualitée avec bouton d'actualisation
  {
    alertbox.show('<div class="w3-panel w3-green">'+
                              '<p>Abonnement supprimé.<br /></p>'+
                              '<div class="w3-center w3-margin">'+
                              '<button class="w3-button w3-blue w3-round-large" onclick="loadNewsItem(\'showtmostrecentweets\')">Actualiser</button></div>'+
                              '</div>.');
  }


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

    case "demandeok":

    // bouton pour annuler ma demande d'abonnement

      zone_abo.innerHTML = '<button class="w3-button w3-orange w3-round"><a class="follow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'"><i class="fas fa-user-times"></i> Annuler</a></button>';

    break;

    //annulation d'une demande d'abonnement

    case "demandeannule":

    // bouton pour suivre ultérieurement

      zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

      console.log(err);

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Un problème est survenu lors du traitement de votre demande.Veuillez réessayer plus tard.</p>'+
                    '</div>.');

    });
       }
})

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

    // si le nombre de like vaut 0 (donc pas de fonction onclick() pour ouvrir la modale des like), on crée désormais un lien vers une modale contenant le nombre de like

    if( document.querySelector('.modallike_'+idtweet).onclick == null )
{
   document.querySelector('.modallike_'+idtweet).setAttribute('onclick', 'openmodallike('+idtweet+');');

   document.querySelector('.modallike_'+idtweet).style.cursor = "pointer";
}

    break;

    // suppression d'un like -> mise à jour du nombre de like

    case "dislike": document.querySelector('.nb_like_'+idtweet).textContent --;

    // si le nombre de like vaut 0 , on supprime la fonction onclick() qui ouvre la modale des likes

    if( document.querySelector('.nb_like_'+idtweet).textContent == 0 )
{
   document.querySelector('.modallike_'+idtweet).removeAttribute('onclick');

   document.querySelector('.modallike_'+idtweet).style.cursor = null;
}

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
