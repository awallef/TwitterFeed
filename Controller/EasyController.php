<?php

App::uses('TwitterFeedAppController', 'TwitterFeed.Controller');

$baseOAuth2Path = CakePlugin::path('OAuth2') . 'Lib' . DS .  'Network' . DS . 'Http' . DS ;
require_once($baseOAuth2Path . 'OAuth2CakeUtils.php');

class EasyController extends TwitterFeedAppController {
    
    public $uses = array();
    
    public function index(){
        
        $consumer_key = 'ppbO26R0KndIwc72QIU5oA';
        $consumer_secret = 'CxnqCQ9HcQrM5wq9pxflRpkphRHApnnz27poFYjBkeI';
        
        $oAuth2Utils = new OAuth2CakeUtils();
        debug( $oAuth2Utils->obtainBearerToken( $consumer_key, $consumer_secret, 'https://api.twitter.com/oauth2/token' ) );
    }
    
    
}
