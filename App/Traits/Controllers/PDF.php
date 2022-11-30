<?php

namespace App\Traits\Controllers;

use Dompdf\Dompdf;

trait PDF
{
    public function createPDF(array $table, string $view, string $filename, bool $attachment = false) :void
    {
        $dompdf = new Dompdf(['isRemoteEnabled' => true, 'letter']);
        ob_start();
        require_once view($view);
        $pdf = ob_get_clean();
        $dompdf -> loadHtml($pdf);
        $dompdf -> render();
        $dompdf -> stream($filename,['Attachment' => $attachment]);
    }
}