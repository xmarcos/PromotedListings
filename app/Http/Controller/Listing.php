<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Listing extends BaseController
{
    public function connect(Application $app)
    {
        $this->app   = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'listingIndex']);

        return $controllers;
    }

    public function listingIndex(Request $request, Application $app)
    {
        return $app['twig']->render('listing/index.html.twig');
    }
}
