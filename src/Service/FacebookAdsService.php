<?php

namespace PromotedListings\Service;

use DateTime;
use Exception;
use Doctrine\DBAL\Connection;
use Facebook\Entities\AccessToken;

class FacebookAdsService
{
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

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
            throw $e;

            return false;
        }
    }
}
