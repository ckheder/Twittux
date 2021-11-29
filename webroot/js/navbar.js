// navbar.js
// Gestion des actions de la navbar : autocomplete, responsive
//

//variable

var searchInput = document.querySelector('.input-search'); // input de recherche

var autocomplete_zone = document.getElementById("autocomplete-results"); // zone des résultats

var min_characters = 0; // nombre de caractère minimum : on déclenche l'appel AJAX avec 1 caractère minimum

var titlepage = document.querySelector('title'); // récupération du titre de la page

const socket = io("http://localhost:8082"); // connexion à Node JS avec Socket IO

// detection mobile

    var hasTouchScreen = false;
    if ("maxTouchPoints" in navigator) {
        hasTouchScreen = navigator.maxTouchPoints > 0;
    } else if ("msMaxTouchPoints" in navigator) {
        hasTouchScreen = navigator.msMaxTouchPoints > 0;
    } else {
        var mQ = window.matchMedia && matchMedia("(pointer:coarse)");
        if (mQ && mQ.media === "(pointer:coarse)") {
            hasTouchScreen = !!mQ.matches;
        } else if ('orientation' in window) {
            hasTouchScreen = true; // deprecated, but good fallback
        } else {
            // Only as a last resort, fall back to user agent sniffing
            var UA = navigator.userAgent;
            hasTouchScreen = (
                /\b(BlackBerry|webOS|iPhone|IEMobile)\b/i.test(UA) ||
                /\b(Android|Windows Phone|iPad|iPod)\b/i.test(UA)
            );
        }
    }

// Recherche de nouvelles notifications au changement de page

  if (hasTouchScreen === false) // je ne suis pas sur mobile
{
  var nbunreadnotif = document.getElementById('desktopscreensnav').querySelector('.nbunreadnotif'); // nombre de notification non lue sur le badge rouge de la navbar desktop
}
  else if(hasTouchScreen === true) // je suis sur mobile
{
  var nbunreadnotif = document.getElementById('smallscreensnav').querySelector('.nbunreadnotif'); // nombre de notification non lue sur le badge rouge de la navbar mobile
}

// appel de la fonction checkunreadnotif() au chargement de la page

  nbunreadnotif.addEventListener("load", checkunreadnotif());

  // Recherche toutes les notifications non lues

  function checkunreadnotif()
{
  fetch('/twittux/notifications/unreadnotif', { // URL de recherche

    headers: {
                'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
              }
  })
  .then(function (data) {
    return data.text();
  })
.then(function (Data) {

    if(Data > 0) // si j'ai plus d'une notification non lue
  {

      if (hasTouchScreen === false) // version Desktop
    {
      // on retire le nombre de notifications précédents

      titlepage.textContent = titlepage.textContent.replace(/ *\([^)]*\) */g, "");

      // on ajoute sur le titre de la page le nombre de notifications non lues

      titlepage.textContent  = "(" + Data + ")" + titlepage.textContent;

    }
      else // version mobile : apparition d'une pastille rouge sur le menu mobile
    {
      document.querySelector('.dot').style.display='inline-block'; // apparition d'un rond rouge sur le menu déroulant pour signaler de nouvelles notifications non lues
    }

    nbunreadnotif.textContent = Data; // affichage sur le badge du nombre de notification non lue

  }
    else // 0 notifications
  {
      if (hasTouchScreen === true) // version mobile
    {
      document.querySelector('.dot').style.display='none'; // on efface le rond rouge
    }

      else // version Desktop
    {
      titlepage.textContent = titlepage.textContent.replace(/ *\([^)]*\) */g, "");
    }

    // on supprime le nombre de notifications non lues du titre de la page

    nbunreadnotif.innerHTML = ''; // on efface le badge rouge

  }

})

};

// Affichage du menu sur les petits résolutions en cliquant sur la bouton

  function openNav()
{
  var x = document.getElementById("smallscreensnav");

  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
  } else {
    x.className = x.className.replace(" w3-show", "");
  }
}

// si on clique en dehors de la liste de résultat, on la masque et on la vide

window.addEventListener("click", function(event) {
  if (!event.target.matches('.resultsearch')) {
    document.querySelector('.input-search').value='';
autocomplete_zone.style.display='none';
  }
});

// ## NODE JS ## //

