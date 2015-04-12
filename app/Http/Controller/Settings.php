<?php

namespace PromotedListings\Http\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Facebook\FacebookSession;
use FacebookAds\Api;
use Facebook\Entities\AccessToken;

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
        // $controllers->get('/facebook/callback', [$this, 'facebookCallback']);
        $controllers->get('/facebook/accounts', [$this, 'facebookAccounts']);

        return $controllers;
    }

    public function settingsIndex(Request $request, Application $app)
    {
        return $app['twig']->render('settings/index.html.twig');
    }

    public function facebookAccounts(Request $request, Application $app)
    {
        $access_token = new AccessToken("CAAWR4iINZACIBAJ6RYdgDT4AtIgyeytEVlH4oiDshBt6W1j6ZBDwVI0kz0ljj3JxpCvcDQ6KaGnDSYYqq0er70xhyMZA8h4OoprwoZAbuqVkPtm8fQyRGy87sj1fo2N46d3X2RtkvQ4ew7zNV7bhFYOwGvz4ZCpZC4H05fanP14mzGgBqKQc58kYHp34c4cpDr1aQblPRZB9hstxlflTQF0gseCnH4eapsPmKQiP2jtiwZDZD");

        $app['facebook.ad_service']->setAccessToken($access_token);

        $ad_accounts = $app['facebook.ad_service']->getActiveAccounts();
        dump($ad_accounts);
        die();
    }

    // public function facebookCallback(Request $request, Application $app)
    // {
    //     $success       = false;
    //     $error_message = '';
    //     $access_token  = null;

    //     try {
    //         $session = $app['facebook.login_helper']->getSessionFromRedirect();
    //         if ($session instanceof FacebookSession) {
    //             $access_token = $session->getAccessToken();
    //             if (!$access_token->isLongLived()) {
    //                 $access_token = $access_token->extend();
    //             }

    //             $success = $app['facebook.ad_service']->saveAccessToken($access_token, 1);
    //         } else {
    //             $error_message = "Error while connection to Facebook, please try again later.";
    //         }
    //     } catch (FacebookRequestException $e) {
    //         $error_message = $e->getMessage();
    //     } catch (Exception $e) {
    //         $error_message = $e->getMessage();
    //     }
    // }

    // public function facebookCallback(Request $request, Application $app)
    // {
    //     $success       = false;
    //     $error_message = '';
    //     $access_token  = null;

    //     try {
    //         $session = $app['facebook.login_helper']->getSessionFromRedirect();
    //         if ($session instanceof FacebookSession) {
    //             $access_token = $session->getAccessToken();
    //             if (!$access_token->isLongLived()) {
    //                 $access_token = $access_token->extend();
    //             }

    //             $success = $app['facebook.ad_service']->saveAccessToken($access_token, 1);
    //         } else {
    //             $error_message = "Error while connection to Facebook, please try again later.";
    //         }
    //     } catch (FacebookRequestException $e) {
    //         $error_message = $e->getMessage();
    //     } catch (Exception $e) {
    //         $error_message = $e->getMessage();
    //     }

    //     if (!$error && $access_token instanceof AccessToken) {

    //         $app['facebook.ad_service']->setAccessToken($access_token);

    //         $accounts = $app['facebook.ad_service']->getActiveAccounts();

    //         dump($accounts);
    //     } else {
    //         dump($error);
    //     }


    //         // if (is_array($ad_accounts)) {
    //         //     $i = 0;
    //         //     foreach ($ad_accounts as $account) {
    //         //         try {
    //         //             $exists = $app['repository.fb.ad_account']->getByAdAccount($account);
    //         //             // check if we have an account on that id connected to another customer
    //         //             if (!is_null($exists) && $exists->getCustomerId() !== $app['account']->get('customer')->getId()) {
    //         //                 $app['session']->getFlashBag()->add(
    //         //                     'error',
    //         //                     sprintf(
    //         //                         'Ad Account %s (%s) is already connected to another Customer.',
    //         //                         $account->getName(),
    //         //                         $account->getId()
    //         //                     )
    //         //                 );
    //         //             } else {
    //         //                 $account->setCustomerId($app['account']->get('customer')->getId());
    //         //                 $account->setCanUpload($ad_service->canUploadToAccount($account));
    //         //                 $account->setEnabled($i === 0);
    //         //                 $save = $app['repository.fb.ad_account']->upsert($account);
    //         //                 if ($save) {
    //         //                     $i++;
    //         //                     $app['session']->getFlashBag()->add(
    //         //                         'success',
    //         //                         sprintf('Ad Account Connected - %s (%s)', $account->getName(), $account->getId())
    //         //                     );
    //         //                 }
    //         //             }
    //         //         } catch (Exception $e) {
    //         //             $msg = $app['debug'] ? sprintf('%s - %s', get_class($e), $e->getMessage()) : 'Internal Error';
    //         //             $app['session']->getFlashBag()->add('error', $msg);
    //         //         }
    //         //     }
    //         // } else {
    //         //     $app['session']->getFlashBag()->add(
    //         //         'error',
    //         //         sprintf('No active Ad Account was found')
    //         //     );
    //         // }
    //     // }

    //     // if ($error) {
    //     //     $app['session']->getFlashBag()->add(
    //     //         'error',
    //     //         'The Application was not Authorized. Try again.'
    //     //     );
    //     // }

    //     // return $app->redirect(
    //     //     $app->path('account_dashboard')
    //     // );


    //     dump($request);
    //     die();

}
