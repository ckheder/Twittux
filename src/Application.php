<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Cake\Http\Middleware\HttpsEnforcerMiddleware;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use Cake\I18n\FrozenTime;
/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();



        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        if (Configure::read('debug')) {
            $this->addPlugin('DebugKit');
        }

        // Load more plugins here

          $this->addPlugin('Authentication');
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {

        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance. For that when
            // creating the middleware instance specify the cache config name by
            // using it's second constructor argument:
            // `new RoutingMiddleware($this, '_cake_routes_')`
            ->add(new RoutingMiddleware($this))

            ->add(new EncryptedCookieMiddleware(['CookieAuth'],Configure::read('Security.cookieKey')))

            // disableOnDebug' => true -> disable in local environnement

            ->add(new HttpsEnforcerMiddleware(['disableOnDebug' => true])) // 'redirect' => true/false (if true 'statusCode' => 302), 'headers' => ['X-Https-Upgrade' => 1]

            ->add(new AuthenticationMiddleware($this));

        return $middlewareQueue;
    }

    /**
     * Bootrapping for CLI application.
     *
     * That is when running commands.
     *
     * @return void
     */
    protected function bootstrapCli(): void
    {
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');

        // Load more plugins here
    }

    /**
 * Returns a service provider instance.
 *
 * @param \Psr\Http\Message\ServerRequestInterface $request Request
 * @return \Authentication\AuthenticationServiceInterface
 */
 public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
{
  $service = new AuthenticationService();

  // adresse de redirection en cas d'accès à uné méthode en étant non authentifié

  $service->setConfig([
      'unauthenticatedRedirect' => '/twittux/login',
      'queryParam' => 'redirect',
  ]);

  // Load identifiers
  $service->loadIdentifier('Authentication.Password', [
      'fields' => [
        IdentifierInterface::CREDENTIAL_USERNAME => 'username',
        IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
      ]
  ]);

  $now = FrozenTime::now();
  $now = $now->modify('+365 days');

  // Load the authenticators
  $service->loadAuthenticator('Authentication.Session');

  $service->loadAuthenticator('Authentication.Cookie', [
      'fields' =>
    [
        IdentifierInterface::CREDENTIAL_USERNAME => 'username',
        IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
    ],
      'cookie'=>
    [
      'httponly' => true, // empếche l'accès aux cookies en Javascript
      'expires' => $now,
      'secure' => true, // cookie crée uniquement dans le cas d'une connection https
    ]

]);
  $service->loadAuthenticator('Authentication.Form');

  return $service;
}

}
