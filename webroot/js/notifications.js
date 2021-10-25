/**
 * notifications.js
 *
 * Gestion des notifications
 *
 */

 // variables

 var actnotif = document.querySelectorAll('.actnotif'); // on stock tous les lien qui ont une classe 'actnotif' -> mettre à jour le statut d'une notif
 var nb_notification = document.querySelector('.nb_notification'); // récupération du nombre d'abonnement

 // Infinite AJAX scroll de la liste des notifications : instanciation dans le cas unique ou le nombre de notification est supérieur à zéro 

  if(nb_notification > 0)
 {

   let ias = new InfiniteAjaxScroll('#list_notif', {
   item: '.itemnotif',
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
       element.setAttribute('hidden', ''); // default behaviour
     }
   },
   pagination: '.pagination'
 });

// action lors du chargement de toutes les données : affichage d'une div annoncant qu'il n'y a plus rien à charger

 ias.on('last', function() {

   document.querySelector('.no-more').style.opacity = '1';
 })
}
 // marquer une notif comme lue / non lue

 document.addEventListener('click',function(e){

    if(e.target && e.target.matches('.actnotif')) // si le lien cliqué possède l'attribut 'data_idconv'
   {


       var data = {
         "statut": e.target.getAttribute('data_statut') // récupération du statut
       }

          if(e.target.getAttribute('data_id_notif')) // si il y'a un identifiant on l'ajoute au tableau (sert pour la mmise à jour de statut lue/non lue)
       {

         data.id_notif = e.target.getAttribute('data_id_notif');
       }

         let response = fetch('/twittux/notifications/statut', {

           headers: {
                       'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                       'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authnetifié mon action depuis le site
                     },
                     method: "POST",

           body: JSON.stringify(data)
         })
       .then(function(response) {
        return response.json(); // récupération des données au format texte
       })
         .then(function(Data) {

           switch(Data.Result)
         {

             // mise à jour réussie du statut d'une notification

             case "updateok":

             // récupération de la div contenant la notification pour mettre à jour sa classe

              var div_notif =   document.querySelector('[data_id_notif="'+ e.target.getAttribute('data_id_notif') +'"]');

              if(e.target.getAttribute('data_statut') == "0") // on passe à une notification lue
            {
              e.target.setAttribute('data_statut', 1); // mise à jour du statut : 1 -> lue

              e.target.innerHTML = "<i class=\"fas fa-eye-slash\"></i> Marquer comme non lue</a>"; // nouveau lien

              div_notif.className = div_notif.className.replace("w3-container itemnotif w3-light-grey", "w3-container itemnotif w3-sand"); // mise à jour du background de la div

            }
                else if (e.target.getAttribute('data_statut') == "1") // on passe à une notification non lue
              {

                e.target.setAttribute('data_statut', 0); // mise à jour du statut : 0 -> non lue

                e.target.innerHTML = "<i class=\"fas fa-eye\"></i> Marquer comme lue</a>"; // nouveau lien

                div_notif.className = div_notif.className.replace("w3-container itemnotif w3-sand", "w3-container itemnotif w3-light-grey"); // mise à jour du background de la div

            }

             break;

             // Notification supprimée

             case "deletenotifok": alertbox.show('<div class="w3-panel w3-green">'+
                                           '<p>Notification supprimée.</p>'+
                                           '</div>.');

            // suppresion du DOM de la div contenant la notification

            var div_notif_delete =  document.querySelector('[data_id_notif="'+ e.target.getAttribute('data_id_notif') +'"]');

            div_notif_delete.parentNode.removeChild(div_notif_delete);

            //décrémentation du nombre de notification

            nb_notification.textContent --;

             break;

             // tous marquer comme lue

             case "allnotifreadok": alertbox.show('<div class="w3-panel w3-green">'+
                                           '<p>Toutes les notif sont lue.</p>'+
                                           '</div>.');

                                           // on récupère toutes les div contenant la classe w3-container.w3-light-grey synonyme de notification non lue

                                           var allnotifnoread = document.querySelectorAll(".w3-container.w3-light-grey.itemnotif");

                                           allnotifnoread.forEach(item => {

                                             // pour chaque résultat, on récupère et on met à jour son lien

                                             item.querySelector('a[class="actnotif"]').innerHTML = "<i class=\"fas fa-eye-slash\"></i> Marquer comme non lue</a>";

                                             // on met à jour l'attribut 'statut' vers 1

                                             item.querySelector('a[class="actnotif"]').setAttribute('data_statut', 1);

                                             // mise à jour class de la div

                                             item.className = item.className.replace("w3-container itemnotif w3-light-grey", "w3-container itemnotif w3-sand");

                                           })


             break;

             // toutes les notifs sont supprimées

             case "allnotifdeleteok": alertbox.show('<div class="w3-panel w3-green">'+
                                           '<p>Toutes les notifications ont été supprimées.</p>'+
                                           '</div>.');

            // mise à jour du nombre de notifications à 0

            nb_notification.textContent = "0";

            // on vide la liste des notifications

            document.querySelector('#list_notif').innerHTML = '';

             break;

         }

         }).catch(function(err) {

           console.log(err);

       // notification d'échec : problème technique, serveur,...

             alertbox.show('<div class="w3-panel w3-red">'+
                           '<p>Un problème est survenu lors du traitement de votre demande.Veuillez réessayer plus tard.</p>'+
                         '</div>.');

         })

       }

     })

