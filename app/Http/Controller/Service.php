<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Service extends BaseController
{
    public function connect(Application $app)
    {
        $this->app   = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'serviceIndex']);

        return $controllers;
    }

    public function serviceIndex(Request $request, Application $app)
    {
        return $app['twig']->render('service/index.html.twig');
    }
}
