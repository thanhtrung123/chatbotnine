<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Admin\Sheets\UsageSituationSheet;
use App\Exports\Admin\Sheets\FiltersSheet;
use App\Exports\Admin\Sheets\StatusAnswerSheet;
use App\Exports\Admin\Sheets\EnqueteSheet;

/**
 * Class DashboardExport
 * @package App\Exports\Admin
 */
class DashboardExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $filter;
    protected $state_uses_data;
    protected $answer_state_data;
    protected $enquete_answer_data;
    public function __construct(array $filter, 
        array $state_uses_data, 
        array $answer_state_data, 
        array $enquete_answer_data
    ) {
        $this->filter = $filter;
        $this->state_uses_data = $state_uses_data;
        $this->answer_state_data = $answer_state_data;
        $this->enquete_answer_data = $enquete_answer_data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        // Get data date
        $data_date = array(
            'date_list' => ($this->state_uses_data['date_export'] ?? NULL),
            'date_data_users' => ($this->state_uses_data['data_result'] ?? NULL),
            'date_data_talk' => ($this->state_uses_data['data_talk'] ?? NULL)
        );
        // Get data hour
        $data_hour = array(
            'hour_list' => ($this->state_uses_data['hour'] ?? NULL),
            'hour_data_users' => ($this->state_uses_data['data_hour'] ?? NULL),
            'hour_data_talk' => ($this->state_uses_data['data_talk_hour'] ?? NULL)
        );
        // Get data date of week
        $data_day = array(
            'day_of_week' => ($this->state_uses_data['day_of_week'] ?? NULL),
            'data_day_of_week' => ($this->state_uses_data['data_day_of_week'] ?? NULL),
            'data_talk_day_of_week' => ($this->state_uses_data['data_talk_day_of_week'] ?? NULL)
        );
        $data_total_user = ($this->state_uses_data['total_user'] ?? NULL);
        // Get data answer
        $answer_data = array(
            'count_answer' => ($this->answer_state_data['count_answer'] ?? NULL),
            'count_no_answer' => ($this->answer_state_data['count_no_answer'] ?? NULL),
            'count_answer_handle' => ($this->answer_state_data['count_answer_handle'] ?? NULL),
            'count_answer_no_handle' => ($this->answer_state_data['count_answer_no_handle'] ?? NULL),
            'count_answer_yet_handle' => ($this->answer_state_data['count_answer_yet_handle'] ?? NULL)
        );
        if ($this->enquete_answer_data) {
            return [
                new FiltersSheet($this->filter),
                new UsageSituationSheet($data_date, $data_hour, $data_day, $data_total_user),
                new StatusAnswerSheet($answer_data),
                new EnqueteSheet($this->enquete_answer_data)
            ];
        }
        return [
                new FiltersSheet($this->filter),
                new UsageSituationSheet($data_date, $data_hour, $data_day, $data_total_user),
                new StatusAnswerSheet($answer_data)
        ];
    }
}
