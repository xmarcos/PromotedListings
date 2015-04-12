<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Listing extends BaseController {

    public function connect(Application $app) {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'listingIndex']);

        return $controllers;
    }

    public function listingIndex(Request $request, Application $app) {
        $meli = new \Meli(
                $app->config()->get('credentials.meli_app.app_id'), $app->config()->get('credentials.meli_app.app_secret'), $_SESSION['access_token']
        );

        $access_token = $_SESSION['access_token'];
        $user_id = $_SESSION['user_id'];

        //Search my own items
        //path
        $path = 'users/' . $user_id . '/items/search';
        //Params
        $params = array(
            'access_token' => $access_token,
            'attributes' => 'results'
        );
        if ($request->get('q')) {
            $params['q'] = $request->get('q');
        }
        //Request
        $search_response = $meli->get($path, $params);
        $items_ids = $search_response['body']->results;
        $items_ids_str = implode($items_ids);

        //Get basic info of my own
        $items_response = $meli->get('items', array('access_token' => $access_token, 'ids' => $items_ids_str, 'attributes' => 'title,subtitle,thumbnail,base_price,currency_id'));
        $items = $items_response['body'];

        return $app['twig']->render('listing/index.html.twig', array('items' => $items));
    }

}
