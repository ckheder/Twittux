<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */
/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {
    $builder->setExtensions(['json', 'xml']);
    // Register scoped middleware for in scopes.
    $builder->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httponly' => true,
    ]));

    /*
     * Apply a middleware to the current route scope.
     * Requires middleware to be registered through `Application::routes()` with `registerMiddleware()`
     */
    $builder->applyMiddleware('csrf');

    /*
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, templates/Pages/home.php)...
     */
    $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);


    /*
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    //$builder->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

/* ROUTE USERS */

    // route login

    $builder->connect('/login',['controller' => 'Users', 'action' => 'login']);

    // route logout

    $builder->connect('/logout',['controller' => 'Users', 'action' => 'logout']);

    // route recherche utilisateurs (autocomplete)

    $builder->connect('/searchusers-{query}',['controller' => 'Users', 'action' => 'searchusers']);

    // route vers edition profil

    $builder->connect('/settings',['controller' => 'Users', 'action' => 'edit']);

    // route forgotpassword

    $builder->connect('/forgotpassword',['controller' => 'Users', 'action' => 'forgotpassword']);

    // route resetpassword

    $builder->connect('/resetpassword/{token}',['controller' => 'Users', 'action' => 'resetpassword']);


  /* ROUTE TWEET */

        // route profil

    $builder->connect('/:username',['controller' => 'Tweets', 'action' => 'index'],['_name' => 'profil'])->setPass(['username']);

        // ajouter un tweet

    $builder->connect('/tweet/add',['controller' => 'Tweets', 'action' => 'add']);

        //voir un tweet

    $builder->connect('/statut/{id}',
        ['controller' => 'Tweets', 'action' => 'view'],
                        ['id' => '\d+', 'pass' => ['id']],['_name' => 'viewtweet']);

        // voir tweet avec media sur profil

    $builder->connect('/:username/media',['controller' => 'Tweets', 'action' => 'mediatweet'],['_name' => 'mediatweet'])->setPass(['username']);

        // supprimer un tweet

    $builder->connect('/tweet/delete',
        ['controller' => 'Tweets', 'action' => 'delete']);

        // voir mon actualités

    $builder->connect('/actualites',['controller' => 'Tweets', 'action' => 'actualites']);

  /* ROUTE HASHTAG */

        // voir la liste des hashtag les plus populaire

    $builder->connect('/trending',['controller' => 'Hashtag', 'action' => 'index']);

  /* ROUTE COMMENTAIRE */

        //ajouter un commentaire

    $builder->connect('/commentaire/add',['controller' => 'Commentaires', 'action' => 'add']);

        // supprimer un commentaire

    $builder->connect('/commentaire/delete',['controller' => 'Commentaires', 'action' => 'delete']);

      //activer/Désactiver les commentaires

    $builder->connect('/commentaire/actioncomm',['controller' => 'Commentaires', 'action' => 'actioncomm']);

  /* ROUTE ABONNEMENT */

        //ajouter un abonnement

    $builder->connect('/abonnement/add',['controller' => 'Abonnements', 'action' => 'add']);

        // supprimer un abonnement

    $builder->connect('/abonnement/delete',['controller' => 'Abonnements', 'action' => 'delete']);

        // annuler une demande d'abonnement

    $builder->connect('/abonnement/cancel',['controller' => 'Abonnements', 'action' => 'cancel']);

        // liste des abonnements

    $builder->connect('/social/{username}',['controller' => 'Abonnements', 'action' => 'abonnements'])->setPass(['username']);

            // liste des abonnés

    $builder->connect('/abonnes/{username}',['controller' => 'Abonnements', 'action' => 'abonnes'])->setPass(['username']);

            // liste des demande d'abonnement

    $builder->connect('/abonnement/demande',['controller' => 'Abonnements', 'action' => 'demande']);

            // répondre à une demande d'abonnement

    $builder->connect('/abonnement/request',['controller' => 'Abonnements', 'action' => 'request']);

  /* ROUTE RECHERCHE */

        //recherche tweet

    $builder->connect('/search/{query}',['controller' => 'Search', 'action' => 'index'],['_name' => 'search']);

        //recherche users (moteur de recherche)

    $builder->connect('/search/users/{query}',['controller' => 'Search', 'action' => 'searchusers']);

        // recherche hashtag

    $builder->connect('/search/hashtag/{query}',['controller' => 'Search', 'action' => 'hashtag']);

    // recherche hashtag avec media

    $builder->connect('/search/hashtag/media/{query}',['controller' => 'Search', 'action' => 'mediahashtag']);

        //recherche users hashtag (moteur de recherche)

    $builder->connect('/search/hashtag/users/{query}',['controller' => 'Search', 'action' => 'userhashtag']);

    //recherche tweet avec média

    $builder->connect('/search/media/{query}',['controller' => 'Search', 'action' => 'media']);

  /* ROUTE LIKE */

        // liste des personnes aimant un post (fenêtre modal)

    $builder->connect('/like/{idtweet}',['controller' => 'Aime', 'action' => 'view'],['idtweet' => '\d+', 'pass' => ['idtweet']]);

        // ajouter/supprimer un like

    $builder->connect('/likecontent',['controller' => 'Aime', 'action' => 'add']);

  /* ROUTE BLOCAGE */

        // crée un blocage

    $builder->connect('/blockuser',['controller' => 'Blocage', 'action' => 'add']);

        // supprimer un blocage

    $builder->connect('/deblockuser',['controller' => 'Blocage', 'action' => 'delete']);

      // liste des utilisateurs bloqués

    $builder->connect('/userblock',['controller' => 'Blocage', 'action' => 'index']);

  /* ROUTE PARTAGE */

        // ajouter un partage

    $builder->connect('/share',['controller' => 'Partage', 'action' => 'add']);

        // supprimer un partage

    $builder->connect('/share/delete',['controller' => 'Partage', 'action' => 'delete']);

  /* ROUTE SETTINGS */

    // modifier type de compte : public / privé

    $builder->connect('/setupprofil',['controller' => 'Settings', 'action' => 'setupprofil']);

    // modifier préférence de notification

    $builder->connect('/setupnotif',['controller' => 'Settings', 'action' => 'setupnotif']);

    // supprimer un compte

    $builder->connect('/deleteaccount',['controller' => 'Users', 'action' => 'delete']);

