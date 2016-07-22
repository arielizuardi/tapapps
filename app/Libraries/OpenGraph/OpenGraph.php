<?php
namespace App\Libraries\OpenGraph;

use Embed\Embed;

class OpenGraph
{
    public static function getImage($url)
    {
        $image_url = '';

        try {
            $embed = Embed::create($url);
            $image_url = $embed->getImage();
        } catch (\Exception $ex) {
            \Log::error($ex);
        }

        return $image_url;
    }
}