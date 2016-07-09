<?php
namespace App\Http\Controllers;

use App\Libraries\Twitter\Service;

class ImageResourceController extends Controller
{
    protected $twitter;

    public function __construct(Service $twitter)
    {
        $this->twitter = $twitter;
    }

    public function fetchImages()
    {
        //$image_urls = $this->twitter->search('#pathdaily');

        $result = [];


        $image_urls = [
            'http://media.pathcdn.net/dn/path_classic/photos2/68a2ca04-974a-44bb-a936-d80f6ad23afc/original.jpg',
            'http://media.pathcdn.net/dn/path_classic/photos2/065118db-2a54-4470-97ea-3d82a86b7592/original.jpg',
            'http://media.pathcdn.net/dn/path_classic/photos2/f9faf8fa-1024-47df-ba05-00eb75c4ec69/original.jpg',
            'http://media.pathcdn.net/dn/path_classic/photos2/a7a13d85-b304-4e28-b19a-585ff8112641/original.jpg'
        ];

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