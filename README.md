TwitterFeed
==============
A simple cake 2.x plugin to retrieve your tweets from any controller
This plugin provides a TwitterFeedModel
Uses OAuth and API v 1.1 of Twitter

#register
first sign in and register an app here: [https://dev.twitter.com/]https://dev.twitter.com/

#install
add following to your app/Config/boostrap.php file

    CakePlugin::load('TwitterFeed');

add following to your app/Config/database.php file

    public $twitter = array(
        'datasource' => 'TwitterSource',

        'screen_name' => 'YOUR_SCREEN_NAME',
        'public_key' => 'YOUR_CONSUMER_KEY',
        'private_key' => 'YOUR_CONSUMER_SECRET',
        'cacheDuration' => '+8 hours',

        'statusesAPI' => 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=%screen_name%&count=%count%',
        'oauthAPI' => 'https://api.twitter.com/oauth/request_token',
    );