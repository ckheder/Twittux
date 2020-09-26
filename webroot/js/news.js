// news.js
// Gestion des actions sur la page d'actualités
//

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

