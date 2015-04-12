<?php

namespace PromotedListings\Service;

use PDO;
use Meli;
use DateTime;
use Exception;
use Doctrine\DBAL\Connection;
use xmarcos\Dot\Container as DotContainer;

class MeliAuthenticationService
{
    protected $db;
    protected $meli;
    protected $session;
    protected $redirect_uri;
    protected $access_token;

    public function __construct(
        Meli $meli,
        Connection $db,
        $session,
        $redirect_uri = null
    ) {
        $this->db           = $db;
        $this->meli         = $meli;
        $this->session      = $session;
        $this->redirect_uri = $redirect_uri;
    }

    public function hasActiveSession()
    {
        return $this->session->has('meli_user');
    }

    public function getCurrentUser()
    {
        return $this->session->get('meli_user');
    }

    public function loginUser($user_id)
    {
        $meli_user = $this->getUserById($user_id);

        if ($meli_user) {
            $meli_user->set('access_token', $this->access_token);
            $this->session->set('meli_user', $meli_user);
        }
    }

    public function logoutUser()
    {
        $this->session->invalidate();
    }

    public function getAuthUrl()
    {
        return $this->meli->getAuthUrl($this->redirect_uri);
    }

    public function getAccessTokenFromCode($code)
    {
        $response = $this->meli->authorize($code, $this->redirect_uri);

        $data = $this->getDataFromResponse($response);
        if (!empty($data)) {
            $this->access_token = $data->get('access_token');
        }

        return $data;
    }

    public function getUserInfoFromAccessToken(DotContainer $access_token)
    {
        $response = $this->meli->get('/users/'.$access_token->get('user_id'));

        return $this->getDataFromResponse($response);
    }

    private function getDataFromResponse($response)
    {
        $data = null;

        if (
            is_array($response)
            && isset($response['httpCode'])
            && 200 == $response['httpCode']
            && !empty($response['body'])
        ) {
            $data = DotContainer::create((array) $response['body']);
        }

        return $data;
    }

    public function getUserById($user_id)
    {
        $sql = 'SELECT * FROM meli_account WHERE user_id = :user_id LIMIT 1';
        $q = $this->db->prepare($sql);
        $q->bindValue(':user_id', $user_id);
        $q->execute();

        $user = $q->fetch();

        if (isset($user['data'])) {
            $user['data'] = json_decode($user['data'], true);
        }

        return empty($user) ? null : DotContainer::create($user);
    }

    public function saveUser(DotContainer $user_info)
    {
        $data = [
            'user_id'    => (int) $user_info->get('id'),
            'nickname'   => $user_info->get('nickname'),
            'country_id' => $user_info->get('country_id'),
            'site_id'    => $user_info->get('site_id'),
            'data'       => json_encode($user_info->all()),
            'updated'    => (new DateTime('NOW'))->format('Y-m-d H:i:s'),
        ];

        $this->db->beginTransaction();
        try {
            $sql = 'SELECT count(*) as users FROM meli_account WHERE user_id = :user_id';
            $q = $this->db->prepare($sql);
            $q->bindValue(':user_id', $data['user_id']);
            $q->execute();

            //we already have an user on that id, ignore
            if ((int) $q->fetch(PDO::FETCH_OBJ)->users > 0) {
                return true;
            }

            $this->db->insert('meli_account', $data);
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            // throw $e;
            return false;
        }
    }
}
