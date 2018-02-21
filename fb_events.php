<?php

class js_fb_events_fetch
{
    var $fbAccessToken;
    var $useCache;
    var $fbAppId = "163100071137046";
    var $fbAppSecret = "df65ee0adf9dcddea774bbc409e364b1";

    public function __construct($fbAccessToken, $useCache = 0)
    {
        if (isset($fbAccessToken)) {
            $this->token = $fbAccessToken;
        }
        else
            //False is Access token is not passed
            $this->token = false;
    }

    public function get_fb_error($fbResponse)
    {
        if (isset($fbResponse->error)) {
            return $fbResponse->error->message;
        } else
            return false;
    }

    /**
     * @return mixed
     */
    public function get_fb_events()
    {
        if ($this->token) {
        $items = '';
        $graph_url = "https://graph.facebook.com/v2.12/niskevesti.rs/events";
        $fb_post_data = "access_token=" . $this->token . "&method=get&fields=cover,description,name,place,start_time";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $graph_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fb_post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        $arrOutput = json_decode($output);

        $fbErr = $this->get_fb_error($arrOutput);
        if ($fbErr) {
            return $fbErr;
        }



        if (count($arrOutput->data)) {
        foreach ($arrOutput->data as $eventNode) {
            $items = [];
            $items[]["id"] = $eventNode->id;
            $items[]["event_name"] = $eventNode->name;
            $items[]["description"] = $eventNode->description;
            $items[]["place"] = $eventNode->place->name;
            $items[]["city"] = $eventNode->place->location->city;
            $items[]["country"] = $eventNode->place->location->country;
            $items[]["street"] = $eventNode->place->location->street;
            $items[]["start_time"] = $eventNode->start_time;
            $items[]["image_url"] = $eventNode->cover->source;
            }
            return $items;
        }
        else
            return false;

    }
    else
        return null; }

    static function fbGetAccessToken($fbAppId, $fbAppSecret)
        {
            $graph_url = "https://graph.facebook.com/oauth/access_token";
            $fb_post_data = "grant_type=client_credentials&client_id=" . $fbAppid . "&client_secret=" . $fbAppSecret;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $graph_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fb_post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $output = curl_exec($ch);
            curl_close($ch);
            $arrOutput = json_decode($output);
            return $arrOutput->access_token;
        }
}


$fbToken = js_fb_events_fetch::fbGetAccessToken("163100071137046","df65ee0adf9dcddea774bbc409e364b1");

if ($fbToken)
    $fbEvents = new js_fb_events_fetch($fbToken);
else
    echo 'Problem with getting Authentication Token';





