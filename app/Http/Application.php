<?php

namespace PromotedListings\Http;

use Silex;
use Exception;
use InvalidArgumentException;
use Igorw\Silex\ConfigServiceProvider;
use xmarcos\Dot\Container as DotContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

class Application extends Silex\Application
{
    use Silex\Application\TwigTrait;

    protected $config;

    public function __construct()
    {
        parent::__construct();

        $this->setupConfig();
        $this->setupFacebookServices();
        $this->registerProviders();
        $this->registerRoutes();
    }

    public function config()
    {
        return $this->config;
    }

    protected function setupConfig()
    {
        $root_path = realpath(__DIR__.'/../..');
        $paths     = [
            'root'      => $root_path,
            'config'    => sprintf('%s/app/config', $root_path),
            'public'    => sprintf('%s/public', $root_path),
            'storage'   => sprintf('%s/data/storage', $root_path),
            'resources' => sprintf('%s/resources', $root_path),
        ];

        $config = require sprintf('%s/app.php', $paths['config']);
        $this->config = DotContainer::create($config);
        $this->config->set('paths', $paths);

        $this['debug'] = $this->config->get('debug');
    }

    protected function setupFacebookServices()
    {
        $this->before(function (Request $request) {
            $request->getSession()->start();
        });

        FacebookSession::setDefaultApplication(
            $this->config()->get('facebook_app.app_id'),
            $this->config()->get('facebook_app.app_secret')
        );

        $this['facebook.login_helper'] = $this->share(
            function (Application $app) {
                return new FacebookRedirectLoginHelper(
                    $app->config()->get('facebook_app.redirect_url'),
                    $app->config()->get('facebook_app.app_id'),
                    $app->config()->get('facebook_app.app_secret')
                );
            }
        );
    }

    protected function registerProviders()
    {
        $this->register(new Silex\Provider\ServiceControllerServiceProvider());
        $this->register(new Silex\Provider\TwigServiceProvider(), [
            'debug'            => $this['debug'],
            'strict_variables' => $this['debug']
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
    }

    protected function registerRoutes()
    {
        $this->mount('/', new Controller\Home());
        $this->mount('/account', new Controller\Account()); // Login Meli
        $this->mount('/listing', new Controller\Listing()); // Productos Meli
        $this->mount('/service', new Controller\Service()); // Facebook Account Link
        $this->mount('/settings', new Controller\Settings()); // Facebook Account Link
    }
}
