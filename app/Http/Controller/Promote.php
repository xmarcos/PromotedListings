<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Promote extends BaseController {

    public function connect(Application $app) {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'promoteIndex']);
        $controllers->get('/facebook/ads', [$this, 'accountLogin']);
        $controllers->get('/facebook/pages', [$this, 'accountLogin']);
        $controllers->get('/facebook/events', [$this, 'accountLogin']);
        $controllers->get('/facebook/groups', [$this, 'accountLogin']);

        return $controllers;
    }

    public function promoteIndex(Request $request, Application $app) {
        return $app['twig']->render('promote/index.html.twig');
    }

    /*public function promoteFacebookAds(Request $request, Application $app) {
        return $app['twig']->render('promote/facebook/ads.html.twig');
    }

    public function promoteFacebookPages(Request $request, Application $app) {
        if ($request->get('page_id')) {
            $page_id = $request->get('page_id');

            $request_page = new Facebook\FacebookRequest($app['facebook.api_session'], 'GET', '/' . $page_id, array('fields' => 'id,access_token'));
            $page = $request_page->execute()->getGraphObject()->asArray();

            $request_publish = (new Facebook\FacebookRequest($app['facebook.api_session'], 'POST', '/' . $page['id'] . '/feed', array('access_token' => $page['access_token'], 'link' => $post['link'], 'message' => $post['message'])));
            $status = (isset($request_publish->execute()->getGraphObject()->asArray()['id']));

            return $app['twig']->render('promote/congratulations.html.twig', array('status' => $status));
        } else {
            $request = new Facebook\FacebookRequest($app['facebook.api_session'], 'GET', '/me/accounts', array('fields' => 'id,name,access_token'));
            $pages = $request->execute()->getGraphObject()->asArray()['data'];
            return $app['twig']->render('promote/facebook/pages.html.twig', array('pages' => $pages));
        }
    }

    public function promoteFacebookEvents(Request $request, Application $app) {
        if ($request->get('event_id')) {
            $event_id = $request->get('event_id');

            $request_publish = (new Facebook\FacebookRequest($app['facebook.api_session'], 'POST', '/' . $event_id . '/feed', array('link' => $post['link'], 'message' => $post['message'])));
            $status = (isset($request_publish->execute()->getGraphObject()->asArray()['id']));

            return $app['twig']->render('promote/congratulations.html.twig', array('status' => $status));
        } else {
            $request = new Facebook\FacebookRequest($app['facebook.api_session'], 'GET', '/me/events', array('fields' => 'id,name'));
            $events = $request->execute()->getGraphObject()->asArray()['data'];
            return $app['twig']->render('promote/facebook/events.html.twig', array('events' => $events));
        }
    }

    public function promoteFacebookGroups(Request $request, Application $app) {
        if ($request->get('group_id')) {
            $group_id = $request->get('group_id');

            $request_publish = (new Facebook\FacebookRequest($app['facebook.api_session'], 'POST', '/' . $group_id . '/feed', array('link' => $post['link'], 'message' => $post['message'])));
            $status = (isset($request_publish->execute()->getGraphObject()->asArray()['id']));

            return $app['twig']->render('promote/congratulations.html.twig', array('status' => $status));
        } else {
            $request = new Facebook\FacebookRequest($app['facebook.api_session'], 'GET', '/me/groups', array('fields' => 'id,name'));
            $groups = $request->execute()->getGraphObject()->asArray()['data'];
            return $app['twig']->render('promote/facebook/groups.html.twig', array('groups' => $groups));
        }
    }*/

}
