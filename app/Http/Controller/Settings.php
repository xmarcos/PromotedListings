<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Facebook\FacebookSession;
use Facebook\Entities\AccessToken;

function jlog($data) {
    $data = is_object($data) ? get_object_vars($data) : $data;
    file_put_contents(
            __DIR__ . '/log.json', json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND
    );
}

class Settings extends BaseController {

    public function connect(Application $app) {
        $this->app = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'settingsIndex']);
        $controllers->get('/facebook/connect', [$this, 'facebookConnect'])->bind('facebook_connect');
        $controllers->get('/facebook/callback', [$this, 'facebookCallback']);
        $controllers->get('/facebook/accounts', [$this, 'facebookAccounts']);

        return $controllers;
    }

    public function settingsIndex(Request $request, Application $app) {
        return $app['twig']->render('settings/index.html.twig');
    }

    public function facebookAccounts(Request $request, Application $app) {
        $access_token = new AccessToken('CAAWR4iINZACIBAIbwZCxtcFzvghAqntIZA3E2pssJ5zHz2tnshKCPrQjJZBrUEejipn5x2kfsYQnSqeYcBTUBAkv5EFH6II4RwnVapAbyTO5kTHZAmq0jTezNYk6LvZCzsJ9wJHRf1oyYCpaB3vzFRSrjEuqf0CPkyPuqW6zeIvbSXjL8q8isfjoZBAtn9xSONBYjG7jZAhB2ilN310ddXnUj0OPHVp989o4hYk6muJf3wZDZD');

        $app['facebook.ad_service']->setAccessToken($access_token);

        $ad_accounts = $app['facebook.ad_service']->getActiveAccounts();
        //dump($ad_accounts);
        die();
    }

    public function facebookConnect(Request $request, Application $app) {
        return new RedirectResponse(
                $app['facebook.login_helper']->getLoginUrl(
                        $app->config()->get('credentials.facebook_app.scope', [])
                )
        );
    }

    public function facebookCallback(Request $request, Application $app) {
        $success = false;
        $error_message = '';
        $access_token = null;

        try {
            $session = $app['facebook.login_helper']->getSessionFromRedirect();
            if ($session instanceof FacebookSession) {
                $access_token = $session->getAccessToken();
                if (!$access_token->isLongLived()) {
                    $access_token = $access_token->extend();
                }

                $meli_user = $app['meli.authentication_service']->getCurrentUser();
                $success = $app['facebook.ad_service']->saveAccessToken(
                        $access_token, $meli_user->get('user_id')
                );
            } else {
                $error_message = "Error while connection to Facebook, please try again later.";
            }
        } catch (FacebookRequestException $e) {
            $error_message = $e->getMessage();
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        if ($success && $access_token instanceof AccessToken) {
            $app['facebook.ad_service']->setAccessToken($access_token);
            $app['facebook.api_service']->setAccessToken($access_token);
            //$accounts = $app['facebook.ad_service']->getActiveAccounts();
        } else {
            //dump($error_message);
        }

        return $app->redirect($_SESSION['redirect_login']);
    }

}
