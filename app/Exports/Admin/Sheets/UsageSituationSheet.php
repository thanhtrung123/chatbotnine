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
 * Class UsageSituationSheet
 * @package App\Exports\Admin\Sheet
 */
class UsageSituationSheet implements WithTitle, FromView, WithDrawings, WithEvents
{
    protected $data_date;
    protected $data_hour;
    protected $data_day;
    protected $count_date_data;
    protected $data_total_user;
    protected $page = 37;

    public function __construct(array $data_date, 
        array $data_hour, 
        array $data_day,
        array $data_total_user
    ) {
        $this->data_date = $data_date;
        $this->data_hour = $data_hour;
        $this->data_day = $data_day;
        $this->data_total_user = $data_total_user;
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return '利用状況';
    }
    
    public function drawings()
    {
        $count_date_data = 6;
        $drawing_ary = array();
        if (file_exists(storage_path('app/uploads/chart_period.png'))) {
            $drawing = new Drawing();
            $drawing->setName('Date Chart');
            $drawing->setDescription('Date Chart data');
            $drawing->setPath(storage_path('app/uploads/chart_period.png'));
            $drawing->setHeight(250);
            $drawing->setCoordinates('D'. $count_date_data);
            $drawing_ary[] = $drawing;
        }
        // Caculate page
        $page_row = count(($this->data_date['date_list'] ?? [])) + 6;
        $page_new = (int) $this->page - 1;
        if ($page_row <= $page_new) {
            $page_last = $page_new - $page_row;
            $count_date_data = $page_row + $page_last + 2;
        } else if ($this->page < $page_row) {
            $page_last = $this->page - ($page_row - floor($page_row/$page_new)*$page_new);
            $count_date_data = count(($this->data_date['date_list'] ?? [])) + $count_date_data + 2 + $page_last;
        } else {
            $count_date_data = count(($this->data_date['date_list'] ?? [])) + $count_date_data + 2;
        }
        if (file_exists(storage_path('app/uploads/chart_hour.png'))) {
            $drawing2 = new Drawing();
            $drawing2->setName('Hour Chart');
            $drawing2->setDescription('Hour Chart Data');
            $drawing2->setPath(storage_path('app/uploads/chart_hour.png'));
            $drawing2->setHeight(250);
            $drawing2->setCoordinates('D'. $count_date_data);
            $drawing_ary[] = $drawing2;
        }
        $page_row = count(($this->data_hour['hour_list'] ?? [])) + 1;
        $page_new = (int) $this->page - 1;
        $page_last = $page_new - $page_row;
        $count_date_data = count(($this->data_hour['hour_list'] ?? [])) + $count_date_data + $page_last + 2;
        if (file_exists(storage_path('app/uploads/chart_week.png'))) {
            $drawing3 = new Drawing();
            $drawing3->setName('Day Chart');
            $drawing3->setDescription('Hour Chart Data');
            $drawing3->setPath(storage_path('app/uploads/chart_week.png'));
            $drawing3->setHeight(250);
            $drawing3->setCoordinates('D'. $count_date_data);
            $drawing_ary[] = $drawing3;
        }
        return $drawing_ary;
    }

    public function view(): View
    {
        return view('admin.export.export_user', [
            'data_date'=> $this->data_date,
            'data_hour'=> $this->data_hour,
            'data_day'=> $this->data_day,
            'data_user' => $this->data_total_user,
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
        $columns = [16 => 'A', 21 => 'B', 21 => 'C', 23 => 'D', 15 => 'E'];
        $count_date_data = count(($this->data_date['date_list'] ?? [])) + 6;

        $page_row = $count_date_data;
        $page_new = (int) $this->page - 1;
        if ($page_row <= $page_new) {
            $page_last = $page_new - $page_row;
            $count_hour = $page_row + $page_last + 2;
        } else if ($this->page < $page_row) {
            $page_last = $this->page - ($page_row - floor($page_row/$page_new)*$page_new);
            $count_hour = $count_date_data + 2 + $page_last;
        } else {
            $count_hour = $count_date_data + 2;
        }
        $count_hour_end = $count_hour + count(($this->data_hour['hour_list'] ?? []));
        $page_last = $this->page - count(($this->data_hour['hour_list'] ?? []));
        $count_day = $count_hour_end + $page_last;
        $count_day_end = $count_day + count(($this->data_day['day_of_week'] ?? []));
        return [
            AfterSheet::class    => function(AfterSheet $event) use ($style_ary, $count_date_data, $count_hour, $count_hour_end, $count_day, $count_day_end, $columns)
            {
                foreach ($columns as $key => $column) {
                    $event->getSheet()->getDelegate()->getColumnDimension($column)->setWidth($key);
                }
                $event->getSheet()->getDelegate()->getPageSetup()->setScale(75);
                $event->getSheet()->getDelegate()->getPageSetup()->setOrientation('landscape');
                $event->getSheet()->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $event->getSheet()->getDelegate()->getStyle('A1:E4')->applyFromArray($style_ary);
                $event->getSheet()->getDelegate()->getStyle('A6:C'.$count_date_data)->applyFromArray($style_ary);
                $event->getSheet()->getDelegate()->getStyle('A' . $count_hour . ':C'. $count_hour_end)->applyFromArray($style_ary);
                $event->getSheet()->getDelegate()->getStyle('A' . $count_day . ':C'. $count_day_end)->applyFromArray($style_ary);
                $count_total = floor(($count_day_end + $this->page) / $this->page);
                for ($i = 1; $i <= $count_total; $i++) {
                    $page = $this->page * $i;
                    $event->getSheet()->getDelegate()->setBreak('A' . $page, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                }
            },
        ];
   }
}
