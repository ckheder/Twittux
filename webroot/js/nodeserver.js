/**
 * nodeserver.js
 *
 * Serveur Node JS de traitement des différends évènements
 *
 **/

 /** variable **/

 const httpServer = require("http").createServer();

 const io = require("socket.io")(httpServer, {
  cors: {
          origin: "http://localhost"
        }
});

  const users = []; // tableau contenant tous les utilisateurs connectés au chat

  var myconvs; // variable contenant mes conversations

//## Connexion ##//

io.on("connection", socket => {

  socket.on('connexion', function(data) {

    // on rejoint les conversations ou les tweets

    socket.join(data.rooms);

    // si je me connecte depuis la messagerie

      if(data.source == 'messagerie')
    {

      myconvs = Array.from(socket.rooms); // stockage de mes conversations

      socket.username = data.authname;

      // ajout de l'utilisateur au tableau des connectés

      users.push({
                  userID: socket.id,
                  username: socket.username,
                });


    // renvoi de la liste de mes destinataires

      io.to(socket.id).emit('testusers', testusers(data.usersinmyconvs));

    // on récupère les conversations auxquelles je suis connectée

      myconvs = myconvs.slice(1); // suppression de mon socket.id

      myconvs.forEach(element =>

    // on envoi l'info à toutes les rooms que je viens de me connecter

      socket.to(element).emit('joinconv', data.authname)

    );
  }
       // déconnexion

       socket.on('disconnect', () => {

         if(data.source == 'messagerie') // si je me déconnecte de la messagerie
       {

          // suppression d'un utilisateur déconnecté du tableau des connectés

          users.splice(users.findIndex(elem => elem.userID === socket.id), 1);

          myconvs.forEach(element =>

          // on envoi l'info à toutes les rooms

          socket.to(element).emit('leaveconv', data.authname)

        );
      }

       });
});

//## TWEET ## //

// nouveau tweet

  socket.on('newtweet', function (data)
{
  io.emit('addtweet', {Tweet: data.Tweet, Hashtag: data.Hashtag});
}

)

// tweet supprimé

  socket.on('userdeletetweet', function (data)
{
  io.to(data.usertweet).emit('deletetweet', data);
}

)

//## MESSAGERIE ## //

// fonction de test de la connexion de mes contacts

  function testusers(listusers)
{

  var destconv = [];

  listusers.forEach(function(element)
{

 let obj = users.find(o => o.username === element)

    if(typeof obj !== "undefined") // destinataire connecté
  {
    destconv.push(element)
  }

})

  return destconv;

}

//## Envoi message depuis l'index de la messagerie ##//

  socket.on('messagefromindex', function (data)
{

  let obj = users.find(o => o.username === data.destinataire); // on récupère les infos du destinataire

  let connectstate; // état de la connexion du destinataire

    if(typeof obj !== "undefined") // destinataire connecté
  {

    socket.to(obj.userID).emit('newmessage', data); // envoi pour l'affichage du message chez le destinataire

    connectstate = 'green'; // utilisateur connecté au chat

  }
    else
  {
    connectstate = 'red';
  }

    Object.assign(data, {  etatconnexion: connectstate  }); // on rajoute pour moi l'état de connexion

    io.to(socket.id).emit('newmessage', data); // envoi pour l'affichage du message chez moi avec l'état de connexion de mon destinataire

});

//## Envoi message depuis une conversation ##//

    socket.on('messagefromconv', function(data)
  {

    io.to(data.conversation).emit('newmessage', data);

  });

  //## On vérifie si je suis connecté à une conversation : loadconversation et création d'une conversation ##//

  socket.on('checkconnconv', function(conversation)
  {

    if(!socket.rooms.has(''+conversation+'')) // je ne suis pas connecté
  {
    socket.join(''+conversation+''); // je rejoins la conversation

    myconvs.push(''+conversation+''); // ajout de la conversation à ma liste de conversation
  }

  });

  //## Rejoindre une conversation par invitation ##//

    socket.on('joinbyinv', function(data)
  {
    socket.to(data.idconv).emit('newuserconv', data);
  })

  //## Activation d'une conversation : test si mes destinataires de cette conversation sont connectés ##//

    socket.on('activateconv', function(destinatairesconv)
  {
    io.to(socket.id).emit('updateuserstatut', testusers(destinatairesconv));
  }
  )

  //## Déconnexion d'une conversation : conversation masquée ##//

    socket.on('disableconv', function(conversation)
  {

    if(socket.rooms.has(''+conversation+''))
  {
    socket.leave(''+conversation+''); // je quitte la conversation

    myconvs = myconvs.filter(item => item !== conversation) // j'enlève la conversation
  }

  });

  //## COMMENTAIRE ## //

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

httpServer.listen(8082);
