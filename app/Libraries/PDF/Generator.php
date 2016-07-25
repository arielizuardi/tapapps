<?php
namespace App\Libraries\PDF;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;

class Generator
{
    protected $pdf;

    public function __construct()
    {
        $this->pdf = new Dompdf();
        $this->pdf->setPaper('4r');
    }

    public function toDOMPDF($source_dir)
    {
        /**
         * In CPDF I add 4r size(custom)
         */
        $path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        // /app/images/polaroid
        $files = Storage::files($source_dir);
        $html = "<style>html { margin: 0px }</style>";
        foreach ($files as $file) {
            $html = $html.'<img style="width:4in;height=6in;" src="file://'.$path.$file.'"><br>';
        }

        $this->pdf->loadHtml($html);
        $this->pdf->render();
        $this->pdf->stream(time(),['Attachment'=> 0]);
    }
}