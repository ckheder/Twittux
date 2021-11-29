// hashtag.js
//
// Infinite AJAX Scroll d'affichage des hashtags
//

// variables

const spinner = document.querySelector('.spinner'); // div qui accueuillera le spinner de chargement des données via AJAX

//**Connexion NODE JS */

socket.emit("connexion", {authname: authname, rooms: 'trendingpage'}); // on transmet mon username au serveur

// Hashtag

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
            else if(typeof hashtagitem !== "undefined") // si je suis sur la page trending et que le hashtag n'existe pas on le crée à la fin
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