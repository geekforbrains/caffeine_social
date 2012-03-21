<?php

class Social_OauthModel extends Model {

    public $_fields = array(
        'oauth_provider' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => true
        ),
        'oauth_uid' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => true
        ),
        'oauth_token' => array(
            'type' => 'text',
            'not null' => true
        ),
        'oauth_secret' => array(
            'type' => 'text',
            'not null' => true
        ),
        'username' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => true
        ),
    );

    public $_indexes = array(
        'oauth_provider', 
        'oauth_uid',
        'username'
    );

}
