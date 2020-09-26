// navbar.js
// Gestion des actions de la navbar : autocomplete, responsive
//


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

// autocomplétion

var searchInput = document.querySelector('.input-search'); // input de recherche

var searchInputResp = document.querySelector('.input-search-resp'); // input de recherche

var autocomplete_zone = document.getElementById("autocomplete-results"); // zone des résultats

var min_characters = 0; // nombre de caractère minimum : on déclenche l'appel AJAX avec 1 caractère minimum

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

        autocomplete_zone.innerHTML += '<li><strong><a href="/twittux/search/hashtag/'+urlvar+'">'+searchInput.value+'</a></strong></li>';

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
    		autocomplete_zone.innerHTML += '<li>Aucun résultat</li>'; // affichage d'un message
    	}
    		else
    	{

    	   jsonData.forEach(function(item) //pour chaque résultat, on crée un nouvel element <li> avec l'avatar de la personne plus un lien vers le profil
       { 

    	   autocomplete_zone.innerHTML += '<li><a href="/twittux/'+item.username+'"><img src="/twittux/img/avatar/'+item.username+'.jpg" alt="image utilisateur" class="w3-circle" width="23" height="23"> '+item.username+'</a></li>';
          
        }

        )};

//ajout à la fin d'un lien vers une recherche plus complète, notamment les tweets

    	autocomplete_zone.innerHTML += '<li><a href="/twittux/search/'+searchInput.value+'">Recherche complète pour '+searchInput.value+'</a></li>';

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

document.querySelector('.testrest').addEventListener('submit', function (e) {

  e.preventDefault();
  });

// redirection vers la page des résultats de recherche après appuie sur "entrée" (searchInput -> desktop, searchInputResp ->mobile)

[searchInput, searchInputResp].forEach(item => {

  item.addEventListener('keydown', function (e) {

    if (e.keyCode === 13) {

 // si pression sur la touche entrée

        if (item.value.length == min_characters ) // si le nombre de caractère est égal à 0 , on affiche un message 
    { 

      alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Recherche trop courte.</p>'+
                    '</div>.');

    }
        else if(item.value.startsWith("#")) // si la recherche commence par #

    {
        if(item.value.length >= 2) // 2 caractères minimum
      {
        item.value = item.value.replace("#", "%23");

        window.location.href = '/twittux/search/hashtag/'+item.value+''; // on redirige vers la recherche hashtag
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
      window.location.href = '/twittux/search/'+item.value+''; // on redirige vers la recherche classique
    }
  }

})

});