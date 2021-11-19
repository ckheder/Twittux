/**
 * addtweet.js
 *
 * Ajout d'un tweet
 *
 */

 // variables

const menuemoji = document.getElementById("menuemoji"); //div contentant la liste des emojis

const textarea_tweet = document.querySelector('#textarea_tweet'); // textarea de rédaction d'un tweet

let inputfile = document.getElementById('tweetmedia'); // input d'envoi de photo depuis l'ordinateur (sur modalmedia)

//ouverture menu

function openemojimenu() {

 if (menuemoji.className.indexOf("w3-show") == -1) {
        menuemoji.className += " w3-show";
    } else {
        menuemoji.className = menuemoji.className.replace(" w3-show", "");
    }
}

//ajout au textarea des emojis ou des médias

document.addEventListener('click',function(e){

  // récupération élément

    if(e.target && e.target.className == 'emoji')
  {
    var code = e.target.getAttribute('data_code');
    //suppression de l'extension du fichier
    code  = code.replace(/\.[^/.]+$/, "");
    code = ' :'+code+': ';

    addtotextarea(code)

    menuemoji.className = menuemoji.className.replace(" w3-show", "");
  }
});

// fonction d'ajout au textarea

  function addtotextarea(stringtoadd)
{
  textarea_tweet.value += stringtoadd;

  textarea_tweet.focus();
}

// restriction et compteur de caractère tweet : 255 caractères

function countCharacters(e) {
  var textEntered, countRemaining, counter;
  textEntered = textarea_tweet.value;
  counter = (255 - (textEntered.length));
  countRemaining = document.getElementById('charactersRemaining');
  countRemaining.textContent = counter + ' caractère(s) restant(s)';
}


textarea_tweet.addEventListener('keydown', countCharacters);

// MEDIA

//vérifier si il y'a déjà un media

  function remove_existig_media(editor)
{
  return editor.replace(/\[.*\]/g,'');
}


// test si fichier img ou trop gros pour l'input d'envoi depuis l'ordinateur

inputfile.addEventListener('change', (event) => {

     var imgPath = event.target; //fichier

     var name = imgPath.files[0].name; // nom du fichier

     var size = imgPath.files[0].size; // taille fichier

     var extn = imgPath.files[0].type; // extension fichier

      if (extn == "image/jpeg" || extn == "image/png" || extn == "image/gif") // fichier jpg/png/gif
     {
        if(size > 3047171) // taille inférieur ou égale à 3mo
       {

         //notification fichier trop gros

         alertbox.show('<div class="w3-panel w3-red">'+
                        '<p>Ce fichier est trop gros.</p>'+
                        '</div>.');

                  inputfile.value = "";

       }
        else
       {
         textarea_tweet.value = remove_existig_media(textarea_tweet.value); // suppression d'un éventuel média déjà sur la textarea

          // ajout de l'image

          addmediatotweet = '[image]'+name +'[/image]';

          addtotextarea(addmediatotweet);

       }
     }

      else
     {

       //notification extension fichier

       alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Seuls les fichiers Jpeg/Png/Gif sont autorisés.</p>'+
                      '</div>.');

                  inputfile.value = ""; // on vide l'input
     }
 });

// FIN MEDIA

//ajout d'un tweet

let form_tweet = document.querySelector('#form_tweet') // récupération du formulaire

let button_submit_tweet = form_tweet.querySelector('button[type=submit]') // récupération du bouton d'envoi

let buttonTextSubmitTweet = button_submit_tweet.textContent // récupération du texte du bouton

form_tweet.addEventListener('submit',  function (e) { // on capte l'envoi du formulaire

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

  button_submit_tweet.textContent = 'Publication en cours...' // mise à jour du texte du bouton

    let data = new FormData(this) // on récupère les données du formulaire

    let response = fetch(form_tweet.getAttribute('action'), { // on récupère l'URL d'envoi des données
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

  document.getElementById('modaltweet').style.display='none'; // fermeture modale

  // impossible de poster un tweet

  if(jsonData.result == 'notweet')
{
  alertbox.show('<div class="w3-panel w3-red">'+
                '<p>Impossible de poster ce tweet.</p>'+
                '</div>.');
}

  // émission d'un event de nouveau tweet

  else
{

  socket.emit('newtweet', {Tweet: jsonData.Tweet, Hashtag: jsonData.Hashtag});

  // évènenement de citation dans un tweet : contient un tableau de tous les utilisateurs cités dans le tweet posté et qui accepte les notifications de citation

    if(jsonData.notifcitation.length > 0) // si ce tableau n'est pas vide, on déclenche l'évènement
  {
    socket.emit('tweetcitation', jsonData.notifcitation)
  }

  //on vide la formulaire

  form_tweet.reset()

  //reset du nombre de caractère restants

  document.getElementById('charactersRemaining').textContent = '255 caractère(s) restant(s)';

  //notification de réussite

  alertbox.show('<div class="w3-panel w3-green">'+
                '<p>Tweet posté.</p>'+
                '</div>.');


}
    }).catch(function(err) {

      console.log(err);

    // notification d'échec

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Un problème technique empêche de poster ce tweet.</p>'+
                      '</div>.');

    });

  button_submit_tweet.disabled = false // on réactive le bouton


  button_submit_tweet.textContent = buttonTextSubmitTweet// on remet le texte initial du bouton

})
