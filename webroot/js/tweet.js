/**
 * tweet.js
 *
 * Gestion des tweet, abonnement à un profil en cours de visite, affichage de notifications,like
 *
 */

  const menuemoji = document.getElementById("menuemoji"); //div contentant la liste des emojis
  const textarea_tweet = document.querySelector('#textarea_tweet'); // textarea de rédaction d'un tweet
  const zone_abo = document.querySelector('#zone_abo'); // zone contentna t les boutons d'abonnement, suppression ou demande
  var URL; // URL à atteindre suivant le type de suppression d'un tweet : tweet personnel ou tweet partagé

 //menu déroulant tweet

 function openmenutweet(id) {

    document.getElementById("btntweet"+id).classList.toggle("show");
}

//emoji

//ouverture menu

function openemojimenu() {

 if (menuemoji.className.indexOf("w3-show") == -1) {
        menuemoji.className += " w3-show";
    } else {
        menuemoji.className = menuemoji.className.replace(" w3-show", "");
    }
}

//ajout au textarea

document.addEventListener('click',function(e){
  // récupération élément
  if(e.target && e.target.className == 'emoji'){
    var code = e.target.getAttribute('data_code');
    //suppression de l'extension du fichier
    code  = code.replace(/\.[^/.]+$/, "");
    code = ' :'+code+': ';
    //ajout au textarea
  textarea_tweet.value += code;
  menuemoji.className = menuemoji.className.replace(" w3-show", "");
  textarea_tweet.focus();
}
});

// restriction et compteur de caractère tweet : 255 caractères

function countCharacters(e) {
  var textEntered, countRemaining, counter;
  textEntered = textarea_tweet.value;
  counter = (255 - (textEntered.length));
  countRemaining = document.getElementById('charactersRemaining');
  countRemaining.textContent = counter + ' caractère(s) restant(s)';
}

const el = document.getElementById('textarea_tweet');
el.addEventListener('keydown', countCharacters);

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
//ajout d'un tweet

let form_tweet = document.querySelector('#form_tweet') // récupération du formulaire

let button_submit_tweet = form_tweet.querySelector('button[type=submit]') // récupération du bouton d'envoi

let buttonTextSubmitTweet = button_submit_tweet.textContent // récupération du texte du bouton

form_tweet.addEventListener('submit', async function (e) { // on capte l'envoi du formulaire

      e.preventDefault();

// on vérifie si le texte envoyé est supérieur à 255

  if(document.getElementById('textarea_tweet').value.length > 255)
{
  alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>255 caractères maximum.</p>'+
                    '</div>.');
  return;
}


  button_submit_tweet.disabled = true // désactivation du bouton

  button_submit_tweet.textContent = 'Chargement...' // mise à jour du texte du bouton

    let data = new FormData(this) // on récupère les données du formulaire

    let response = await fetch(form_tweet.getAttribute('action'), { // on récupère l'URL d'envoi des données
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

var el = document.getElementById("list_tweet"); // récupération de la div ou l'on va insérer le nouveau tweet

//insertion du nouveau tweet au tout début de la div

el.insertAdjacentHTML('afterbegin', '<div class="w3-container w3-card w3-white w3-round w3-margin"  id="tweet'+ jsonData.id_tweet+'"><br>'+
        		'<img src="/twittux/img/avatar/'+ jsonData.username+'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/>'+
            '<div class="dropdown">'+
            '<button onclick="openmenutweet('+ jsonData.id_tweet+')" class="dropbtn">...</button>'+
            '<div id="btntweet'+ jsonData.id_tweet+'" class="dropdown-content">'+
            '<a class="deletetweet" href="#" onclick="return false;" data_idtweet="'+ jsonData.id_tweet+'"> Supprimer</a>'+
            '</div>'+
            '</div>'+
        		'<h4>'+ jsonData.username+'</h4>'+
            '<span class="w3-opacity">à l\'instant</span>'+
        		'<hr class="w3-clear">'+
        		'<p>'+ jsonData.contenu_tweet+'</p>'+
            '<hr class="w3-clear">'+
            '<span class="w3-opacity"> <a onclick="openmodallike('+ jsonData.id_tweet+')" style="cursor: pointer;"><span class="nb_like_'+ jsonData.id_tweet+'">0</span>'+
            'J\'aime</a> - 0 Commentaire(s) - Partagé <span class="nb_share_'+ jsonData.id_tweet+'">0</span> fois</span>'+
            '<hr><p>'+
            '<a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="like" data_id_tweet="'+ jsonData.id_tweet+'"><i class="fa fa-thumbs-up"></i> J\'aime</a>\xa0\xa0\xa0'+
        		'<a href="./statut/'+ jsonData.id_tweet+'" class="w3-margin-bottom"><i class="fa fa-comment"></i> Commenter</a>'+
            '</p>'+
      			'</div>');

//on vide la formulaire

form_tweet.reset()

//reset du nombre de caractère restants

document.getElementById('charactersRemaining').textContent = '255 caractère(s) restant(s)';

//notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
  										'<p>Tweet posté.</p>'+
										'</div>.');


    }).catch(function(err) {

// notification d'échec

    	  alertbox.show('<div class="w3-panel w3-red">'+
  										'<p>Impossible de poster ce tweet.</p>'+
										'</div>.');

    });
  button_submit_tweet.disabled = false // on réactive le bouton
  button_submit_tweet.textContent = buttonTextSubmitTweet// on remet le texte initial du bouton
})

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

    zone_abo.innerHTML = '<button class="w3-button w3-red w3-round"><a class="follow" href="#" onclick="return false;" data_action="delete" data_username="'+data.username +'">Ne plus suivre</a></button>';

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

    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'">Suivre</a></button>';

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

    zone_abo.innerHTML = '<button class="w3-button w3-orange w3-round"><a class="follow" href="#" onclick="return false;" data_action="cancel" data_username="' + data.username +'">Annuler</a></button>';

    break;

    //annulation d'une demande d'abonnement

    case "demandeannule": alertbox.show('<div class="w3-panel w3-green">'+
                          '<p>Demande d\'abonnemment annulée.</p>'+
                          '</div>.');

    // nouveau bouton pour s'abonner

    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="follow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'">Suivre</a></button>';

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

    var idtweet = e.target.getAttribute('data_id_tweet');

    let response = fetch('/twittux/share', {
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

    // ajout d'un partage -> mise à jour du nombre de partage

    case "addshare": document.querySelector('.nb_share_'+idtweet).textContent ++;

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
