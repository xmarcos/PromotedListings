<?php
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\AdGroup;
use FacebookAds\Object\Fields\AdGroupFields;
use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\Search\TargetingSearchTypes;
use FacebookAds\Object\Fields\AdGroupBidInfoFields;
use FacebookAds\Object\Values\BidTypes;



class FacebookAdsHelper {


    protected static $instance;

	
	public function setApiInstance(Api $adsApiInstance) {
	    //instanceof FacebookAds\Api
	    self::$instance = $adsApiInstance;
	}
	
	
	public static function getToken() {
	     return self::$instance->getSession()->getAccessToken();   
	}
	
	
	public static function getAccountData($account_id) {
	    $fields = array(
            AdAccountFields::ID,
            AdAccountFields::NAME,
            AdAccountFields::DAILY_SPEND_LIMIT,
			AdAccountFields::BALANCE,
        );
		$account = new AdAccount($account_id);
				
		return $account->read($fields)->getData();
	}
	
	
	
	public static function createCampaign() {
	    
	}
	
	
	public static function createAdset() {
	
	}
	
	public static function createAd() {
	
	}
	
	
}