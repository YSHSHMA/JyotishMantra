<?php

namespace App\Traits;



trait  PdfGenerator
{
    public static function generatePdf($view, $filePrefix, $filePostfix): string
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($filePrefix . $filePostfix . '.pdf', 'D');
    }
}