/** rejoindre une conversation après notification d'un nouveau message **/

     document.addEventListener('click',function(e){

         if(e.target && e.target.getAttribute('data_msg_conv')) // si le lien cliqué possède l'attribut 'data_msg_conv'
       {

         var msg_conv = e.target.getAttribute('data_msg_conv');

         // envoi de l'id de conversation en local storage et on redirige vers l'index de la messagerie

         localStorage.setItem("idconv", msg_conv);

         window.location.href = '/twittux/messagerie';
       }

     })

/** répondre à une invitation à rejoindre une conversation **/

     document.addEventListener('click',function(e){

        if(e.target && e.target.getAttribute('data_idconv')) // si le lien cliqué possède l'attribut 'data_idconv'
       {

    // stockage des données nécessaires au traitemnt par CakePHP de la réponse
    var data = {
                  "idconv": e.target.getAttribute('data_idconv'), // identifiant de la conversation
                  "typeconv": e.target.getAttribute('data_typeconv') // username de la personne invité
                }

    // appel controller pour crée une ligne de conversation

    let response = fetch('/twittux/conversation/joinconv', {
      headers: {
                  'X-Requested-With': 'XMLHttpRequest', // envoi d'un header pour tester dans le controlleur si la requête est bien une requête ajax
                  'X-CSRF-Token': csrfToken // envoi d'un token CSRF pour authnetifié mon action depuis le site
                },
                method: "POST",

      body: JSON.stringify(data)
    })
.then(function(response) {
    return response.json(); // récupération des données au format texte
  })
    .then(function(Data) {

    // possibilité de rejoindre la conversation

        if(Data.Result == 'joinconvok')
      {

        // création d'un item localStorage avec l'identifiant de la conversation

        localStorage.setItem("idconv", e.target.getAttribute('data_idconv'));

        // redirection vers l'index de la messagerie

        window.location.href = '/twittux/messagerie';
      }

      // impossible de rejoindre la conversation

        else if (Data.Result == 'joinconvnotok')
      {

          alertbox.show('<div class="w3-panel w3-red">'+
                  '<p>Impossible de rejoindre cette conversation.</p>'+
                '</div>.');

      }

    }).catch(function(err) {

// notification d'échec : problème technique, serveur,...

        alertbox.show('<div class="w3-panel w3-red">'+
                      '<p>Impossible de supprimer ce commentaire.</p>'+
                    '</div>.');

    });

}
})

/** accéder à la page des demandes d'abonnement depuis une notification **/

  if(document.querySelector(".requestlink"))
{

  document.querySelector(".requestlink").addEventListener('click',function(e)
{

// création d'un item local contenant le nom du profil en cours de visite

  localStorage.setItem("requestlink", e.target.getAttribute('data_username'));

// redirection vers la page social

  window.location.href = '/twittux/social/'+ e.target.getAttribute('data_username')+'';

})
}
