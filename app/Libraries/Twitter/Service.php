<?php
namespace App\Libraries\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Libraries\Collection;
use App\Libraries\Item;
use App\Libraries\OpenGraph\OpenGraph;
use App\Libraries\SearchContract;

class Service implements SearchContract
{
    protected $connection;
    protected $consumer_key;
    protected $consumer_secret;
    protected $access_token;
    protected $access_token_secret;

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
     * @param $num int num of returned result
     * @param $next_cursor string
     * @param $first_cursor string
     *
     * @return Collection
     */
    public function search($q, $num = 20, $next_cursor = null, $first_cursor = null)
    {
        $image_urls = [];
        $response = $this->connection->get('search/tweets', ['q' => $q, 'result_type' => 'recent', 'count' => $num]);

        if (empty($response->statuses)) {
            return [];
        }

        foreach ($response->statuses as $status) {
            $entities = $status->entities;
            if (!empty($entities)) {
                $image_urls = array_merge($image_urls, $this->processEntities($entities));
            }
        }

        $data = [];
        foreach($image_urls as $image_url) {
            $data[] = new Item($image_url);
        }

        $next_cursor = $this->encodeNextUrl($response);

        return new Collection($data, $next_cursor);
    }

    protected function encodeNextUrl($response)
    {
        $metadata = $response->search_metadata;
        $q = $metadata->query;

        if (substr($q,0,2) == '%25') {
            $q = substr($q, 2);
        }

        $next_cursor = [
            'type' => 'twitter',
            'max_id_str' => $metadata->max_id_str,
            'q' => $q,
            'count' => $metadata->count
        ];

        return $next_cursor;
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
                if ($host == 'path.com' or $host == 'instagram.com') {
                    $image_from_og = OpenGraph::getImage($url->expanded_url);
                    if (!empty($image_from_og)) {
                        $image_urls[] = $image_from_og;
                    }
                }
            }
        }

        return $image_urls;
    }

}