<?php return array(

    'configs' => array(
        'social.twitter_consumer_key' => '',
        'social.twitter_consumer_secret' => ''
    ),

    'routes' => array(
        'social/twitter/auth' => array(
            'callback' => array('twitter', 'auth')
        ),
        'social/twitter/auth/callback' => array(
            'callback' => array('twitter', 'callback')
        ),
        'social/twitter/hello' => array(
            'callback' => array('twitter', 'hello')
        )
    )

); 
