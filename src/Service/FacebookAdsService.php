<?php

namespace PromotedListings\Service;

use DateTime;
use Exception;
use Doctrine\DBAL\Connection;
use Facebook\Entities\AccessToken;
use FacebookAds\Api;
use FacebookAds\Object\AdUser;
use FacebookAds\Object\Fields\AdAccountFields;

class FacebookAdsService
{
    protected $db;
    protected $api;
    protected $app_id;
    protected $app_secret;
    protected $access_token;

    public function __construct(
        $app_id,
        $app_secret,
        Connection $db
    ) {
        $this->db         = $db;
        $this->app_id     = $app_id;
        $this->app_secret = $app_secret;
    }

    /**
     * This inits the API.
     */
    public function setAccessToken(AccessToken $access_token)
    {
        $this->api = Api::init(
            $this->app_id,
            $this->app_secret,
            (string) $access_token
        );
        $this->access_token = $access_token;
    }

    /**
     * Saves the AccessToken to the DB, should be in a repository but MVP.
     */
    public function saveAccessToken(AccessToken $access_token, $meli_user_id = null)
    {
        $expires = $access_token->getExpiresAt() instanceof DateTime
            ? $access_token->getExpiresAt()->getTimestamp()
            : null;

        $data = [
            'meli_user_id' => $meli_user_id,
            'access_token' => (string) $access_token,
            'expires'      => $expires,
            'updated'      => (new DateTime('NOW'))->format('Y-m-d H:i:s'),
        ];

        $this->db->beginTransaction();

        try {
            $this->db->insert('facebook_access_token', $data);
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollback();

            return false;
        }
    }

    public function getAccessTokenByMeliUserId($meli_user_id = null)
    {
        $sql = 'SELECT * FROM facebook_access_token WHERE meli_user_id = :meli_user_id LIMIT 1';
        $q = $this->db->prepare($sql);
        $q->bindValue(':meli_user_id', $meli_user_id);
        $q->execute();

        $token = $q->fetch(PDO::FETCH_ASSOC);

        return empty($token) ? null : new AccessToken(
            $token['access_token'],
            $token['expires']
        );
    }

    public function getActiveAccounts()
    {
        $data = null;

        try {
            $me       = new AdUser('me', null, $this->api);
            $accounts = $me->getAdAccounts();
            foreach ($accounts as $account) {
                try {
                    $account->read([
                        AdAccountFields::ID,
                        AdAccountFields::NAME,
                        AdAccountFields::ACCOUNT_STATUS,
                    ]);
                    if ($account->{AdAccountFields::ACCOUNT_STATUS} == 1) {
                        $data[$account->id] = $account;
                    }
                } catch (Exception $e) {
                }
            }
        } catch (Exception $e) {
            $data = $e->getMessage();
        }

        return $data;
    }

    public function saveAccounts($accounts = [])
    {
        return $data;
    }
}
