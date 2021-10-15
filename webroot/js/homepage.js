/**
 * homepage.js
 *
 * Test des cookies, traitement du formulaire d'envoi d'adresse mail pour réinitialiser le mot de passe
 *
 */

// test des cookies

  var cookieEnabled = (navigator.cookieEnabled) ? true : false;

  if (typeof navigator.cookieEnabled == "undefined" && !cookieEnabled) // si le test navigateur ne donne rien , test de la création d'un cookie de test
{
  document.cookie="testcookie";

  cookieEnabled = (document.cookie.indexOf("testcookie") != -1) ? true : false;
}

// si la création de cookie échoue : affichage d'un message puis effacement du bouton de connexion

  if(cookieEnabled == false)
{

  document.querySelector('.nocookie').innerHTML = '<div class="w3-container w3-panel w3-border w3-blue">'+
                                                    '<p>'+
                                                    '<i class="fas fa-info-circle"></i> Les cookies sont bloqués sur votre navigateur. Veuillez les accepter pour vous inscrire ou vous connecté. Plus d\'informations : <a href="/twittux/privacy" style="text-decoration: underline;">Politique de confidentialité</a>'+
                                                    '</p></div>';

  document.querySelector('.w3-blue-grey').remove();
};


// Traitement du formulaire d'envoi d'adresse mail pour réinitialiser le mot de passe

let form_forgetpassword = document.querySelector('#formforgetpassword') // récupération du formulaire

form_forgetpassword.addEventListener('submit',  function (e) { // on capte l'envoi du formulaire

      e.preventDefault();

    let data = new FormData(this) // on récupère les données du formulaire

    let response = fetch(form_forgetpassword.getAttribute('action'), { // on récupère l'URL d'envoi des données
      method: 'POST',
      headers: {
                  'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                },
      body: data
    })
.then(function(response) {
    return response.text(); // récupération des données en JSON
  })
    .then(function(Data) {

document.getElementById('modalforgotpassword').style.display='none'; // fermeture modal

  if(Data == 'mailnotsend') // mail non envoyé
{
  alertbox.show('<div class="w3-panel w3-red">'+
                '<p>Cette adresse mail n\est pas enregistrée sur Twittux.</p>'+
              '</div>.');
}
else if (Data == 'emptymail') {
  alertbox.show('<div class="w3-panel w3-red">'+
                '<p>Veuillez entrer votre adresse mail.</p>'+
              '</div>.');
}
  else
{

//notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
                      '<p>Un e-mail pour réinitialiser votre mot de passe à était envoyé sur votre adresse.</p>'+
                    '</div>.');
}

// on vide le formulaire

form_forgetpassword.reset();

    }).catch(function(err) {

// notification d'échec

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Un problème technique empêche de réinitialiser votre mot de passe.</p>'+
                    '</div>.');

    });

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
