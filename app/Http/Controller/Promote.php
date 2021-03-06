<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Facebook\FacebookRequest;

class Promote extends BaseController {

    public function connect(Application $app) {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'promoteIndex']);
        $controllers->get('/facebook/ads', [$this, 'promoteFacebookAds']);
        $controllers->get('/facebook/pages', [$this, 'promoteFacebookPages'])->bind('promote_pages');
        $controllers->get('/facebook/events', [$this, 'promoteFacebookEvents'])->bind('promote_events');
        $controllers->get('/facebook/groups', [$this, 'promoteFacebookGroups'])->bind('promote_groups');

        return $controllers;
    }

    public function promoteIndex(Request $request, Application $app) {
        return $app['twig']->render('promote/index.html.twig');
    }

    public function promoteFacebookAds(Request $request, Application $app) {
        return $app['twig']->render('promote/facebook/ads.html.twig');
    }

    public function promoteFacebookPages(Request $request, Application $app) {
        $item_id = $request->get('item_id');
        if ($app['facebook.api_service']->getAccessToken()) {
            if ($request->get('page_id')) {
                $page_id = $request->get('page_id');
                $item = (array) $this->getItem($app, $item_id);

                $request_page = new FacebookRequest($app['facebook.api_service']->getSession(), 'GET', '/' . $page_id, array('fields' => 'id,access_token'));
                $page = $request_page->execute()->getGraphObject()->asArray();

                $request_publish = (new FacebookRequest($app['facebook.api_service']->getSession(), 'POST', '/' . $page['id'] . '/feed', array('access_token' => $page['access_token'], 'link' => $item['permalink'], 'message' => $item['title'])));
                //$status = (isset($request_publish->execute()->getGraphObject()->asArray()['id']));
                $response = $request_publish->execute()->getGraphObject()->asArray();
                $status = (isset($response['id']));

                return $app['twig']->render('promote/congratulations.html.twig', array('status' => $status));
            } else {
                $request = new FacebookRequest($app['facebook.api_service']->getSession(), 'GET', '/me/accounts', array('fields' => 'id,name,access_token'));
                $pages = $request->execute()->getGraphObject()->asArray()['data'];
                return $app['twig']->render('promote/facebook/pages.html.twig', array('pages' => $pages, 'item_id' => $item_id));
            }
        } else {
            $_SESSION['redirect_login'] = $app->path('promote_pages', array('item_id' => $item_id));

            return $app->redirect(
                            $app->path('facebook_connect')
            );
        }
    }

    public function promoteFacebookEvents(Request $request, Application $app) {
        $item_id = $request->get('item_id');
        if ($app['facebook.api_service']->getAccessToken()) {

            if ($request->get('event_id')) {
                $event_id = $request->get('event_id');
                $item = (array) $this->getItem($app, $item_id);

                $request_publish = (new FacebookRequest($app['facebook.api_service']->getSession(), 'POST', '/' . $event_id . '/feed', array('link' => $item['permalink'], 'message' => $item['title'])));
                $response = $request_publish->execute()->getGraphObject()->asArray();
                $status = (isset($response['id']));

                return $app['twig']->render('promote/congratulations.html.twig', array('status' => $status));
            } else {
                $request = new FacebookRequest($app['facebook.api_service']->getSession(), 'GET', '/me/events', array('fields' => 'id,name'));
                $events = $request->execute()->getGraphObject()->asArray()['data'];
                return $app['twig']->render('promote/facebook/events.html.twig', array('events' => $events, 'item_id' => $item_id));
            }
        } else {
            $_SESSION['redirect_login'] = $app->path('promote_events', array('item_id' => $item_id));

            return $app->redirect(
                            $app->path('facebook_connect')
            );
        }
    }

    public function promoteFacebookGroups(Request $request, Application $app) {
        $item_id = $request->get('item_id');
        if ($app['facebook.api_service']->getAccessToken()) {

            if ($request->get('group_id')) {
                $group_id = $request->get('group_id');
                $item = (array) $this->getItem($app, $item_id);

                $request_publish = (new FacebookRequest($app['facebook.api_service']->getSession(), 'POST', '/' . $group_id . '/feed', array('link' => $item['permalink'], 'message' => $item['title'])));
                $response = $request_publish->execute()->getGraphObject()->asArray();
                $status = (isset($response['id']));

                return $app['twig']->render('promote/congratulations.html.twig', array('status' => $status));
            } else {
                $request = new FacebookRequest($app['facebook.api_service']->getSession(), 'GET', '/me/groups', array('fields' => 'id,name'));
                $groups = $request->execute()->getGraphObject()->asArray()['data'];
                return $app['twig']->render('promote/facebook/groups.html.twig', array('groups' => $groups, 'item_id' => $item_id));
            }
        } else {
            $_SESSION['redirect_login'] = $app->path('promote_groups', array('item_id' => $item_id));

            return $app->redirect(
                            $app->path('facebook_connect')
            );
        }
    }

    private function getItem($app, $item_id) {
        $user = $app['meli.authentication_service']->getCurrentUser();
        $access_token = $user->get('access_token');

        $app['meli.api']->setAccessToken($access_token);

        $item_response = $app['meli.api']->get(
                sprintf('items/%s', $item_id), [
            'access_token' => $access_token,
            'attributes' => 'permalink,title',
                ]
        );
        return $item_response['body'];
    }

}
