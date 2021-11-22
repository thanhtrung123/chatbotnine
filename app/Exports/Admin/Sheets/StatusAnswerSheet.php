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
 * Class StatusAnswerSheet
 * @package App\Exports\Admin\Sheet
 */
class StatusAnswerSheet implements WithTitle, FromView, WithDrawings, WithEvents
{
    protected $answer_data;

    public function __construct(array $answer_data) {
        $this->answer_data = $answer_data;
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return '回答状況';
    }
    

    public function drawings()
    {
        $drawing_ary = array();
        if (file_exists(storage_path('app/uploads/chart_answer.png'))) {
            $drawing = new Drawing();
            $drawing->setName('回答率');
            $drawing->setDescription('回答率');
            $drawing->setPath(storage_path('app/uploads/chart_answer.png'));
            $drawing->setHeight(200);
            $drawing->setCoordinates('A8');
            $drawing_ary[] = $drawing;
        }
        if (file_exists(storage_path('app/uploads/chart_resolve.png'))) {
            $drawing2 = new Drawing();
            $drawing2->setName('解決率');
            $drawing2->setDescription('解決率');
            $drawing2->setPath(storage_path('app/uploads/chart_resolve.png'));
            $drawing2->setHeight(200);
            $drawing2->setCoordinates('E8');
            $drawing_ary[] = $drawing2;
        }
        return $drawing_ary;
    }

    public function view(): View
    {
        return view('admin.export.export_answer', [
            'answer_data'=> $this->answer_data
        ]);
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
            ],
        ];
        return [
            AfterSheet::class    => function(AfterSheet $event) use ($style_ary)
            {
                $event->sheet->getDelegate()->getHighestColumn();
                $event->getSheet()->getDelegate()->getStyle('A1:D2')->applyFromArray($style_ary);
                $event->getSheet()->getDelegate()->getStyle('A5:E6')->applyFromArray($style_ary);
            },
        ];
   }
}
