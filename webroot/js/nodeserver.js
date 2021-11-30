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

  const users = []; // tableau contenant tous les utilisateurs connectés

  var myconvs; // variable contenant mes conversations

//## Connexion ##//

io.on("connection", socket => {

  socket.on('connexion', function(data) {

          // on rejoint les conversations ou les tweets

          socket.join(data.rooms);

          // ajout de l'utilisateur au tableau des connectés

          socket.username = data.authname;

          users.push({
            userID: socket.id,
            username: socket.username,
          });

          //io.emit('joinconv', data.authname)

          socket.broadcast.emit('joinconv', data.authname);

          // si je me connecte depuis la messagerie

          if(data.usersinmyconvs)
        {

          myconvs = Array.from(socket.rooms); // stockage de mes conversations

          // renvoi de la liste de mes destinataires et si ils sont connectés

          io.to(socket.id).emit('testusers', testusers(data.usersinmyconvs));

          // on récupère les conversations auxquelles je suis connectée

          myconvs = myconvs.slice(1); // suppression de mon socket.id

        }

    });

       // déconnexion

       socket.on('disconnect', () => {

        // suppression d'un utilisateur déconnecté du tableau des connectés

        users.splice(users.findIndex(elem => elem.username == socket.username), 1);

        if(myconvs)
      {
        myconvs.forEach(element =>

        // on envoi l'info à toutes les rooms

        socket.to(element).emit('leaveconv', socket.username)

        );
      }

});

//## TWEET ## //

  // nouveau tweet + traitement hashtag

      socket.on('newtweet', function (data)
    {

      io.to(data.Tweet['username']).emit('addtweet', {Tweet: data.Tweet}); // annonce d'un nouveau tweet

        if(data.Hashtag.length >0) // si des hashtag sont trouvés : envoi d'un évènement aux pages de news, au profil qui vient de poster et à la page trending
      {
        io.to("newspage").to(data.Tweet['username']).to("trendingpage").emit('hashtag', {Hashtag: data.Hashtag});
      }

    }
  )

// tweet supprimé

      socket.on('userdeletetweet', function (data)
    {
      io.to(data.usertweet).emit('deletetweet', data);
    }
  )

// citation dans un tweet

      socket.on('tweetcitation', function(data)
    {

      Object.values(data).forEach(element => // on vérifie si ,pour chaque utilisateur cité dans mon tweet et acceptant les notifications de citation, il est connecté et , si  oui, on lui envoi une notification
    {

      let obj = users.find(o => o.username == element); // on récupère les infos du destinataire

      if(typeof obj !== "undefined") // utilisateur connecté
    {
      socket.to(obj.userID).emit('newnotif'); // envoi d'une notification
    }
  })
})

//## MESSAGERIE ## //

// fonction de test de la connexion de mes contacts

  function testusers(listusers)
{

  var destconv = []; // contiendra mes destinataires connectés

    listusers.forEach(function(element)
  {

    let obj = users.find(o => o.username == element)

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

  let obj = users.find(o => o.username == data.destinataire); // on récupère les infos du destinataire

  let connectstate; // état de la connexion du destinataire

    if(typeof obj !== "undefined") // destinataire connecté
  {

    socket.to(obj.userID).emit('newmessage', {message: data}); // envoi pour l'affichage du message chez le destinataire

    connectstate = 'green'; // utilisateur connecté au chat

    socket.to(obj.userID).emit('newnotif'); // notification pour le destinataire connecté d'une notification de nouveau message

  }
    else
  {
    connectstate = 'red';
  }

    Object.assign(data, {  etatconnexion: connectstate  }); // on rajoute pour moi l'état de connexion

    io.to(socket.id).emit('newmessage', {message: data}); // envoi pour l'affichage du message chez moi avec l'état de connexion de mon destinataire

});

//## Envoi message depuis une conversation ##//

    socket.on('messagefromconv', function(data)
  {

    io.to(data.message['conversation']).emit('newmessage', data);

      data.notifnewmessage.forEach(function(element)
    {

     let obj = users.find(o => o.username == element);

        if(typeof obj !== "undefined") // destinataire connecté
      {
        socket.to(obj.userID).emit('newnotif');
      }

    })

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

  //## Notification à rejoindre une conversation ## //

    socket.on('notifinvittojoinconv', function(data)
  {
    data.forEach(function(element) // on envoi une notification pour chaque utilisateur invité
    {

     let obj = users.find(o => o.username == element);

        socket.to(obj.userID).emit('newnotif');
    })
  })

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

  //## PARTAGE ##//

  //## Ajout d'un partage : création d'une notification + incrémentation du nombre de partage sur un post

    socket.on('newshare', function(data)
  {
      io.to("newspage").to("searchpage").to(data.auttweet).emit('addshare', data.idtweet);

      if(data.notifshare == "oui") // si l'auteur du tweet accepte les notifications de partage et qu'il est connecté
    {

        let obj = users.find(o => o.username == data.auttweet); // on vérifie si l'auteur du tweet est connecté

        if(typeof obj !== "undefined") // auteur connecté
      {
        socket.to(obj.userID).emit('newnotif'); // évènement de nouvelle notification
      }
    }
  });

  //## ABONNEMENT ##//

    //## Ajout d'un abonnement/demande : création d'une notification

    socket.on('newabo', function(username)
  {

    let obj = users.find(o => o.username === username); // on vérifie si la personne à laquelle on s'abonne est connectée

      if(typeof obj !== "undefined") // personne connecté
    {
      socket.to(obj.userID).emit('newnotif'); // évènement de nouvelle notification
    }
  });

  //## LIKE ##//

  //## new like ##//

    socket.on('like', function(data) // émission d'un évènement de new like ou dislike(data.action : add-> nouveau like, remove -> dislike)
  {
    io.to("newspage").to("searchpage").to(data.auttweet).emit('actionlike', {idtweet: data.idtweet, action: data.action});

  })

  //## COMMENTAIRE ## //

  //## Ajout d'un commentaire ##

    socket.on('newcomm', function (data)
  {

    io.to(data.idtweet).emit('addcomm', data.comm); // émission d'un évènement de nouveau commentaire contenant les informations de ce commentaire

      // notification si accepté

      if(data.comm['notifnewcomm'])
    {

      let obj = users.find(o => o.username === data.comm['auttweet']); // on vérifie si l'auteur du tweet est connecté

        if(typeof obj !== "undefined") // destinataire connecté
      {
        socket.to(obj.userID).emit('newnotif'); // évènement de nouvelle notification
      }
    }
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
