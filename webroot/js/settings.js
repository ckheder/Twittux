/**
 * settings.js
 *
 * Mise à jour des paramètres
 *
 */

// désactivation du copier coller pour les champs mail et mot de passe de confirmation

document.querySelectorAll("#confirmemail, #confirmpassword").forEach(item => {
  item.addEventListener('paste', event => {

    event.preventDefault();

    return false;

  })
})

// mise à jour informations

  //variables

let form_settings = document.querySelector('#form_settings'); // récupération du formulaire

let inputfile = document.getElementById('submittedfile'); // input file (avatar)

//preview de l'Avatar

inputfile.addEventListener('change', (event) => {

     var imgPath = event.target;

     var size = imgPath.files[0].size; // taille fichier

     var extn = imgPath.files[0].type; // extension fichier

      if (extn == "image/jpg" || extn == "image/jpeg")
     { // fichier jpg/jpeg

        if(size <= 3047171) // taille inférieur ou égale à 3mo
       {
          if (typeof (FileReader) != "undefined") // si vieux navigateur
         {

             var reader = new FileReader();

              reader.onload = function()
             {
                var output = document.getElementById('previewHolder');
                output.src = reader.result;
             }

             reader.readAsDataURL(imgPath.files[0]);

         } else {

//notification vieux navigateur
alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Votre navigateur ne permet pas de lire ce fichier</p>'+
                  '</div>.');
         }
       }else {

//notification fichier trop gros

alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Ce fichier est trop gros.</p>'+
                  '</div>.');

                  inputfile.value = ""; // on vide l'input
       }
     } else {

//notification extension fichier

alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Seuls les fichiers Jpeg sont autorisés.</p>'+
                  '</div>.');
                  inputfile.value = "";
     }
 });

//envoi formulaire d'information

form_settings.addEventListener('submit', function (e) { // on capte l'envoi du formulaire

      e.preventDefault();

      let input_password = document.getElementById('password').value; // input password
      let input_confirm_password = document.getElementById('confirmpassword').value; //input confirmation password
      let input_mail = document.getElementById('mail').value; // input adresse mail
      let input_confirm_mail = document.getElementById('confirmemail').value; // input confirmation adresse mail
      let description = document.getElementById('description').value; // description
      let lieu = document.getElementById('lieu').value; // input lieu
      let website = document.getElementById('website').value; // input du website

// test tous les champs vide

  if (!input_password && !input_confirm_password && !input_mail && !input_confirm_mail && !description && !lieu && !website && !inputfile.value)
{
  return;
}

// vérification de l'égalité entre les mots de passe

//vérification si mot de passe envoyé

  if(input_password.length != 0)
{
  // test si le confirme passsword est pas vide

      if(input_confirm_password.length == 0)
    {
      alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Vous devez rentrer la confirmation du mot de passe.</p>'+
                    '</div>.');

      return;

    }

      else if(input_password != input_confirm_password) // comparaison entre les 2
    {
      alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Les 2 mots de passe ne correspondent pas.</p>'+
                    '</div>.');

      return;

    }
}
// si l'utilisateur rentre une confirmation de mot de passe sans mot de passe

    if(input_confirm_password.length != 0 && input_password.length == 0)
  {
    alertbox.show('<div class="w3-panel w3-red">'+
                        '<p>Les 2 mots de passe ne correspondent pas.</p>'+
                      '</div>.');
    return;
  }


// vérification de l'égalité entre les adresse mail

  if(input_mail.length != 0)
{
// test si le confirme mail est pas vide

        if(input_confirm_mail.length == 0)
      {
          alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Vous devez rentrer la confirmation de l\'adresse mail.</p>'+
                  '</div>.');
        return;
      }

        else if(input_mail != input_confirm_mail) // comparaison entre les 2
      {
        alertbox.show('<div class="w3-panel w3-red">'+
                    '<p>Les 2 adresses mail ne correspondent pas.</p>'+
                  '</div>.');
                  return;
      }
}
// si l'utilisateur rentre une confirmation d'adresse mail sans adresse mail

  if(input_confirm_mail.length != 0 && input_mail.length == 0)
{
  alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Les 2 adresses mail ne correspondent pas.</p>'+
                    '</div>.');
  return;
}