/* ROUTE Notifications */

    // liste des notifications

    $builder->connect('/notifications',['controller' => 'Notifications', 'action' => 'index']);

    // statut des notifications

    $builder->connect('/notifications/statut',['controller' => 'Notifications', 'action' => 'statut']);

    // nombre de notifications non lues

    $builder->connect('/notifications/unreadnotif',['controller' => 'Notifications', 'action' => 'getunreadnotif']);

/* ROUTE MESSAGERIE */

    // page d'accueil de la messagerie

    $builder->connect('/messagerie',['controller' => 'Messagerie', 'action' => 'index'],['_name' => 'messagerie']);

    // nouveau message

    $builder->connect('/messagerie/newmessage',['controller' => 'Messagerie', 'action' => 'add']);

    // liste des conversations

    $builder->connect('/messagerie/listconv',['controller' => 'Messagerie', 'action' => 'listconv']);

    // voir une conversation

    $builder->connect('/conversation-{idconv}',['controller' => 'Messagerie', 'action' => 'view'],['idconv' => '\d+', 'pass' => ['idconv']]);

    // rejoindre ou crée une conversation depuis le profil

    $builder->connect('/messagerie/messagefromprofil',['controller' => 'Messagerie', 'action' => 'messagefromprofil']);

    // masquer une conversation

    $builder->connect('/conversation/update',['controller' => 'UserConversation', 'action' => 'edit']);

    // inviter à rejoindre une conversation

    $builder->connect('/conversation/addtoconv',['controller' => 'UserConversation', 'action' => 'addtoconv']);

    // rejoindre une conversation depuis les notifications

    $builder->connect('/conversation/joinconv',['controller' => 'UserConversation', 'action' => 'joinconv']);

    /*
     * Connect catchall routes for all controllers.
     *
     * The `fallbacks` method is a shortcut for
     *
     * ```
     * $builder->connect('/:controller', ['action' => 'index']);
     * $builder->connect('/:controller/:action/*', []);
     * ```
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $builder->fallbacks();
});

/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * $routes->scope('/api', function (RouteBuilder $builder) {
 *     // No $builder->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */
