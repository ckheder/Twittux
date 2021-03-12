// news.js
// Gestion des actions sur la page d'actualités
//

//** VARIABLE **//

const navAnchor = document.querySelectorAll('.tablinknews'); // liste de tous les liens du menu pour permettre de surligner le lien actif

const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

//**NAVIGATION **//

// surlignage

// ajout d'un écouteur de clique sur chaque lien du menu

navAnchor.forEach(anchor => {
  anchor.addEventListener('click', addActive);
})

// on enlève la classe w3-red à l'item qui la possède pour la donner à l'élkément cliqué

function addActive(e) {
  const current = document.querySelector('.tablinknews.w3-red');
  current.className = current.className.replace("w3-red", "");
  e.target.className += " w3-red";
}

// naviguer entre les tweet et les tweets avec media

document.addEventListener('click',function(e){

  var url_news; // URL de rercherche à charger suivant l'onglet cliqué

    if(e.target.id == 'showtmostrecentweets') // URL d'affichage de tous les tweets les plus récents
  {

    url_news = '/twittux/actualites?sort=created&direction=desc';

  }
    else if (e.target.id === 'showtmostcommentsweets') // URL d'affichage de tous les tweets les plus commenté
  {

    url_news = '/twittux/actualites?sort=nb_commentaire&direction=desc';

  }
    else
  {
      return;
  }

  	document.getElementById("list_actu_online").innerHTML = ""; // on vide la div d'affichage des tweets

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

    document.getElementById("list_actu_online").innerHTML = html; // chargement de la réponse dans la div précédente

    })

    // affichage d'erreur si besoin

    .catch(function(err) {
  	                       console.log(err);
  	});
})

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
    return response.json(); // récupération des données au format JSON
  })
    .then(function(Data) {

  switch(Data.Result)
{

    //abonnement supprimé

    case "abonnementsupprime": alertbox.show('<div class="w3-panel w3-green">'+
                              '<p>Abonnement supprimer. Les posts de '+ data.username+' ne s\'afficheront plus.</p>'+
                              '</div>.');

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
  document.getElementById('modallike').style.display='block'; // affichage de la fenêtre modale

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
