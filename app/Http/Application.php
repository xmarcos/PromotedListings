<?php

namespace PromotedListings\Http;

use Meli;
use Silex;
use xmarcos\Dot\Container as DotContainer;
use Symfony\Component\HttpFoundation\Request;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use PromotedListings\Service\FacebookAdsService;
use PromotedListings\Service\MeliAuthenticationService;
use PromotedListings\Service\FacebookService;

class Application extends Silex\Application
{
    use Silex\Application\TwigTrait;
    use Silex\Application\UrlGeneratorTrait;

    protected $config;

    public function __construct()
    {
        parent::__construct();

        $this->setupConfig();
        $this->setupFacebookServices();
        $this->registerProviders();
        $this->registerRoutes();

        $this->loginHook();
    }

    public function config()
    {
        return $this->config;
    }

    protected function setupConfig()
    {
        $root_path = realpath(__DIR__.'/../..');
        $paths = [
            'root'      => $root_path,
            'config'    => sprintf('%s/app/config', $root_path),
            'public'    => sprintf('%s/public', $root_path),
            'storage'   => sprintf('%s/data/storage', $root_path),
            'resources' => sprintf('%s/resources', $root_path),
        ];

        $app = require sprintf('%s/app.php', $paths['config']);
        $credentials = require sprintf('%s/credentials.php', $paths['config']);

        $this->config = DotContainer::create($app);
        $this->config->set('paths', $paths);
        $this->config->set('credentials', $credentials);

        $this['debug'] = $this->config->get('debug');
    }

    protected function loginHook()
    {
        $this->before(
            function (Request $request, Application $app) {
                $has_session       = $app['meli.authentication_service']->hasActiveSession();
                $current_route     = $request->get('_route');
                $account_routes    = ['login'];
                $is_account_route  = in_array($current_route, $account_routes);
                $account_dashboard = 'account_dashboard';

                if ($account_dashboard == $current_route) {
                    return;
                }

                if ($has_session && $is_account_route) {
                    return $app->redirect(
                        $app->path($account_dashboard)
                    );
                }

                if ($has_session) {
                    // $user         = $app['meli.authentication_service']->getCurrentUser();
                    // $access_token = $app['facebook.ad_service']->getAccessTokenByMeliUserId(
                    //     $user->get('user_id')
                    // );
                    // $ad_accounts = $app['facebook.ad_service']->getActiveAccounts();
                }

                if (!$has_session && !$is_account_route) {
                    $this['session']->set(
                        'redirect_url',
                        $app->path($current_route)
                    );

                    return $app->redirect(
                        $app->path($account_dashboard)
                    );
                }
            }
        );
    }

    protected function setupFacebookServices()
    {
        //Hack to ensure Facebook has a session
        $this->before(function (Request $request) {
            if (!headers_sent()) {
                $request->getSession()->start();
            }
        });

        FacebookSession::setDefaultApplication(
            $this->config()->get('credentials.facebook_app.app_id'),
            $this->config()->get('credentials.facebook_app.app_secret')
        );

        $this['facebook.login_helper'] = $this->share(
            function (Application $app) {
                return new FacebookRedirectLoginHelper(
                    $app->config()->get('credentials.facebook_app.redirect_url'),
                    $app->config()->get('credentials.facebook_app.app_id'),
                    $app->config()->get('credentials.facebook_app.app_secret')
                );
            }
        );

        $this['facebook.ad_service'] = $this->share(
            function (Application $app) {
                return new FacebookAdsService(
                    $app->config()->get('credentials.facebook_app.app_id'),
                    $app->config()->get('credentials.facebook_app.app_secret'),
                    $app['db']
                );
            }
        );
        
        $this['facebook.api_service'] = $this->share(
            function (Application $app) {
                return new FacebookService($app['facebook.ad_service']->getAccessToken());
            }
        );
    }

    protected function registerProviders()
    {
        $this->register(new Silex\Provider\ServiceControllerServiceProvider());
        $this->register(new Silex\Provider\TwigServiceProvider(), [
            'debug'            => $this['debug'],
            'strict_variables' => $this['debug'],
        ]);
        $this->extend('twig', function ($twig, Application $app) {
            $app['twig.loader.filesystem']->addPath(__DIR__.'/Views');

            return $twig;
        });
        $this->register(
            new Silex\Provider\DoctrineServiceProvider(),
            $this->config()->get('doctrine')
        );
        $this->register(new Silex\Provider\SessionServiceProvider());
        $this->register(new Silex\Provider\UrlGeneratorServiceProvider());

        $this['meli.api'] = $this->share(
            function (Application $app) {
                return new Meli(
                    $app->config()->get('credentials.meli_app.app_id'),
                    $app->config()->get('credentials.meli_app.app_secret')
                );
            }
        );

        $this['meli.authentication_service'] = $this->share(
            function (Application $app) {
                return new MeliAuthenticationService(
                    $app['meli.api'],
                    $app['db'],
                    $app['session'],
                    $app->config()->get('credentials.meli_app.app_url')
                );
            }
        );
        
        if($this['meli.authentication_service']->hasActiveSession()){
            $user_id = $this['meli.authentication_service']->getCurrentUser()->get('user_id');
            $access_token = $this['facebook.ad_service']->getAccessTokenByMeliUserId($user_id);
            $this['facebook.api_service']->setAccessToken($access_token);
        }
    }

    protected function registerRoutes()
    {
        $this->get('/', function(Request $request, Application $app) {
            return $app->redirect(
                $app->path('account_dashboard')
            );
        });
        $this->mount('/account', new Controller\Account()); // Login Meli
        $this->mount('/listing', new Controller\Listing()); // Productos Meli
        $this->mount('/promote', new Controller\Promote()); // Facebook Account Link
        $this->mount('/settings', new Controller\Settings()); // Facebook Account Link
    }
}
