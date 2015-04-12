<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

function jlog($data)
{
    $data = is_object($data) ? get_object_vars($data) : $data;
    file_put_contents(
        __DIR__.'/log.json',
        json_encode($data, JSON_PRETTY_PRINT).PHP_EOL,
        FILE_APPEND
    );
}

class Settings extends BaseController
{
    public function connect(Application $app)
    {
        $this->app   = $app;
        $controllers = $app['controllers_factory'];

        $controllers->get('/', [$this, 'settingsIndex']);
        $controllers->get('/facebook/connect', [$this, 'facebookConnect']);
        $controllers->get('/facebook/callback', [$this, 'facebookCallback']);
        // $controllers->get('/facebook/accounts', [$this, 'facebookCallback']);

        return $controllers;
    }

    public function settingsIndex(Request $request, Application $app)
    {
        return $app['twig']->render('settings/index.html.twig');
    }

    public function facebookConnect(Request $request, Application $app)
    {
        return new RedirectResponse(
            $app['facebook.login_helper']->getLoginUrl(
                $app->config()->get('facebook_app.scope', [])
            )
        );
    }

    public function facebookCallback(Request $request, Application $app)
    {
        $error        = false;
        $access_token = null;

        try {
            $session = $app['facebook.login_helper']->getSessionFromRedirect();
            $error   = empty($session);
            if ($session) {
                $token = $session->getAccessToken();
                if (!$token->isLongLived()) {
                    $token = $token->extend();
                    $res = $app['facebook.ad_service']->saveAccessToken($token, 1);
                }
                dump($token);
                dump($res);
            }
        } catch (FacebookRequestException $e) {
            dump($e->getMessage());
        } catch (Exception $e) {
            dump($e->getMessage());
        }
        dump($request);
        die();
    }
}
