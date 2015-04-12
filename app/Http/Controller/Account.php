<?php

namespace PromotedListings\Http\Controller;

use Meli;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Account extends BaseController {

    public function connect(Application $app) {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'accountIndex'])->bind('account_dashboard');
        $controllers->get('/login', [$this, 'accountLogin'])->bind('login');
        $controllers->get('/logout', [$this, 'accountLogout'])->bind('logout');

        return $controllers;
    }

    public function accountIndex(Request $request, Application $app)
    {
        return $app['twig']->render('account/index.html.twig');
    }

    public function accountLogin(Request $request, Application $app)
    {
        if ($app['meli.authentication_service']->hasActiveSession()) {
            return $app->redirect(
                $app->path('account_dashboard')
            );
        }

        $auth_code = $request->query->get('code');

        if (empty($auth_code)) {
            return new RedirectResponse(
                $app['meli.authentication_service']->getAuthUrl()
            );
        }

        $access_token = $app['meli.authentication_service']->getAccessTokenFromCode($auth_code);

        if (!empty($access_token)) {
            $user_info = $app['meli.authentication_service']->getUserInfoFromAccessToken($access_token);
            if (!empty($user_info)) {
                $save = $app['meli.authentication_service']->saveUser($user_info);
                if ($save) {
                    $app['meli.authentication_service']->loginUser($user_info->get('id'));
                    $redirect = $app['session']->has('redirect_url')
                        ? $app['session']->get('redirect_url')
                        : $app->path('account_dashboard');

                    return $app->redirect($redirect);

                } else {
                    die('Unable to save user(?)');
                }
            } else {
                die('Empty user Info');
            }
        } else {
            die('Empty Access Token');
        }
    }

    public function accountLogout(Request $request, Application $app)
    {
        $app['meli.authentication_service']->logoutUser();

        return $app->redirect(
            $app->path('account_dashboard')
        );
    }
}
