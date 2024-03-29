/**
 * tweet.js
 *
 * Gestion des tweet, abonnement à un profil en cours de visite, affichage de notifications,like
 *
 */

  var zone_abo = document.querySelector('#zone_abo'); // zone contenant les boutons d'abonnement, suppression ou demande

  const zone_blocage = document.querySelector('#zone_blocage'); // zone contentant les boutons de blocage : ajout ou suppression

  var URL; // URL à atteindre suivant le type de suppression d'un tweet : tweet personnel ou tweet partagé

  const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

  const navAnchor = document.querySelectorAll('.tablinktweet'); // liste de tous les liens du menu pour permettre de surligner le lien actif

  let iastweet; // variable contenant la construction de l'Infinite Ajax Scroll pour les tweets et les like

  var url_tweet; // URL de recherche à charger suivant l'onglet cliqué

  // ## Node Js ## //

  // connexion au serveur Node JS

  socket.emit("connexion", {rooms: username, authname: authname}); // on transmet mon username et le profil courant au serveur

  // ajout d'un tweet

  socket.on('addtweet', function(data)
{
      if(document.querySelector("#list_tweet_" + data.Tweet['username'])) // je suis sur une page de profil
    {

    if(data.Tweet['username'] == authname) // je suis sur mon profil et je suis l'auteur du tweet
  {

    // insertion du tweet pour moi

      document.querySelector(".usertweets").insertAdjacentHTML('afterbegin', '<div class="w3-container w3-card w3-white w3-round w3-margin"  id="tweet'+ data.Tweet['id_tweet']+'"><br>'+
            '<img src="/twittux/img/avatar/'+ data.Tweet['username']+'.jpg" alt="image utilisateur" class="w3-left w3-circle w3-margin-right" width="60"/>'+
            '<div class="dropdown">'+
            '<button onclick="openmenutweet('+ data.Tweet['id_tweet']+')" class="dropbtn">...</button>'+
            '<div id="btntweet'+ data.Tweet['id_tweet']+'" class="dropdown-content">'+
            '<a class="deletetweet" href="#" onclick="return false;" data_type = "0" data_idtweet="'+ data.Tweet['id_tweet']+'"> Supprimer</a>'+
            '</div>'+
            '</div>'+
            '<h4>'+ data.Tweet['username']+'</h4>'+
            '<span class="w3-opacity">à l\'instant</span>'+
            '<hr class="w3-clear">'+
            '<p>'+ data.Tweet['contenu_tweet']+'</p>'+
            '<hr class="w3-clear">'+
            '<span class="w3-opacity"> <a class="modallike_'+data.Tweet['id_tweet']+'"><span class="nb_like_'+ data.Tweet['id_tweet']+'">0</span>'+
            ' J\'aime</a> - 0 Commentaire(s) - Partagé <span class="nb_share_'+ data.Tweet['id_tweet']+'">0</span> fois</span>'+
            '<hr><p>'+
            '<a class="w3-margin-bottom" onclick="return false;" style="cursor: pointer;" data_action="like" data_id_tweet="'+ data.Tweet['id_tweet']+'"><i class="fa fa-thumbs-up"></i> J\'aime</a>\xa0\xa0\xa0'+
            '<a href="./statut/'+ data.Tweet['id_tweet']+'" class="w3-margin-bottom"><i class="fa fa-comment"></i> Commenter</a>'+
            '</p>'+
            '</div>');
    }
      else
    {
        if(!document.querySelector('.messagenewtweet')) // si cette div n'existe pas , on affiche un message de nouveau tweet(testé à chaque fois en cas de plusieurs tweets à la suite)
      {
        document.querySelector('.displaymessagenewtweet').insertAdjacentHTML('afterbegin', '<div class="w3-panel w3-pale-green w3-display-container messagenewtweet">'+
                                                                              '<span onclick="this.parentElement.remove()"'+
                                                                              'class="w3-button w3-large w3-display-topright">x</span>'+
                                                                              '<p class="w3-center"><i class="fas fa-pen"></i> Nouveaux tweets de '+data.Tweet['username']+'.<br /><br />'+
                                                                              '<button class="w3-button w3-round w3-border w3-border-black" onclick="loadTweetItem(\'showtweets\')">Afficher</button></p>'+
                                                                              '</div>');
      }
    }
  }
})

