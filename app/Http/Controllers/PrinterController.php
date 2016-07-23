<?php
namespace App\Http\Controllers;

use App\Libraries\Image\Drawer;
use App\Libraries\PDF\Generator;
use Illuminate\Http\Request;

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

        $this->pdf_generator->toDOMPDF();

        $image_url = '';
        $background_url = '';
        $saved_filename = '';
    }

    public function test()
    {
        $this->pdf_generator->toDOMPDF();
    }
}