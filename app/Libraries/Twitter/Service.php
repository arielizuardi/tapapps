<?php
namespace App\Libraries\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Libraries\Path\Path;
use App\Libraries\SearchContract;

class Service implements SearchContract
{
    protected $connection;
    protected $consumer_key;
    protected $consumer_secret;
    protected $access_token;
    protected $access_token_secret ;

    public function __construct()
    {
        $this->consumer_key = env('TWITTER_CONSUMER_KEY');
        $this->consumer_secret = env('TWITTER_CONSUMER_SECRET');
        $this->access_token = env('TWITTER_ACCESS_TOKEN');
        $this->access_token_secret = env('TWITTER_ACCESS_TOKEN_SECRET');

        $this->connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_token_secret);
    }

    /**
     * @param $q string Query (could be hashtag or username)
     *
     * @return array of Item
     */
    public function search($q)
    {
        $result = [];
        $response = $this->connection->get('search/tweets', ['q' => $q]);

        if (empty($response->statuses)) {
            return [];
        }

        foreach ($response->statuses as $status) {
            $entities = $status->entities;
            if (!empty($entities)) {
                $result = array_merge($result, $this->processEntities($entities));
            }
        }
        //todo convert to Item object
        return $result;
    }

    protected function processEntities($twitter_entities)
    {
        $image_urls = [];

        if (!empty($twitter_entities->media)) {
            foreach ($twitter_entities->media as $media) {
                if ($media->type == 'photo') {
                    $image_urls[] = $media->media_url;
                }
            }
        }

        if (!empty($twitter_entities->urls)) {
            foreach ($twitter_entities->urls as $url) {
                $host = parse_url($url->expanded_url, PHP_URL_HOST);
                if ($host == 'path.com') {
                    $image_from_path = Path::getImageFromMoment($url->expanded_url);
                    if (!empty($image_from_path)) {
                        $image_urls[] = $image_from_path;
                    }
                }
            }
        }

        return $image_urls;
    }

}