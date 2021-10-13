/**
 * messagerie.js
 *
 * Traitement des actions de la messagerie : liste des ocnversations, affichage d'une conversation, envoi de message
 *
 **/

 /** variable **/

 const httpServer = require("http").createServer();

 const io = require("socket.io")(httpServer, {
  cors: {
          origin: "http://localhost"
        }
});

// connection

io.on("connection", socket => {

  socket.on('connexion', function(data) {

    // on rejoint les commentaires

    socket.join(data);

});


//## Ajout d'un commentaire ##

  socket.on('newcomm', function (data)
{

  io.to(data.idtweet).emit('addcomm', data.comm);
}

)

//## Mise à jour d'un commentaire ##

  socket.on('userupdatecomm', function (data)
{

  io.to(data.idtweet).emit('updatecomm', data);

}
)

// ## Suppression d'un commentaire ##

socket.on('deletecommok', function (data)
{

  io.to(data.idtweet).emit('deletecomm', data.idcomm);

}
)

//## Activation / Désactivation des commentaires ##

// Activation des commentaires

  socket.on('userallowcomment', function (idtweet)
{
  io.to(idtweet).emit('allowcomment');
}
);

// désactivation des commentaires

  socket.on('userdisablecomment', function (idtweet)
{
  io.to(idtweet).emit('disablecomment');
}

);

});

httpServer.listen(8083);
