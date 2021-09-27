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
  const users = []; // tableau contenant tous les utilisateurs connectés au chat
// connection

io.on("connection", socket => {

  socket.on('connexion', function(data) {

    socket.username = data.authname;

    // ajout de l'utilisateur au tableau des connectés

    users.push({
                userID: socket.id,
                username: socket.username,
              });


    // test de la connexion de mes destinataires

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

    // renvoi de la liste de mes destinataires

    io.to(socket.id).emit('testusers', testusers(data.usersinmyconvs));

    // on rejoint les conversations

    socket.join(data.rooms);

    // on récupère les conversations auxquelles je suis connectée

    var myconvs = Array.from(socket.rooms);

        myconvs = myconvs.slice(1); // suppression de mon socket.id

        myconvs.forEach(element =>

          // on envoi l'info à toutes les rooms que je viens de me connecter

        socket.to(element).emit('joinconv', data.authname)

    );

       // on vérifie si je suis connecté à une conversation : loadconversation et création d'une conversation

        socket.on('checkconnconv', function(conversation)
       {

          if(!socket.rooms.has(''+conversation+'')) // je ne suis pas connecté
        {
          socket.join(''+conversation+''); // je rejoins la conversation

          myconvs.push(''+conversation+''); // ajout de la conversation à ma liste de conversation
        }

       });

       // activation d'une conversation : test si mes destinataires sont connectés

       socket.on('activateconv', function(destinatairesconv)
     {


       io.to(socket.id).emit('updateuserstatut', testusers(destinatairesconv));

    }
   )

       // déconnexion d'une conversation : masquée conversation

       socket.on('disableconv', function(conversation)
      {

          if(socket.rooms.has(''+conversation+''))
        {
          socket.leave(''+conversation+''); // je quitte la conversation

          myconvs = myconvs.filter(item => item !== conversation) // j'enlève la conversation
        }

      });

       // déconnexion

       socket.on('disconnect', () => {

          // suppression d'un utilisateur déconnecté du tableau des connectés

          users.splice(users.findIndex(elem => elem.userID === socket.id), 1);

          myconvs.forEach(element =>

          // on envoi l'info à toutes les rooms

          socket.to(element).emit('leaveconv', data.authname)

        );

       });
});



// rejoindre une conversation par invitation

  socket.on('joinbyinv', function(data)
{

  socket.to(data.idconv).emit('newuserconv', data);

})

// envoi message depuis l'index

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

// envoi message depuis une conversation

    socket.on('messagefromconv', function(data)
  {

    io.to(data.conversation).emit('newmessage', data);

  });

});

httpServer.listen(8082);
