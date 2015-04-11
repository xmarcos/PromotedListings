<?php

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;


class FacebookAdsHelper {


    protected static $instance;
	
	
    /**
     * Singleton
     * @return this
     */
	public function getInstance($access_token = '') {
	    if(!isset(self::$instance)) {
		    Api::init(
			    '1567775303492642', //app_id
				'6dd7567d4887134f154554a0dd4bab46', //app_secret
				'CAAWR4iINZACIBANVzM21rxuKYIndwieZBX5QtpCZBZB1jzE9dOKLP8EZAjHy9E3T3IPF33Ptbx69raJ8KPyqgnB6ASlxDq9ZBhI7BbuLndAyDBigAWjOZCcZCtovV4vOGAxEEWOrXaSd1CQy2CH40hMZAX1m8TVRbicahCoQ8uGPqLjnFUFPYZAMjfeVgj9RySpshmkCQwCoFJZCwP8pCaK48lG3nueM4JtgMUZD'
			);
		    self::$instance = Api::instance();     
		}
		return self::$instance;
	}
	
	
	
	public static function getAccountData($account_id) {
	    $fields = array(
            AdAccountFields::ID,
            AdAccountFields::NAME,
            AdAccountFields::DAILY_SPEND_LIMIT,
			AdAccountFields::BALANCE,
        );
		$account = new AdAccount($account_id);
		
		return $account->read($fields)->getData());
	}
	
	
	
	public static function createCampaign() {
	    
	}
	
	
	public static function createAdset() {
	
	}
	
	public static function createAd() {
	
	}
	

	
   
}