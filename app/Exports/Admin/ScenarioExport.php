<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Admin\Sheets\ScenarioSheet;

/**
 * Class ScenarioExport
 * @package App\Exports\Admin
 */
class ScenarioExport implements WithMultipleSheets
{
    use Exportable;

    protected $data_scenario_group;
    protected $data_scenario;

    /**
     * ScenarioExport constructor.
     * @param array $data_scenario_group
     * @param array $data_scenario
     */
    public function __construct(array $data_scenario_group, array $data_scenario) {
        $this->data_scenario_group = $data_scenario_group;
        $this->data_scenario = $data_scenario;
    }

    /**
     * Get sheet
     * @return array
     */
    public function sheets(): array
    {
        $scenario_sheet = [];
       foreach ($this->data_scenario_group as $key => $data_group) {
        $scenario_sheet[] = new ScenarioSheet($key, $data_group, $this->data_scenario[$key]);
       }
       return $scenario_sheet;
    }
}
