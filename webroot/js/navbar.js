// navbar.js
// Gestion des actions de la navbar : autocomplete, responsive
//

//variable

const evtSource = new EventSource("/twittux/notifications/unreadnotif"); // URL du controller en charge de calculer le nombre de notification non lue

var searchInput = document.querySelector('.input-search'); // input de recherche

var autocomplete_zone = document.getElementById("autocomplete-results"); // zone des résultats

var min_characters = 0; // nombre de caractère minimum : on déclenche l'appel AJAX avec 1 caractère minimum

var titlepage = document.querySelector('title'); // récupération du titre de la page

// detetction mobile

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

// actualisation du nombre de notifcations non lues

evtSource.onmessage = function (event) {

    if(event.data > 0) // si j'ai 1 notification minimum
  {

    if (hasTouchScreen === false) // je ne suis pas sur mobile
  {
    document.querySelector('.nbunreadnotif').innerHTML = event.data; // mise à jour du nombre de notification non lue sur le badge de la navbar
  }
    else // je suis sur mobile
  {
    document.querySelector('.nbunreadnotifres').innerHTML = event.data; // mise à jour du nombre de notification sur le badge rouge à côté du lien vers les notifications

    document.querySelector('.dot').style.display='inline-block'; // apparition d'un rond rouge sur le menu déroulant pour signaler de nouvelles notifications non lues
  }

// mise à jour du titre de la page

// on retire le nombre de notifications précédents

  titlepage.textContent = titlepage.textContent.replace(/ *\([^)]*\) */g, "");

// on ajoute sur le titre de la page le nombre de notifications non lues

  titlepage.textContent  = "(" + event.data + ")" + titlepage.textContent;

}
  else // 0 notifications
{
    if (hasTouchScreen === true) // je  suis sur mobile
  {

    document.querySelector('.nbunreadnotifres').innerHTML = ''; // on efface le badge rouge

    document.querySelector('.dot').style.display='none'; // on efface le rond rouge

  }

    else // je ne suis pas sur mobile
  {

    document.querySelector('.nbunreadnotif').innerHTML = ''; // on efface le badge rouge

  }

// on supprime le nombre de notifications non lues du titre de la page

  titlepage.textContent = titlepage.textContent.replace(/ *\([^)]*\) */g, "");

}

}

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
