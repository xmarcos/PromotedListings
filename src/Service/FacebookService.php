<?php

namespace PromotedListings\Service;

use Facebook\FacebookSession;

class FacebookService {

    protected $session;
    protected $access_token;

    public function __construct($access_token) {
        $this->access_token = $access_token;
    }

    public function setAccessToken($access_token) {
        $this->access_token = $access_token;
        $this->session = null;
    }

    public function getSession() {
        if (!is_null($this->access_token)) {
            if (is_null($this->session)) {
                $this->session = new FacebookSession($this->access_token);
            }
            return $this->session;
        }
        return null;
    }

}
