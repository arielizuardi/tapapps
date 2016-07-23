<?php
namespace App\Libraries\Image;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class Drawer
{
    protected $imagine;
    protected $canvas_w;
    protected $canvas_h;
    protected $insta_w;
    protected $insta_h;
    protected $image_coord_x;
    protected $image_coord_y;

    public function __construct(Imagine $imagine)
    {
        $this->imagine = $imagine;
        $this->canvas_w = 1200;
        $this->canvas_h = 1800;
        $this->insta_w = 1050;
        $this->insta_h = 1050;
        $this->image_coord_x = 70;
        $this->image_coord_y = 271;
    }

    public function draw($image_url, $background_image_url, $saved_filename)
    {
        $canvas = $this->imagine->create(new Box($this->canvas_w,$this->canvas_h));
        $background_image = $this->imagine->open($background_image_url);
        $canvas->paste($background_image, new Point(0, 0));
        $image = $this->imagine->open($image_url);
        $image->resize(new Box($this->insta_w,$this->insta_h));
        $canvas->paste($image, new Point($this->image_coord_x,$this->image_coord_y));
        //$white_bg = $this->imagine->open(\Config::get('instacetak.images_dir').'white_1050_450.jpg');
        $canvas->save($saved_filename);
    }
}