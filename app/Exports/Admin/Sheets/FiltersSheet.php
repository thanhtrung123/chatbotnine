<?php

namespace App\Exports\Admin\Sheets;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDrawings;

/**
 * Class FiltersSheet
 * @package App\Exports\Admin\Sheet
 */
class FiltersSheet implements WithTitle, FromView, WithEvents
{
    protected $filter;
    
    public function __construct(array $filter)
    {
        $this->filter = $filter;
    }
    
    
    /**
     * @return string
     */
    public function title(): string
    {
        return '条件';
    }

    public function view(): View
    {
        $params = $this->filter;
        return view('admin.export.export_filter', ['params'=> $params]);
    }
    
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $style_ary = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ]
        ];
        $columns = ['A', 'B', 'C', 'D', 'E'];
        return [
            AfterSheet::class    => function(AfterSheet $event) use ($style_ary, $columns)
            {
                $event->getSheet()->getDelegate()->getStyle('A2:C2')->applyFromArray($style_ary);
                $event->getSheet()->getDelegate()->getStyle('A5:B6')->applyFromArray($style_ary);
                foreach ($columns as $column) {
                    $event->getSheet()->getDelegate()->getColumnDimension($column)->setWidth(15);
                }
            },
        ];
   }
}
