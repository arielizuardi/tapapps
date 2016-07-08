<?php
namespace App\Libraries\Path;

use Embed\Embed;

class Path
{
    public static function getImageFromMoment($moment_url)
    {
        $image_url = '';

        try {
            $embed = Embed::create($moment_url);
            $image_url = $embed->getImage();
        } catch (\Exception $ex) {
            \Log::error($ex);
        }

        return $image_url;
    }
}