<?php
namespace App\Http\Controllers;

use App\Libraries\Twitter\Service as TwitterService;
use App\Libraries\Instagram\Service as InstagramService;
use Illuminate\Http\Request;

class ImageResourceController extends Controller
{
    protected $twitter;
    protected $instagram;

    public function __construct(TwitterService $twitter, InstagramService $instagram)
    {
        $this->twitter = $twitter;
        $this->instagram = $instagram;
    }

    public function fetchImages(Request $request)
    {
        $q = $request->get('q');

        /**
        $c1 = [
            'num' => 20,
            'max_id_str' => 'someRandomText'
        ];
        $c2 = [
            'num' => 20,
            'cursor' => 30
        ];
        $combine = [
            'twitter' => $c1,
            'instagram' => $c2
        ];
        $cursor= base64_encode(serialize($combine));
        $decode_cursor = base64_decode($cursor);
        $array = unserialize($decode_cursor);
        **/

        $ig_coll = $this->instagram->search('rasuna1');
        $twitter_coll = $this->twitter->search($q);
        $merge_coll = array_merge($ig_coll->toArray()['data'], $twitter_coll->toArray()['data']);

        $image_urls = [];
        foreach ($merge_coll as $item) {
            $image_urls[] = $item->image_url;
        }

        $result = [];
        /*
        $image_urls = [
            'http://media.pathcdn.net/dn/path_classic/photos2/68a2ca04-974a-44bb-a936-d80f6ad23afc/original.jpg',
            'http://media.pathcdn.net/dn/path_classic/photos2/065118db-2a54-4470-97ea-3d82a86b7592/original.jpg',
            'http://media.pathcdn.net/dn/path_classic/photos2/f9faf8fa-1024-47df-ba05-00eb75c4ec69/original.jpg',
            'http://media.pathcdn.net/dn/path_classic/photos2/a7a13d85-b304-4e28-b19a-585ff8112641/original.jpg'
        ];*/

       $image_urls = array_unique($image_urls);

        foreach ($image_urls as $url) {
            $result[] = [
                'url' => $url,
                'printed' => false,
                'clicked' => false
            ];

        }

        return response()->json($result, 200);
    }
}