// nouvelle notification //

  socket.on('newnotif', function()
{

    if(nbunreadnotif.innerHTML) // si  il y'a minimum 1 notification non lue
  {
    nbunreadnotif.textContent ++;
  }
    else // si il n'y a aucune notification
  {
    nbunreadnotif.innerHTML = 1;
  }

  // mise à jour du titre de la page

    // on retire le nombre de notifications précédents

    titlepage.textContent = titlepage.textContent.replace(/ *\([^)]*\) */g, "");

    // on ajoute sur le titre de la page le nombre de notifications non lues

    titlepage.textContent  = "(" + nbunreadnotif.textContent + ")" + titlepage.textContent;

    // si je suis sur la page des notifications et qu'il n'y as pas déjà la div messagenewnotif, on affiche cette dernière pour proposer de recharger la liste des notifications avec les nouvelles

      if(document.querySelector('#listnotif') && !document.querySelector('.messagenewnotif'))
    {
      document.querySelector('.displaymessagenewnotif').insertAdjacentHTML('afterbegin', '<div class="w3-panel w3-light-grey w3-display-container messagenewnotif">'+
                                                                            '<span onclick="this.parentElement.remove()"'+
                                                                            'class="w3-button w3-large w3-display-topright">x</span>'+
                                                                            '<p class="w3-center"><i class="fa fa-bell"></i> Vous avez de nouvelles notifications.<br />'+
                                                                            '<button class="w3-button w3-round w3-border w3-border-black" onclick="loadnotif()">Afficher</button></p>'+
                                                                            '</div>');

    }

});



// ## FIN NODE JS ## //

// autocomplétion

searchInput.addEventListener('keyup', displayMatches); // on déclenche l'évènement après chaque pression de touche

function displayMatches() {

	   if (searchInput.value.length == min_characters ) // si le nombre de caractère est égal à 0 , on vide la liste des résultats
    {
	  	autocomplete_zone.innerHTML='';

      return;
    }

    else if(searchInput.value.startsWith("#")) // si le premier terme tapé commence par #

    {
        if(searchInput.value.length >= 2) // on vérifie qu'il y'a minimum 2 caractères entrés
      {

        autocomplete_zone.innerHTML=''; // on vide la liste de recherche

        var urlvar = searchInput.value.replace("#", "%23"); // on remplace # par %23

      //ajout d'un lien vers une recherche plus complète avec hashtag

        autocomplete_zone.innerHTML += '<li class="resultsearch"><strong><a href="/twittux/search/hashtag/'+urlvar+'">'+searchInput.value+'</a></strong></li>';

      //affichage de la liste des résultats

      autocomplete_zone.style.display = 'block';

      }
      else
      {
        autocomplete_zone.innerHTML=''; // on ne retourne rien

        return;
      }

    }
      else
    {

      let response = fetch('/twittux/searchusers-'+searchInput.value+'', { // on ajoute la valeur de l'input comme terme de la recherche
      headers: {
                  'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                }
    })
      .then(function(response) {

    return response.json(); // récupération des données en JSON
  })
      .then(function(jsonData) {

    	autocomplete_zone.innerHTML=''; // on vide la liste de recherche

    		if(jsonData == 'noresult') // si réception d'une valeur 'noresult'
    	{
    		autocomplete_zone.innerHTML += '<li class="resultsearch"><img src="/twittux/img/default.png" alt="image utilisateur" class="w3-circle" width="23" height="23"> Aucun utilisateur trouvé</li>'; // affichage d'un message
    	}
    		else
    	{

    	   jsonData.forEach(function(item) //pour chaque résultat, on crée un nouvel element <li> avec l'avatar de la personne plus un lien vers le profil
       {

    	   autocomplete_zone.innerHTML += '<li class="resultsearch"><a href="/twittux/'+item.username+'"><img src="/twittux/img/avatar/'+item.username+'.jpg" alt="image utilisateur" class="w3-circle" width="23" height="23"> '+item.username+'</a></li>';

        }

        )};

//ajout à la fin d'un lien vers une recherche plus complète, notamment les tweets

    	autocomplete_zone.innerHTML += '<li class="resultsearch"><a href="/twittux/search/'+searchInput.value+'">Recherche complète pour '+searchInput.value+'</a></li>';

//affichage de la liste des résultats

    	autocomplete_zone.style.display = 'block';

    }).catch(function(err) {

// notification d'échec

    	  alertbox.show('<div class="w3-panel w3-red">'+
  										'<p>Problème lors de la recherche.</p>'+
										'</div>.');

    });

}
}

// redirection vers la page des résultats de recherche après appuie sur "entrée" (searchInput -> desktop, searchInputResp ->mobile)

  searchInput.addEventListener('keydown', function (e) {

    if (e.keyCode === 13) {

 // si pression sur la touche entrée

        if (searchInput.value.length == min_characters ) // si le nombre de caractère est égal à 0 , on affiche un message
    {

      alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Recherche trop courte.</p>'+
                    '</div>.');

    }
        else if(searchInput.value.startsWith("#")) // si la recherche commence par #

    {
        if(searchInput.value.length >= 2) // 2 caractères minimum
      {
        searchInput.value = searchInput.value.replace("#", "%23");

        window.location.href = '/twittux/search/hashtag/'+searchInput.value+''; // on redirige vers la recherche hashtag
      }
        else
      {
        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Recherche trop courte.</p>'+
                    '</div>.');
      }

    }

    else
    {
      window.location.href = '/twittux/search/'+searchInput.value+''; // on redirige vers la recherche classique
    }
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
