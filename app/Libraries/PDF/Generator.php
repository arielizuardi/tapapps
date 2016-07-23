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
        $this->pdf->setCanvas(new TAPCPDF("4r", "portrait", $this->pdf));
    }

    public function toDOMPDF()
    {
        /**
         * In CPDF I add 4r size(custom)
         */

        // /app/images/polaroid
        $files = Storage::files('/images/polaroid');

        $i = 1; $html = '';
        foreach ($files as $file) {
            $html = $html.'<img style="width:4in;height:6in;" src="https://dl.dropboxusercontent.com/s/t6ltq8c3a0fgxjx/FRAME%20URBANGIGS-FINAL-SBY-BATCH2-min.png?dl=0"><br>';
            $i++;
        }

        $this->pdf->loadHtml($html);
        $this->pdf->render();
        $this->pdf->stream(time(),['Attachment'=> 0]);
    }
}