<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Home extends BaseController
{
    public function connect(Application $app)
    {
        $this->app   = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'homeIndex']);

        return $controllers;
    }

    public function homeIndex(Request $request, Application $app)
    {
        return $app['twig']->render('account/index.html.twig');
    }
}
