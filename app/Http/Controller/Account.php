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
       $Meli = new \Meli('4673127771555620', 'WbMuL0JsR7lz6nTOYWAts1IOP6ZTBH57');
       $auth_url = 'http://meli-promotedlistings.ngrok.com/account/login';
       
       if ($request->get('code')) {
           $oAuth = $Meli->authorize($request->get('code'), $auth_url);
           /*$access_token = $oAuth['body']->access_token;
           $_SESSION['access_token'] = $access_token;
           echo $access_token;*/
           echo '<pre>';
           var_dump($oAuth);
           die();
       } else {
           
           $login_url = $Meli->getAuthUrl($auth_url);
           return $app['twig']->render('account/login.html.twig', array('login_url' => $login_url));
       }

       //return $app['twig']->render('account/login.html.twig', array('login_url' => $login_url));
   }
}