// vérification de la structure du website : URL

  if(website.length != 0)
{
  const regex = /^(https?):\/\/[^\s$.?#].[^\s]*$/gm;
  if(!regex.test(website))
  {
    alertbox.show('<div class="w3-panel w3-red">'+
                        '<p>l\'URL entrée est invalide.</p>'+
                      '</div>.');
                      return;
  }
}

    let data = new FormData(this); // on récupère les données du formulaire

  fetch(form_settings.getAttribute('action'), { // on récupère l'URL d'envoi des données
      method: 'POST',
      headers: {
                  'X-Requested-With': 'XMLHttpRequest' // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                },
      body: data
   })
.then(function(response) {
    return response.text(); // récupération des données en JSON
  })
   .then(function(result) {

     switch(result)
   {
case "updateok": // mise à jour d'information réussie

//on vide la formulaire

form_settings.reset();

inputfile.value = ""; // on vide l'input file

//notification de réussite

 alertbox.show('<div class="w3-panel w3-green">'+
  										'<p>Mise à jour réussie de vos informations.</p>'+
										'</div>.');
break;

case "probleme": // problème de mise à jour

                  alertbox.show('<div class="w3-panel w3-red">'+
                      					'<p>Impossible de mettre à jour vos informations.</p>'+
                    						'</div>.');

break;

case "existingmail" : // mail existant

                  alertbox.show('<div class="w3-panel w3-red">'+
                      					'<p>Cette adresse mail existe déjà.</p>'+
                    						'</div>.');

break;

case "notsamemail" : // mail ne correspondant pas

                  alertbox.show('<div class="w3-panel w3-red">'+
                      					'<p>Les deux adresses mail ne correspondent pas.</p>'+
                    						'</div>.');

break;

case "notsamepassword" : // mail ne correspondant pas

                  alertbox.show('<div class="w3-panel w3-red">'+
                      					'<p>Les deux mot de passe ne correspondent pas.</p>'+
                    						'</div>.');

break;


case "sizenotok" : // image trop lourde

                  alertbox.show('<div class="w3-panel w3-red">'+
                      					'<p>Cette image est trop lourde.</p>'+
                    						'</div>.');

break;

case "typenotok" : // pas de type jpeg/jpg

                  alertbox.show('<div class="w3-panel w3-red">'+
                      					'<p>Seules les images au format jpeg sont autorisées.</p>'+
                    						'</div>.');

break;
                  }


    }).catch(function(err) {

// notification d'échec

    	  alertbox.show('<div class="w3-panel w3-red">'+
  										'<p>Impossible de mettre à jour vos informations (serveur).</p>'+
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

/** modification ds paramètres **/

/** setup profil : public/prive **/

function setupprofil(){

        var select = document.querySelector('input[name="profil"]:checked').value; // valeur de l'input coché

        fetch('/twittux/setupprofil', {

          headers: {
                      'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                      'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authnetifié mon action depuis le site
                    },
                    method: "POST",

          body: JSON.stringify(select)
        })

        .then(function (data) {
                                return data.text();
                              })
        .then(function (result) {

            if(result == 'setupok') // mise à jour réussie des préférence de profil
          {

            alertbox.show('<div class="w3-panel w3-green">'+
           								'<p>Mise à jour réussie, votre profil est désormais '+ select +'.</p>'+
         									'</div>.');

          }

            else if (result == 'probleme')

          {
            alertbox.show('<div class="w3-panel w3-red">'+
                          '<p>Impossible de configurer votre profil, réessayez plus tard.</p>'+
                          '</div>.');

          }

        })

        // affichage d'erreur si besoin

          .catch(function(err)
        {
      	                       console.log(err);
      	});
    }
/** fin setup profil **/

/** setup notification **/

  function setupnotif(typenotif) // type_notif : notif_commentaire, notif_message,...
{
        var select = document.querySelector('input[name="'+typenotif+'"]:checked').value; // choix coché : oui /non

        var data = { typenotif: typenotif, select: select }; // tableau de données

        fetch('/twittux/setupnotif', {

          headers: {
                      'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                      'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authnetifié mon action depuis le site
                    },
                    method: "POST",

         body: JSON.stringify(data) // conversion en JSON du tableau
        })

        .then(function (data) {
                                return data.text();
                             })
        .then(function (result) {

            if(result == 'setupok') // modification de préférences de notification réussie
          {

              alertbox.show('<div class="w3-panel w3-green">'+
           									'<p>Préférence de notifications mise à jour.</p>'+
         										'</div>.');
          }

            else if (result == 'probleme')  // problème lors de la msie à jour
          {

              alertbox.show('<div class="w3-panel w3-red">'+
                            '<p>Impossible de configurer votre profil, réessayez plus tard.</p>'+
                            '</div>.');
          }

        })

        // affichage d'erreur si besoin

        .catch(function(err) {
      	                      console.log("fail" + err);
      	});
    }

/** fin setup notification **/
