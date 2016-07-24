<?php
namespace App\Libraries\Instagram;

use App\Libraries\Collection;
use App\Libraries\Item;
use App\Libraries\SearchContract;
use Jenssegers\Mongodb\Connection;

class Service implements SearchContract
{
    /** @var Connection */
    protected $db;

    public function __construct()
    {
        $this->db = \DB::connection('mongodb');
    }

    public function search($q, $num = 20, $next_cursor = null, $first_cursor = null)
    {
        $query = $this->db->collection('instagram')
            ->where('tag', '=', $q)
            ->orderBy('_id', 'desc');

        if (!empty($next_cursor)) {
            $next_cursor = intval($next_cursor);
            $query->skip($next_cursor)->take($num);
        } else {
            $next_cursor = 0;
            $query->skip(0)->take($num);
        }

        $data = [];
        $ig_objects = $query->get();

        foreach ($ig_objects as $ig_object) {
            $data[] = new Item($ig_object['url']);
        }

        $next_cursor = $this->encodeNextUrl($q, $num, (count($ig_objects) + $next_cursor));
        return new Collection($data, $next_cursor);
    }

    protected function encodeNextUrl($q, $num, $next_cursor = null, $first_cursor = null)
    {
        return [
            'instagram' => [
                'q' => $q,
                'num' => $num,
                'next_cursor' =>  $next_cursor,
                'first_cursor' => $first_cursor
            ]
        ];
    }

}