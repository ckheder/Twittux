<!-- modallike.php
  Fenêtre modal d'affichage de la liste des personnes aimant un post
 -->

<div id="modallike" class="w3-modal">
    
    <div class="w3-modal-content w3-card-4 w3-animate-top " style="max-width:600px">
      
      <header class="w3-container w3-center w3-sand"> 
        
        <span onclick="document.getElementById('modallike').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Fermer">&times;</span>
        
          <h3>Mention(s) J'aime</h3>

      </header>

        <div class="w3-container" id="contentlike"><!-- zone d'affichage de la page appelé en AJAX -->


        </div>

    </div>

</div>