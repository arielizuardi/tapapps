<?php
namespace App\Http\Controllers;

use App\Libraries\Collection;
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

    public function fetchImages(Request $request) {

        $q = $request->get('q');

        if (substr($q, 0, 1) == '#') {
            $q = substr($q, 1);
        }

        $ig_next_cursor = null;
        if (!empty($request->get('cursor'))) {
            $ig_next_cursor = $request->get('cursor');
        }

        $ig_coll = $this->instagram->search($q, 20, $ig_next_cursor);

        $image_urls = [];
        foreach ($ig_coll->items as $item) {
            $image_urls[] = $item->image_url;
        }

        $image_urls = array_unique($image_urls);

        $result = [];
        foreach ($image_urls as $url) {
            $result[] = [
                'url' => $url,
                'printed' => false,
                'clicked' => false
            ];

        }

        return response()->json(['data' => $result, 'cursor' => $ig_coll->next_url['instagram']['next_cursor'] ], 200);
    }

    public function toBeFetchImages(Request $request)
    {
        $q = $request->get('q');

        if ($request->has('cursor') and !empty($request->get('cursor'))) {
            $cursor = $request->get('cursor');
            $decode_cursor = $this->decodeCursor($cursor);

            $ig_q = $decode_cursor['instagram']['q'];
            $ig_num = $decode_cursor['instagram']['num'];
            $ig_next_cursor = $decode_cursor['instagram']['next_cursor'];
            $tw_max_id = $decode_cursor['twitter']['max_id_str'];
            $tw_count =  $decode_cursor['twitter']['count'];
            $tw_q = $decode_cursor['twitter']['q'];

            if (!empty($ig_next_cursor)) {
                $ig_coll = $this->instagram->search('rasuna1', $ig_num, $ig_next_cursor);
            } else {
                $ig_coll = new Collection([]);
            }

            $twitter_coll = $this->twitter->search($tw_q, $tw_count, $tw_max_id);
        } else {
            $ig_coll = $this->instagram->search('rasuna1');
            $twitter_coll = $this->twitter->search($q);
        }

        $merge_coll = array_merge($ig_coll->toArray()['data'], $twitter_coll->toArray()['data']);

        $next_cursor = null;
        if (!empty($merge_coll)) {
            $next_cursor = $this->encodeCursor($ig_coll, $twitter_coll);
        }

        $image_urls = [];
        foreach ($merge_coll as $item) {
            $image_urls[] = $item->image_url;
        }

        $result = [];
        $image_urls = array_unique($image_urls);

        foreach ($image_urls as $url) {
            $result[] = [
                'url' => $url,
                'printed' => false,
                'clicked' => false
            ];

        }

        return response()->json(['data' => $result, 'cursor' => $next_cursor], 200);
    }

    protected function encodeCursor($ig_coll, $twitter_coll) {
        $ig_next_url = $ig_coll->toArray()['next_url'];
        $twitter_next_url = $twitter_coll->toArray()['next_url'];
        $combine = array_merge($ig_next_url, $twitter_next_url);
        return base64_encode(serialize($combine));
    }

    protected function decodeCursor($cursor)
    {
        $decode_cursor = base64_decode($cursor);
        return unserialize($decode_cursor);
    }
}