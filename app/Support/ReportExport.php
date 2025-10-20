<?php

namespace App\Support;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\StreamedResponse;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;

class ReportExport
{
    public static function download(string $format, string $filename, array $columns, iterable $rows, array $meta = [])
    {
        $format = strtolower($format);
        return match ($format) {
            'csv'  => self::csv($filename, $columns, $rows),
            'xlsx' => self::xlsx($filename, $columns, $rows),
            'pdf'  => self::pdf($filename, $columns, $rows, $meta),
            default => abort(400, 'Định dạng không hỗ trợ. Dùng csv|xlsx|pdf'),
        };
    }

    protected static function csv(string $filename, array $columns, iterable $rows)
    {
        $resp = new StreamedResponse(function () use ($columns, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM: Excel nhận UTF-8
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                fputcsv($out, array_map(fn($v) => is_bool($v) ? (int)$v : (string)$v, is_array($r) ? $r : (array)$r));
            }
            fclose($out);
        });
        $resp->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $resp->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'.csv"');
        return $resp;
    }

    protected static function xlsx(string $filename, array $columns, iterable $rows)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $resp = new StreamedResponse(function () use ($writer, $columns, $rows) {
            $writer->openToFile('php://output');
            $writer->addRow(WriterEntityFactory::createRowFromArray($columns));
            foreach ($rows as $r) {
                $writer->addRow(WriterEntityFactory::createRowFromArray(array_values(is_array($r) ? $r : (array)$r)));
            }
            $writer->close();
        });
        $resp->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $resp->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'.xlsx"');
        return $resp;
    }

    protected static function pdf(string $filename, array $columns, iterable $rows, array $meta = [])
    {
        $mpdf = new Mpdf([
            'tempDir'      => storage_path('app/tmp'),
            'format'       => 'A4-L',
            'default_font' => 'dejavusans',
        ]);

        $title   = $meta['title']  ?? 'Báo cáo';
        $period  = $meta['period'] ?? '';
        $caption = $period ? "<div style='color:#666;font-size:12px'>{$period}</div>" : '';

        $html = "<h2 style='margin:0 0 6px'>{$title}</h2>{$caption}
        <table width='100%' border='1' cellspacing='0' cellpadding='6' style='border-collapse:collapse;font-size:12px'>
            <thead><tr>";
        foreach ($columns as $c) $html .= "<th style='background:#f2f2f2;text-align:left'>{$c}</th>";
        $html .= "</tr></thead><tbody>";

        foreach ($rows as $r) {
            $r = is_array($r) ? $r : (array)$r;
            $html .= "<tr>";
            foreach ($r as $cell) $html .= "<td>".htmlspecialchars((string)$cell)."</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";

        $mpdf->WriteHTML($html);
        $content = $mpdf->Output($filename.'.pdf', Destination::STRING_RETURN);

        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.pdf"',
        ]);
    }
}
