<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Account extends BaseController
{
    public function connect(Application $app)
    {
        $this->app   = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'accountIndex']);
        $controllers->get('/login', [$this, 'accountLogin']);
        return $controllers;
    }

    public function accountIndex(Request $request, Application $app)
    {
        return $app['twig']->render('account/index.html.twig');
    }

    public function accountLogin(Request $request, Application $app) {

        $meli = new \Meli(
            $app->config()->get('credentials.meli_app.app_id'),
            $app->config()->get('credentials.meli_app.app_secrets')
        );

        $auth_url = $app->config()->get('credentials.meli_app.app_url');

        if ($request->get('code')) {
            $oAuth = $meli->authorize($request->get('code'), $auth_url);
            /*$access_token = $oAuth['body']->access_token;
            $_SESSION['access_token'] = $access_token;
            echo $access_token;*/
            dump($oAuth);
            die();
        } else {
            $login_url = $meli->getAuthUrl($auth_url);
            return $app['twig']->render('account/login.html.twig', array('login_url' => $login_url));
        }
       //return $app['twig']->render('account/login.html.twig', array('login_url' => $login_url));
    }
}