// traitement hashtag

  socket.on('hashtag', function(data)
{

    // traitement des hashtags

      // on récupère les éventuels hashtags utilisés

        var hashtagarray = data.Hashtag;

      // on vérifie si, pour chaque hashtag, si il existe dans les encarts de hashtag(news, profil et page trending).

        hashtagarray.forEach(element =>
      {
        var hashtagitem = document.querySelector('#'+element+'');

          if(hashtagitem) // si le hashtag existe
        {
          //on incrémente le compteur de 1

          hashtagitem.querySelector('#'+element+' span[class="nbtweets"]').textContent ++;

          // on récupère l'élément au dessus du hashtag

          var prevhashtagitem = hashtagitem.previousElementSibling;

          // si cet élément est un paragraphe (donc le hashtag le plus populaire n'est pas utilisé)

            if(prevhashtagitem.tagName == 'P')
          {
              // si le nombre de tweets pour ce hashtag est supérieur à celui au dessus, on échange leur place

              if(hashtagitem.querySelector('.nbtweets').textContent > prevhashtagitem.querySelector('.nbtweets').textContent)
            {
              hashtagitem.parentNode.insertBefore(hashtagitem, prevhashtagitem);
            }
          }

        }
            else if(document.querySelector('.list_hashtag') || typeof hashtagitem !== "undefined") // si je suis sur la page trending et que le hashtag n'existe pas on le crée à la fin
          {

            document.querySelector('#spinnerajaxscroll').insertAdjacentHTML('beforebegin','<p class="itemhashtag" id="'+element+'">'+
                                '<strong>'+
                                '<a href="/twittux/search/hashtag/%23'+element+'" class="w3-text-blue">#'+element+'</a>'+
                                '</strong>'+
                                '<br />'+
                                '<span class="w3-opacity"><span class="nbtweets">1</span> Tweets</span>'+
                                '</p>');
          }

      }

      );

    })


// suppression d'un tweet

  socket.on('deletetweet', function(data)
{

  var divtweet = document.querySelector('#tweet'+data.idtweet); // on récupère la div contenant le tweet

  divtweet.parentNode.removeChild(divtweet); // suppression de la div contenant le tweet

})

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

// ajout d'un partage

socket.on('addshare', function(idtweet)
{
  if(document.querySelector('.nb_share_'+idtweet))
  {
    document.querySelector('.nb_share_'+idtweet).textContent ++;
  }
})
  
  // ## Fin Node JS ##//

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

	  spinner.setAttribute('hidden', ''); // disparition du spinner

    document.getElementById("list_tweet_"+username+"").innerHTML = html; // chargement de la réponse dans la div précédente

      if(document.querySelector('.usertweets')) // si cette div existe, on est sur un profil public ayant posté au moins 1 tweet
    {
        if(iastweet)  // si il y'a déjà une instance InfiniteAjaxScroll (visite d'une autre page tweet), on la vide
      {
        iastweet = null;
      }

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

  }

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

  socket.emit('userdeletetweet', {idtweet: idtweet, usertweet: authname});

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

  if(e.target && e.target.className == 'actionfollow'){

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

        if(document.querySelector('.zone_abo_like[data_username="'+ data['username']+'"]')) // traitement abonnement depuis la modale like
      {
        zone_abo = document.querySelector('.zone_abo_like[data_username="'+ data['username']+'"]')
      }

  switch(Data.Result)
{

    // ajout d'un abonnement

    case "abonnementajoute": alertbox.show('<div class="w3-panel w3-green">'+ // notification
                                            '<p>Abonnement ajouté.</p>'+
                                            '</div>.');

    // nouveau bouton de suppression d'abonnement

    zone_abo.innerHTML = '<button class="w3-button w3-red w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="delete" data_username="'+data.username +'"><i class="fas fa-user-minus"></i> Ne plus suivre</a></button>';

      if(Data.notifabo == 'oui') // si l'utilisateur accepte les notifications d'abonnement, on émet un évènement Node JS
    {
      socket.emit('newabo', data.username);
    }

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

    // nouveau bouton pour annuler une demande d'abonnement

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

    // nouveau bouton pour s'abonner

    zone_abo.innerHTML = '<button class="w3-button w3-blue w3-round"><a class="actionfollow" href="#" onclick="return false;" data_action="add" data_username="' + data.username +'"><i class="fas fa-user-plus"></i> Suivre</a></button>';

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

    // ajout d'un like -> 
    
    // émission d'un évènement serveur de nouveau like( add : new like) 

    case "addlike": socket.emit('like', {idtweet: idtweet, auttweet: username, action: 'add'});

    // mise à jour de l'icone de like qui passe au rouge

    e.target.parentNode.innerHTML = '<i class="fas fa-heart w3-margin-bottom" data_id_tweet="'+ idtweet+'" data_action="like" style="color: red; cursor: pointer"></i>';

    break;

    // suppression d'un like

    // émission d'un évènement serveur de nouveau like( remove : dislike)

    case "dislike": socket.emit('like', {idtweet: idtweet, auttweet: username, action: 'remove'});

    // mise à jour de l'icone de like qui passe au vide

    e.target.parentNode.innerHTML = '<i class="far fa-heart w3-margin-bottom" style="cursor: pointer" data_id_tweet="'+idtweet+'" data_action="like"></i>';

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

    // ajout d'un partage -> émission d'un évènement serveur de nouveau partage

    case "addshare": alertbox.show('<div class="w3-panel w3-green">'+
                                    '<p>Post partagé.</p>'+
                                    '</div>.');


      socket.emit('newshare', {auttweet: data.auttweet,idtweet: data.idtweet, notifshare: Data.notifshare});

      e.target.parentNode.innerHTML = '<i title="Vous avez déjà partagé ce tweet" class="fas fa-share-square" style="color: #4F50F8;"></i>';

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
