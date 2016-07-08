<?php
namespace App\Libraries;

class Item
{
    public $username;
    public $profile_picture_url;
    public $image_url;

    public function __construct($image_url, $username = null , $profile_picture_url = null)
    {
        $this->image_url = $image_url;
        $this->username = $username;
        $this->profile_picture_url;
    }

    public function toArray()
    {
        return [
            'username' => $this->username,
            'profile_picture_url' => $this->profile_picture_url,
            'image_url' => $this->image_url
        ];
    }
}