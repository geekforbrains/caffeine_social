<?php

class Social_TwitterController extends Controller {

    /**
     * Redirects the user to Twitter to authenticate their account and allow
     * the application.
     */
    public static function auth() {
        Social::twitter()->sendToTwitterForAuth();
    }

    /**
     * Stores the details returned from Twitter after authing.
     */
    public static function callback() {
        Social::twitter()->saveCallbackFromTwitter();
    }

    // Testing
    public static function hello()
    {
        if($instance = Social::twitter()->getAuthInstance('geekforbrains'))
        {
            echo "<pre>";

            /*
            Check if following
            */
            $status = $instance->get('friendships/exists', array(
                'user_a' => 'geekforbrains',
                'user_b' => 'nerdburn'
            ));
            die(($status) ? 'YES' : 'NO');

            /*
            Stop following
            $status = $instance->post('friendships/destroy', array('screen_name' => 'nerdburn'));
            die(var_dump($status));
            */

            /*
            Start following
            $status = $instance->post('friendships/create', array('screen_name' => 'nerdburn'));
            die(var_dump($status));
            */

            /*
            Get following
            $response = $instance->get('friends/ids', array('screen_name' => 'geekforbrains'));
            if($response)
            {
                foreach($response->ids as $id)
                {
                    $record = $instance->get('users/lookup', array('user_id' => $id));
                    echo $record[0]->screen_name . '<br />';
                }
            }
            die();
            */

            /*
            Get followers
            $response = $instance->get('followers/ids', array('screen_name' => 'geekforbrains'));
            if($response)
            {
                $record = $instance->get('users/lookup', array('user_id' => implode(',', $response->ids)));
                foreach($record as $r)
                    echo $r->screen_name . '<br />';
            }
            die();
            */
        }
        else
            die('error getting instance');
    }

}
