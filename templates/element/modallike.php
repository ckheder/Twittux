<!-- modallike.php
  Fenêtre modal d'affichage de la liste des personnes aimant un post
 -->

<div id="modallike" class="w3-modal">

    <div class="w3-modal-content w3-card-4 w3-animate-top " style="max-width:600px">

      <header class="w3-container w3-center w3-teal">

        <span onclick="document.getElementById('modallike').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Fermer">&times;</span>

          <h3>Mention(s) J'aime</h3>

      </header>

        <div class="w3-container" id="contentlike"><!-- zone d'affichage de la page appelé en AJAX -->

        </div>

    </div>

</div>

<script>

// ouverture de la fenêtre modale contenant la liste des personnes aimant un tweet

  function openmodallike(idtweetlike)
{

  if(document.getElementById('modallike')) // si la modal existe car inexistante lors de la visite d'un profil en étant pas auth

{

  document.getElementById("contentlike").innerHTML = ""; // on vide la zone d'affichage des personnes aimant

  document.getElementById('modallike').style.display='block'; // affichage de la fenêtre modale

}
  else
{
    return;
}

fetch('/twittux/like/'+idtweetlike+'') // chargement de l'URL des personnes iamant avec l'identifiant du tweet en paramètres

.then(function (data)
{
  return data.text();
})
.then(function (html)
{
  document.getElementById("contentlike").innerHTML = html; // affichage du contenu de la page dans la div prévue

   // si l'élément paginationlike existe (c'est à dire qi il y'a minimum 2 pages de résultats paginées)
  // on crée un infinite ajax scroll

    if(document.querySelector('.paginationlike'))
  {

   let iaslike = new InfiniteAjaxScroll('#contentlike', {
  item: '.itemlike',
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
  pagination: '.paginationlike'

  });

}

})
.catch((err) => console.log(err));
}

// fin affichage modal des like

</script>
