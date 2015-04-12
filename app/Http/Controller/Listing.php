<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Listing extends BaseController
{
    public function connect(Application $app)
    {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'listingIndex'])->bind('listing');
        $controllers->get('/search', [$this, 'listingIndex'])->bind('listing_search');
        $controllers->get('/item/{item_id}', [$this, 'listingItem'])->bind('item');

        return $controllers;
    }

    public function listingIndex(Request $request, Application $app)
    {
        $user         = $app['meli.authentication_service']->getCurrentUser();
        $access_token = $user->get('access_token');
        
        $app['meli.api']->setAccessToken($access_token);

        $path  = sprintf('/users/%s/items/search', $user->get('user_id'));
        $query = $request->get('q');

        $params = [
            'access_token' => $access_token,
            'status' => 'active',
            'attributes'   => 'results,paging',
        ];
        if (!empty($query)) {
            $params['q'] = $query;
        }

        $search_response = $app['meli.api']->get($path, $params);
        $items_ids       = $search_response['body']->results;
        $items_ids_str   = implode(',', $items_ids);
        $total_items     = $search_response['body']->paging->total;
        $message         = '';

        if ($total_items == 0) {
            if (!isset($params['q']))
                $message = 'no existen articulos en su cuenta';
            else
                $message = 'no existen articulos con la siguiente busqueda: '.$params['q'];
        }

        $items_response = $app['meli.api']->get(
            'items',
            [
                'access_token' => $access_token,
                'ids'          => $items_ids_str,
                'attributes'   => 'id,title,subtitle,thumbnail,base_price,currency_id',
            ]
        );

        $items = $items_response['body'];
        
        return $app['twig']->render(
            'listing/index.html.twig',
            [
                'items' => $items,
                'total_items' => $total_items,
                'message' => $message,
            ]
        );
    }

    public function listingItem(Request $request, Application $app)
    {
        $user         = $app['meli.authentication_service']->getCurrentUser();
        $access_token = $user->get('access_token');

        $app['meli.api']->setAccessToken($access_token);

        if ($request->get('item_id')) {
            $item_id       = $request->get('item_id');
            $item_response = $app['meli.api']->get(
                sprintf('items/%s', $item_id),
                [
                    'access_token' => $access_token,
                    'attributes'   => 'id,title,subtitle,thumbnail,base_price,currency_id',
                ]
            );
            $item = $item_response['body'];

            return $app['twig']->render(
                'listing/item_detail.html.twig',
                [
                    'item' => $item,
                ]
            );
        }
    }
}
