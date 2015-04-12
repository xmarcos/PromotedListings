<?php
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\AdCampaign;
use FacebookAds\Object\Fields\AdCampaignFields;
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
	
	
	protected static $account_id;
	
	
	
	public function setApiInstance(Api $adsApiInstance) {
	    //instanceof FacebookAds\Api
	    self::$instance = $adsApiInstance;
	}
	
	public static function setAccountId($account_id) {
	    self::$account_id = $account_id;
	}
	
	
	public static function getAccessToken() {
		$access_token = self::$instance->getSession()->getAccessToken();	
        return $access_token;   
    }
	
	public static function getAccountData($account_id) {
	    $fields = array(
            AdAccountFields::ID,
            AdAccountFields::NAME,
            AdAccountFields::DAILY_SPEND_LIMIT
        );
		$account = new AdAccount($account_id);
				
		return $account->read($fields)->getData();
	}
		
	
	
	public static function createAdCampaign($data) {	
	    $campaign = new AdCampaign(null, self::$account_id); 	    
		$campaign->create(array(
            AdCampaignFields::NAME      => $data['name'],
            AdCampaignFields::STATUS    => AdCampaign::STATUS_PAUSED,
            AdCampaignFields::OBJECTIVE => 'WEBSITE_CLICKS' 
        )); 
		
		return $campaign;		
	}
	
	
	
	public static function createAdset($data) {
	    $params = array(
            AdSetFields::NAME     => $data['name'],
            AdSetFields::BID_TYPE => 'CPC',
            AdSetFields::BID_INFO => array(
                    'CLICKS' => 500,
                ),
            AdSetFields::CAMPAIGN_STATUS    => 'PAUSED',
            AdSetFields::LIFETIME_BUDGET    => $data['inversion'],
            AdSetFields::START_TIME         => $data['start_time'],
            AdSetFields::END_TIME           => $data['end_time'],
            AdSetFields::CAMPAIGN_GROUP_ID  => $data['campaign_id'],          
            AdSetFields::TARGETING          => array(
                    'genders' => array(1,2),
                    'age_min' => '18',
                    'age_max' => '40',
                    'geo_locations' => array(
                        'countries' => array('US','GB',),
                    ),
                    'page_types' => array('rightcolumn')
            )
        );
	
	    try {
	        $adset = new AdSet(null, self::$account_id);
		    $adset->create($params);
		} catch (Exception $e) {
		    echo $e->getMessage();
		}
		
        return $adset;
	}
		
	

	public static function createAdGroup($data) {
	    
	    $creative = new AdCreative(null, self::$account_id);
		try {
		    $creative->create(array(
                AdCreativeFields::TITLE      => $data['title'],
                AdCreativeFields::BODY       => $data['body'],
                AdCreativeFields::OBJECT_URL => $data['link_url'],
				AdCreativeFields::IMAGE_URL  => $data['img_url'],
            ));	
		} catch(Exception $e) {
		    echo $e->getMessage();
		}  
			
	    $fields = array(
            AdGroupFields::NAME => $data['name'],
			AdGroupFields::ADGROUP_STATUS => 'PAUSED',
            AdGroupFields::CAMPAIGN_ID => $data['adset_id'],
            AdGroupFields::CREATIVE => array(
                'creative_id' => $creative->{AdCreativeFields::ID},
            ),
        );
		
		try {
		    $ad = new AdGroup(null, self::$account_id);
            $ad->create($fields);
		} catch(Exception $e) {
		    echo $e->getMessage();
		}
		
		return $ad;
	}
	

	
}