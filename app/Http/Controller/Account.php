<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Account extends BaseController {

    public function connect(Application $app) {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'accountIndex'])->bind('account_dashboard');
        $controllers->get('/login', [$this, 'accountLogin'])->bind('login');

        return $controllers;
    }

    public function accountIndex(Request $request, Application $app) {
        $is_user = isset($_SESSION['meli_account']);
        return $app['twig']->render('account/index.html.twig', array('is_user' => $is_user));
    }

    public function accountLogin(Request $request, Application $app) {
        $meli = new \Meli(
                $app->config()->get('credentials.meli_app.app_id'), $app->config()->get('credentials.meli_app.app_secret')
        );

        $auth_url = $app->config()->get('credentials.meli_app.app_url');
        if ($request->get('code')) {
            $oAuth = $meli->authorize($request->get('code'), $auth_url);

            // complete meli account
            //call to users/{user_id}
            $dataUser = $meli->get('/users/' . $oAuth['body']->user_id);

            $meli_account['user_id'] = $oAuth['body']->user_id;
            $meli_account['nickname'] = $dataUser['body']->nickname;
            $meli_account['country_id'] = $dataUser['body']->country_id;
            $meli_account['site_id'] = $dataUser['body']->site_id;
            $meli_account['data'] = $dataUser['body'];
            $meli_account['access_token'] = $oAuth['body']->access_token;

            if ($meli_account['access_token']) {
                //$dataFb = $app['db']->fetchAll('SELECT * FROM facebook_access_token');
                //$this->saveData($meli_account);
                $table = 'meli_account';
                $existsUser = $app['db']->fetchAssoc("SELECT * FROM $table WHERE user_id = :user_id", array(
                    'user_id' => $meli_account['user_id']
                ));
                if ($existsUser === false) {
                    try {
                        $result = $app['db']->insert($table, array(
                            'user_id' => $meli_account['user_id'],
                            'nickname' => $meli_account['nickname'],
                            'country_id' => $meli_account['country_id'],
                            'site_id' => $meli_account['site_id'],
                            'data' => json_encode($dataUser['body']),
                        ));
                    } catch (Exception $exc) {
                        // We should display message error.
                    }
                }
                $_SESSION['meli_account'] = $meli_account;

                return $app->redirect($app->path('account_dashboard'));
            }
        } else {
            return new RedirectResponse($meli->getAuthUrl($auth_url));
        }
    }

    protected function saveData($params, $table = 'meli_account') {
        var_dump($params);
        die();
    }

}
