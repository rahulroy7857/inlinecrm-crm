<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\View;

class StudentDocumentService
{
    public function applicationPdf(Student $student): string
    {
        $html = View::make('student.pdf.application', compact('student'))->render();

        return $this->htmlToPdf($html);
    }

    public function receiptPdf(Student $student): string
    {
        $html = View::make('student.pdf.receipt', compact('student'))->render();

        return $this->htmlToPdf($html);
    }

    private function htmlToPdf(string $html): string
    {
        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();

            return $dompdf->output();
        }

        return $html;
    }
}
