<?php
namespace App\Http\Controllers;

use App\Libraries\Image\Drawer;
use App\Libraries\PDF\Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrinterController extends Controller
{
    protected $drawer;
    protected $pdf_generator;

    public function __construct(Drawer $drawer, Generator $pdf_generator)
    {
        $this->drawer = $drawer;
        $this->pdf_generator = $pdf_generator;
    }

    public function printImages(Request $request)
    {
        $images = $request->json('images');
        $bg_image = $request->json('bg_image');

        foreach ($images as $key => $image) {
            $img = explode('?', $image['url']);
            $this->drawer->draw($img[0], $bg_image, storage_path('app/images/polaroid/'.$key.'.jpeg'));
        }

        //$this->pdf_generator->toDOMPDF();

        $image_url = '';
        $background_url = '';
        $saved_filename = '';
    }

    public function printget(Request $request)
    {
        $urls_base64 = base64_decode($request->get('urls'));
        $bg_image = base64_decode($request->get('bg_url'));
        $urls = explode(' ', $urls_base64);

        $date = new \DateTime();
        $dirname = strval($date->getTimestamp());
        $source_dir = 'images/polaroid/'.$dirname;
        Storage::makeDirectory($source_dir, 0755, true);

        foreach ($urls as $key => $url){
            $image_urls = explode('?', $url);
            $img_url = $image_urls[0];
            if (!empty($img_url)) {
                $this->drawer->draw($img_url, $bg_image, storage_path('app/images/polaroid/'.$dirname.'/'. $key . '.jpeg'));
            }
        }

        $this->pdf_generator->toDOMPDF($source_dir);
    }
}