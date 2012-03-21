<?php

class Social_Twitter {

	/**
	 * Returns the most recent tweets for a user in an array.
	 *
	 * @param $username
	 *		The twitter username to get tweets for.
	 *
	 * @param $limit
	 *		An optional parameter to specify how many tweets to return
	 *		This defaults to 5 if not set.
	 */
	public function getRecent($username, $limit = 5, $format = 'json')
	{
		try
		{
			$url = sprintf('http://api.twitter.com/1/statuses/user_timeline/%s.%s', $username, $format);
			$get = file_get_contents($url);
		} 
		catch (Exception $e) 
		{
			$get = false;
		}
		
		if($get)
		{
			$tweets = json_decode($get);
			
			if(count($tweets) > $limit)
				$tweets = array_slice($tweets, 0, $limit);

			return $tweets;			
		}
		else
			return false;
	}

    /**
     * TODO
     */
    public function oauth($oauth_token = null, $oauth_secret = null)
    {
        Load::asset('social', 'twitteroauth.php');
        return new TwitterOAuth(
            Config::get('social.twitter_consumer_key'), 
            Config::get('social.twitter_consumer_secret'),
            $oauth_token,
            $oauth_secret
        );
    }

    /**
     * TODO
     */
    public function sendToTwitterForAuth()
    {
        $conn = $this->oauth();
        $request = $conn->getRequestToken(Url::to('social/twitter/auth/callback', true));

        $_SESSION['oauth_token'] = $request['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request['oauth_token_secret'];
         
        switch($conn->http_code)
        {
            case 200:
                $url = $conn->getAuthorizeURL($request['oauth_token']);
                Url::redirect($url);
                break;
            default:
                die('Could not connect to Twitter!'); // TODO Actual error page or message
        }
    }

    public function saveCallbackFromTwitter()
    {
        if(!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret']))
        {
            // TwitterOAuth instance, with two new parameters we got in twitter_login.php
            Load::asset('social', 'twitteroauth.php');

            $twitteroauth = new TwitterOAuth(
                Config::get('social.twitter_consumer_key'), 
                Config::get('social.twitter_consumer_secret'),
                $_SESSION['oauth_token'], 
                $_SESSION['oauth_token_secret']
            );

            // Let's request the access token
            $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);

            // Save it in a session var
            $_SESSION['access_token'] = $access_token;

            // Let's get the user's info
            $user_info = $twitteroauth->get('account/verify_credentials');

            if(isset($user_info->error))
            {
                die('error with user.');
            }
            else
            {
                $record = Social::oauth()
                    ->where('oauth_provider', '=', 'twitter')
                    ->andWhere('oauth_uid', '=', $user_info->id)
                    ->limit(1)
                    ->first();

                // Account doesn't exist, create it
                if(!$record)
                {
                    $id = Social::oauth()->insert(array(
                        'oauth_provider' => 'twitter',
                        'oauth_uid' => $user_info->id,
                        'oauth_token' => $access_token['oauth_token'],
                        'oauth_secret' => $access_token['oauth_token_secret'],
                        'username' => $user_info->screen_name
                    ));

                    // TODO Test for insert failures
                }

                // Account already exists, update tokens
                else
                {
                    $status = Social::oauth()
                        ->where('oauth_provider', '=', 'twitter')
                        ->andWhere('oauth_uid', '=', $user_info->id)
                        ->update(array(
                            'oauth_token' => $access_token['oauth_token'],
                            'oauth_secret' => $access_token['oauth_token_secret']
                        ));

                    // TODO Test for insert failures
                }
            }
        } 
        else
            die('didnt get all the details i needed.'); // TODO Send this to an error page!!!!
    }

    /**
     * Returns an authorized TwitterOAuth instance based on the username given. The username must
     * have valid auth tokens in the database otherwise boolean false is returned.
     */
    public function getAuthInstance($username)
    {
        if($record = Social::oauth()->where('username', '=', $username)->first())
            return $this->oauth($record->oauth_token, $record->oauth_secret);

        return false;
    }

}
