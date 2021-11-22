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
 * Class EnqueteSheet
 * @package App\Exports\Admin\Sheet
 */
class EnqueteSheet implements WithTitle, FromView, WithDrawings, WithEvents
{
    protected $enquete_answer_data;

    protected $page = 44;

    public function __construct(array $enquete_answer_data) {
        $this->enquete_answer_data = $enquete_answer_data;
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'アンケート';
    }
    

    public function drawings()
    {
        $result_enquete_combine = $this->enquete_answer_data;
        $drawing_ary = array();
        $line_row = 5;
        if (!empty($result_enquete_combine)) {
            foreach ($result_enquete_combine as $key => $enquete_answer) {
                $key_img = $key + 1;
                if (file_exists(storage_path('app/uploads/chart_enquete' . $key_img . '.png'))) {
                    $drawing = new Drawing();
                    $drawing->setName($enquete_answer['question_name'] ?? NULL);
                    $drawing->setDescription($enquete_answer['question_name'] ?? NULL);
                    $drawing->setPath(storage_path('app/uploads/chart_enquete' . $key_img . '.png'));
                    $drawing->setHeight(200);
                    $drawing->setWidth(500);
                    $drawing->setCoordinates('A' . $line_row);
                    $drawing_ary[] = $drawing;
                }
                ($key_img % 2 == 0) ? $line_row = $line_row + 23 : $line_row = $line_row + 21;
            }
        }
        return $drawing_ary;
    }

    public function view(): View
    {
        $result_enquete_combine = $this->enquete_answer_data;
        return view('admin.export.export_enquete', [
            'result_enquete_combine' => $result_enquete_combine,
            'page' => $this->page
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
        // Define column
        $column_ary =  array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
        'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ');
        $enquete_data = $this->enquete_answer_data;
        return [
            AfterSheet::class    => function(AfterSheet $event) use ($style_ary, $enquete_data, $column_ary)
            {
                $event->getSheet()->getDelegate()->getPageSetup()->setScale(75);
                $event->getSheet()->getDelegate()->getPageSetup()->setOrientation('landscape');
                $event->getSheet()->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $start_column = 'A';
                $start_column_number = 2;
                foreach ($enquete_data as $key => $enquete_answer_data) {
                    $end_column = $column_ary[(count($enquete_answer_data['item_data'] ?? []))];
                    $end_column_number = $start_column_number + 1;
                    $event->getSheet()->getDelegate()->getStyle($start_column.$start_column_number . ':'. $end_column.$end_column_number)->applyFromArray($style_ary);
                    $number_column = $key + 1;
                    ($number_column % 2 == 0) ? $start_column_number = $start_column_number + 23 : $start_column_number = $start_column_number + 21;
                }
                $count_total = floor(($end_column_number + $this->page) / $this->page);
                for ($i = 1; $i <= $count_total; $i++) {
                    $page = $this->page * $i;
                    $event->getSheet()->getDelegate()->setBreak('A' . $page, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                }
            },];
   }
}
