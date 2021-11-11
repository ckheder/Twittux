// hashtag.js
//
// Infinite AJAX Scroll d'affichage des hashtags
//

// variables

const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

//**Connexion NODE JS */

socket.emit("connexion", {authname: authname}); // on transmet mon username au serveur

iashashtag = null;

// création d'une nouvelle instance InfiniteAjaxScroll

      iashashtag = new InfiniteAjaxScroll('.list_hashtag', {
       item: '.itemhashtag',
       next: '.next',
       logger: false,
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

     iashashtag.on('last', function() {

       document.querySelector('.no-more').style.opacity = '1';
     })