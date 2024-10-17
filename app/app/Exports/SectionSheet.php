<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;

class SectionSheet implements WithMapping, withHeadings, WithTitle, WithStyles, FromArray, WithEvents
{
    public $data;

    public function __construct($value)
    {
        $this->data = $value;
    }

    public function array(): array
    {
        return $this->data['users'];
    }

    public function map($row): array
    {
        $map = [
            [$row['first_name'], $row['last_name'], ...$row['summary'], ...$this->formatCellAttendance($row['overview'])]
        ];

        return $map;
    }

    private function formatCellAttendance($overview)
    {
        return collect($overview)->map(function ($i) {
            return ($i['time_in']['time'] ?? '') . " - " . ($i['time_out']['time'] ?? '');
        })->toArray();
    }

    public function headings(): array
    {
        $headings = [
            ['First name', 'Last Name', 'Summary', '', ''],
            ['', '', 'Present', 'Risk', 'Absent']
        ];
        $this->addDateHeadings($headings);

        return $headings;
    }

    public function addDateHeadings(&$headings)
    {

        collect($this->data['overview'])->map(function ($i) use (&$headings) {
            $headings[0][] = $i['date'];
        });
    }

    public function styles(Worksheet $sheet)
    {
        // dd($this->data);
        $columns = 2;

        $sheet->mergeCells("C1:E1");
        $columns += 3;

        $this->setHeaderDateStyle($columns, $sheet);
        $this->setCellStyle($columns, $sheet);

        $lastColumn = Coordinate::stringFromColumnIndex($columns);
    }

    // Auto-resize columns dynamically
    public static function beforeSheet(\Maatwebsite\Excel\Events\BeforeSheet $event)
    {
        $sheet = $event->sheet->getDelegate();

        // Auto-resize all columns with data
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    // Register event to handle column resizing
    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\BeforeSheet::class => [self::class, 'beforeSheet'],
        ];
    }

    public function title(): string
    {
        return $this->data['section'];
    }

    private function setCellStyle($columns, &$sheet) {
        $rowToStart = 3;
        $dateColumStart =& $columns;

        collect($this->data['users'])->map(function ($i) use (&$rowToStart, &$dateColumStart, &$sheet){

            $this->setDateStyle($dateColumStart, $rowToStart, $sheet, $i['overview']);
            $rowToStart++;
        });
    }

    // Tiwasa mag design sa cell
    private function setHeaderDateStyle($columns, &$sheet)
    {
        $columns = &$columns;

        collect($this->data['overview'])->map(function ($i) use (&$sheet, &$columns) {
            $lastColumn = Coordinate::stringFromColumnIndex($columns + 1);
            $sheet->getStyle("{$lastColumn}1")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => $i['isDisabled'] ? '757575' : 'ffffff', // Red background color
                    ],
                ],
            ]);

            $columns += 1;
        });
    }

    private function setDateStyle($columns, $row, &$sheet, $overview) {
        $columns = &$columns;
        $remarksColor = [
            'Present' => '12d100',
            'Risk' => 'ffd417',
            'Absent' => 'ff1717'
        ];

        collect($overview)->map(function ($i) use (&$sheet, &$columns, $row, $remarksColor) {
            $lastColumn = Coordinate::stringFromColumnIndex($columns + 1);
            $sheet->getStyle("{$lastColumn}{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => $i['isDisabled'] ? '757575' : $remarksColor[$i['remarks']], // Red background color
                    ],
                ],
            ]);

            $columns += 1;
        });
    }
}
