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

const navAnchor = document.querySelectorAll('.tablink'); // liste de tous les liens du menu pour permettre de surligner le lien actif

// surlignage

// ajout d'un écouteur de clique sur chaque lien du menu

navAnchor.forEach(anchor => {
  anchor.addEventListener('click', addActive);
})

// on enlève la classe w3-red à l'item qui la possède pour la donner à l'élkément cliqué

function addActive(e) {
  const current = document.querySelector('.w3-red');
  current.className = current.className.replace("w3-red", "");
  e.target.className += " w3-red";
}

// chargement de donné via lien

document.addEventListener('click',function(e){

var URL; // URL de rercherche à charger suivant l'onglet cliqué

  		switch(e.target.id)
  	{
  		case "searchusers": // recherche d'utilisateurs
                          if(currenturl === 'search') // page de recherche classiqie
                        {
  							           URL = '/twittux/search/users/'+keyword+'';
                        }
                          else // page de recherche hashtag sur la description
                        {

                          keyword = keyword.replace(regexp, '$1');

                          URL = '/twittux/search/hashtag/users/'+keyword+'';

                        }

  							break;

  		case "searchtweets": // recherche dans les tweets
                          if(currenturl === 'search') // page de recherche classiqie
                        {
  							           URL = '/twittux/search/'+keyword+'';
                        }
                          else // page de recherche hashtag sur le contenu des tweets
                        {

                          keyword = keyword.replace(regexp, '$1');

                          URL = '/twittux/search/hashtag/'+keyword+'';

                        }

  							break;

  		case "searchmostrecent": // tri sur la date des tweets (les plus récents)
                              if(currenturl === 'search') // page de recherche classiqie
                            {
  							               URL = '/twittux/search/'+keyword+'?sort=created&direction=desc';
                            }
                              else // page de recherche hashtag sur le contenu des tweets
                            {
                              keyword = keyword.replace(regexp, '$1');

                              URL = '/twittux/search/hashtag/'+keyword+'?sort=created&direction=desc';
                            }
  							break;

      case "searchmediapics": // tweets avec média
                              if(currenturl === 'search') // page de recherche classiqie
                            {
            							      URL = '/twittux/search/media/'+keyword+'';
                            }
                              else // page de recherche hashtag sur le contenu des tweets
                            {
                              keyword = keyword.replace(regexp, '$1');

                              URL = '/twittux/search/hashtag/media/'+keyword+'';
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

      document.getElementById("result_search").innerHTML = html; // chargement de la réponse dans la div précédente

    })

    // affichage d'erreur si besoin

    .catch(function(err) {
  	                       console.log(err);
  	});

})

// traitement des actions d'abonnement/demande/suppression sur les résultats utilisateurs du moteur de recherche

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

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-red w3-round"><a class="follow" href="#" onclick="return false;" data_action="delete" data_username="'+ data.username +'"><i class="fas fa-user-minus"></i> Ne plus suivre</a></button>';

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

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-orange w3-round"><a class="follow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'"><i class="fas fa-user-times"></i> Annuler</a></button>';

    break;

    //annulation d'une demande d'abonnement

    case "demandeannule": alertbox.show('<div class="w3-panel w3-green">'+
                          '<p>Demande d\'abonnemment annulée.</p>'+
                          '</div>.');

    // bouton pour suivre ultérieurement

    document.querySelector('.zone_abo[data_username="'+ data.username+'"]').innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

// affichage modal des likes

  function openmodallike(idtweetlike)
{

  if(document.getElementById('modallike')) // si la modal existe car inexistante lors de recherche en étant pas auth
{
  document.getElementById('modallike').style.display='block'; // affichage de la fenêtre modale
}
  else
{
    return;
}

  fetch('/twittux/like/'+idtweetlike+'') // chargement de l'URL
  .then(function (data)
  {
    return data.text();
  })
  .then(function (html)
  {
    document.getElementById("contentlike").innerHTML = html; // affichage du contenu de la page dans la div prévue
  })
  .catch((err) => console.log("fail" + err));
}

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
