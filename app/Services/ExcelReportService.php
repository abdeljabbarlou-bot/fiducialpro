<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Génère des classeurs Excel (.xlsx) au format professionnel pour les rapports
 * du cabinet : en-tête aux couleurs de la marque, colonnes formatées (dates,
 * montants DH), bandes zébrées et ligne de totaux.
 */
class ExcelReportService
{
    protected const BRAND_DARK = '0F172A';
    protected const BRAND_ACCENT = '0284C7';
    protected const ROW_ALT = 'F8FAFC';
    protected const BORDER_COLOR = 'E2E8F0';

    /**
     * @param  array<int, array{label: string, width?: int, format?: string}>  $columns
     * @param  iterable<array<int, mixed>>  $rows
     * @param  array<int, mixed>|null  $totalsRow
     */
    public function download(
        string $title,
        string $subtitle,
        array $columns,
        iterable $rows,
        string $filename,
        ?array $totalsRow = null,
    ): StreamedResponse {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rapport');

        $columnCount = count($columns);
        $lastColumnLetter = Coordinate::stringFromColumnIndex($columnCount);

        // --- Bandeau d'en-tête (identité du cabinet) ---
        $sheet->mergeCells("A1:{$lastColumnLetter}1");
        $sheet->setCellValue('A1', mb_strtoupper(config('cabinet.name')));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::BRAND_DARK);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells("A2:{$lastColumnLetter}2");
        $sheet->setCellValue('A2', sprintf(
            '%s — %s, %s — ICE: %s',
            config('cabinet.tagline'),
            config('cabinet.city'),
            config('cabinet.country'),
            config('cabinet.ice'),
        ));
        $sheet->getStyle('A2')->getFont()->setSize(9)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::BRAND_DARK);

        $sheet->mergeCells("A3:{$lastColumnLetter}3");
        $sheet->setCellValue('A3', $title);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB(self::BRAND_ACCENT);
        $sheet->getRowDimension(3)->setRowHeight(22);

        $sheet->mergeCells("A4:{$lastColumnLetter}4");
        $sheet->setCellValue('A4', $subtitle);
        $sheet->getStyle('A4')->getFont()->setSize(10)->setItalic(true)->getColor()->setRGB('64748B');

        $sheet->mergeCells("A5:{$lastColumnLetter}5");
        $sheet->setCellValue('A5', 'Généré le ' . now()->format('d/m/Y à H:i') . ' par ' . (auth()->user()->name ?? 'Système'));
        $sheet->getStyle('A5')->getFont()->setSize(8)->getColor()->setRGB('94A3B8');

        // --- Ligne d'en-têtes de colonnes ---
        $headerRow = 7;
        foreach (array_values($columns) as $i => $column) {
            $colLetter = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $column['label']);
            if (isset($column['width'])) {
                $sheet->getColumnDimension($colLetter)->setWidth($column['width']);
            }
        }
        $headerRange = "A{$headerRow}:{$lastColumnLetter}{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::BRAND_DARK);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($headerRow)->setRowHeight(20);

        // --- Lignes de données ---
        $rowIndex = $headerRow + 1;
        foreach ($rows as $row) {
            foreach (array_values($row) as $i => $value) {
                $colLetter = Coordinate::stringFromColumnIndex($i + 1);
                $format = $columns[$i]['format'] ?? 'text';
                $this->setCellValue($sheet, "{$colLetter}{$rowIndex}", $value, $format);
            }

            $dataRange = "A{$rowIndex}:{$lastColumnLetter}{$rowIndex}";
            if ($rowIndex % 2 === 0) {
                $sheet->getStyle($dataRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::ROW_ALT);
            }
            $sheet->getStyle($dataRange)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::BORDER_COLOR);
            $sheet->getStyle($dataRange)->getFont()->setSize(9.5);

            $rowIndex++;
        }

        // --- Ligne de totaux ---
        if ($totalsRow !== null) {
            foreach (array_values($totalsRow) as $i => $value) {
                $colLetter = Coordinate::stringFromColumnIndex($i + 1);
                $format = $columns[$i]['format'] ?? 'text';
                // Une étiquette texte (ex: "TOTAUX") posée dans une colonne date/devise reste du texte brut
                if ($value === '' || (is_string($value) && !is_numeric($value))) {
                    $format = 'text';
                }
                $this->setCellValue($sheet, "{$colLetter}{$rowIndex}", $value, $format);
            }

            $totalsRange = "A{$rowIndex}:{$lastColumnLetter}{$rowIndex}";
            $sheet->getStyle($totalsRange)->getFont()->setBold(true)->setSize(10)->getColor()->setRGB(self::BRAND_DARK);
            $sheet->getStyle($totalsRange)->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::BRAND_DARK);
            $sheet->getStyle($totalsRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EFF6FF');
        }

        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    protected function setCellValue($sheet, string $coordinate, mixed $value, string $format): void
    {
        if ($value === null || $value === '') {
            $sheet->setCellValue($coordinate, $value ?? '');
            return;
        }

        switch ($format) {
            case 'date':
                $date = $value instanceof \DateTimeInterface ? $value : new \DateTime((string) $value);
                $sheet->setCellValue($coordinate, ExcelDate::PHPToExcel($date));
                $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle($coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;

            case 'currency':
                $sheet->setCellValue($coordinate, (float) $value);
                $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode('#,##0.00 "DH"');
                $sheet->getStyle($coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;

            case 'number':
                $sheet->setCellValue($coordinate, (float) $value);
                $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle($coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;

            case 'percent':
                $sheet->setCellValue($coordinate, ((float) $value) / 100);
                $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode('0%');
                $sheet->getStyle($coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;

            default:
                $sheet->setCellValue($coordinate, (string) $value);
        }
    }
}
