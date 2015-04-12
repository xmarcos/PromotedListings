<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Listing extends BaseController {

    public function connect(Application $app) {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'listingIndex'])->bind('listing');
        $controllers->get('/search', [$this, 'listingIndex'])->bind('listing_search');
        $controllers->get('/item/{item_id}', [$this, 'listingItem'])->bind('item');

        return $controllers;
    }

    public function listingIndex(Request $request, Application $app) {
        $access_token = $_SESSION['meli_account']['access_token'];
        $user_id = $_SESSION['meli_account']['user_id'];

        $meli = new \Meli(
                $app->config()->get('credentials.meli_app.app_id'), $app->config()->get('credentials.meli_app.app_secret'), $access_token
        );

        //Search my own items
        //path
        $path = 'users/' . $user_id . '/items/search';
        //Params
        $params = array(
            'access_token' => $access_token,
            'attributes' => 'results,paging'
        );
        if ($request->get('q')) {
            $params['q'] = $request->get('q');
        }
        //Request
        $search_response = $meli->get($path, $params);
        $items_ids = $search_response['body']->results;
        $items_ids_str = implode(',',$items_ids);
        $total_items = $search_response['body']->paging->total;
        if($total_items == 0) {
            if(empty($params['q']))
                $message = 'no existen articulos en su cuenta';
                $message = 'no existen articulos con la siguiente busqueda: '.$params['q'];
        }
        //Get basic info of my own
        $items_response = $meli->get('items', array('access_token' => $access_token, 'ids' => $items_ids_str, 'attributes' => 'id,title,subtitle,thumbnail,base_price,currency_id'));
        $items = $items_response['body'];
        return $app['twig']->render('listing/index.html.twig', array('items' => $items, 'total_items' => $total_items, 'message' => $message));
    }

    public function listingItem(Request $request, Application $app) {
        $access_token = $_SESSION['meli_account']['access_token'];
        
        $meli = new \Meli(
                $app->config()->get('credentials.meli_app.app_id'), $app->config()->get('credentials.meli_app.app_secret'), $access_token
        );

        if ($request->get('item_id')) {
            $item_id = $request->get('item_id');
            //Get basic info of my own
            $item_response = $meli->get('items/' . $item_id, array('access_token' => $access_token, 'attributes' => 'id,title,subtitle,thumbnail,base_price,currency_id'));
            $item = $item_response['body'];
            return $app['twig']->render('listing/item_detail.html.twig', array('item' => $item));
        }
    }
   
}
