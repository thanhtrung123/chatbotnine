<?php

namespace App\Exports\Admin\Sheets;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

/**
 * Class ScenarioSheet
 * @package App\Exports\Admin\Sheet
 */
class ScenarioSheet implements WithTitle, WithHeadings, FromArray
{
    protected $title;
    protected $data_group;
    protected $data_node;
    protected $title_heading = array();

    /**
     * ScenarioSheet constructor.
     * @param string $title
     * @param array $data_group
     * @param array $data_node
     */
    public function __construct(string $title, array $data_group, array $data_node) {
        $this->title = $title;
        $this->data_group = $data_group;
        $this->data_node = $data_node;
    }
    
    /**
     * Get title
     * @return string
     */
    public function title(): string
    {
        if ($this->title != 'なし') {
            return $this->title;
        } else {
            return 'カテゴリ無し';
        }
    }
    
    /**
     * Get data sheet
     * @return array
     */
    public function array(): array
    {
        $rows = array();
        if (!$this->title_heading) {
            return $rows;
        }
        $num_heading = count($this->title_heading);
        foreach ($this->data_group as $key => $line_rows) {
            $key_heading = array_keys($this->title_heading);
            $rows[$key][] = $this->title;
            $num_rows = 1;
            foreach ($line_rows as $line) {
                if (strpos($line, 's') !== false) {
                    $id_s = str_replace('s', '', $line);
                    $rows[$key][] = $id_s;
                    $rows[$key][] = $this->data_node['scenario_data'][$id_s]['name'];
                    $num_rows = $num_rows + 2;
                } else {
                    $cal_row = $num_heading - $num_rows;
                    if ($cal_row > 3) {
                        while ($cal_row > 3) {
                            $rows[$key][] = '';
                            $rows[$key][] = '';
                            $cal_row = $cal_row - 2;
                        }
                    }
                    $id_note_str = str_replace(['qc', 'q'], ['', ''], $line);
                    $id_ary = explode('_', $id_note_str);
                    $id_qc = $id_ary[0] ?? NULL;
                    if ($this->data_node['qa_data'][$id_qc] ?? '') {
                        $rows[$key][] = $this->data_node['qa_data'][$id_qc]['app_id'];
                        $rows[$key][] = $this->data_node['qa_data'][$id_qc]['question'];
                        $rows[$key][] = $this->data_node['qa_data'][$id_qc]['answer'];
                    }
                }
            }
        }
        return $rows;
    }

    /**
     * Heading
     * @return array
     */
    public function headings(): array
    {
        $key_max = 1;
        $key_group = 0;
        $max = null;
        $position = null;
        if (!$this->data_group) {
            return $this->title_heading;
        }
        $this->title_heading[] = 'カテゴリー名';
        // count_group_ary
        foreach ($this->data_group as $key => $group) {
            if ($max == null) {
                $max = count($group);
                $position = $key;
            } else {
                if (count($group) == $max) {
                    $str_node = implode(",", $group);
                    if (strpos($str_node, 'q') !== false || strpos($str_node, 'qc') !== false) {
                        $max = count($group);
                        $position = $key;
                    }
                } else if (count($group) > $max) {
                    $max = count($group);
                    $position = $key;
                }
            }
        }
        $max_data_group = $this->data_group[$position];
        foreach ($max_data_group as $id_node) {
            if (strpos($id_node, 'q') !== false || strpos($id_node, 'qc') !== false) {
                $this->title_heading['q_id'] = 'QA_ID';
                $this->title_heading['q_question'] = 'QA_質問文章';
                $this->title_heading['q_answer'] = 'QA_回答文章';
                $key_group = 1;
            } else {
                $this->title_heading[] = 'シナリオ階層' . $key_max . '_ID';
                $this->title_heading[] = 'シナリオ階層' . $key_max . '_シナリオ名';
                $key_max++;
            }
        }
        if ($key_group < 1) {
            if (count($this->data_node['qa_data']) > 0) {
                $this->title_heading['q_id'] = 'QA_ID';
                $this->title_heading['q_question'] = 'QA_質問文章';
                $this->title_heading['q_answer'] = 'QA_回答文章';
            }
        }
        return  $this->title_heading;
    }
}
