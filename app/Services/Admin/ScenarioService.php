<?php

namespace App\Services\Admin;

use App\Services\Admin\Traits\ScenarioTrait;
use App\Repositories\Scenario\ScenarioRepositoryInterface;
use App\Services\Bot\BotDbService;
use App\Services\RepositoryServiceInterface;
use App\Services\File\ZipService;
use DB;
use Storage;
use File;
use ZipArchive;

/**
 * シナリオサービス
 * Class ScenarioService
 * @package App\Services\Admin
 */
class ScenarioService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait, ScenarioTrait;
    /**
     * @var ScenarioRepositoryInterface
     */
    private $repository;
    /**
     * @var BotDbService
     */
    private $bot_db_service;

    // Save position of scenario and Qa
    protected $tmp_position = array();

    // array node child
    protected $child = array();

    // coordinates y of node
    protected $ys = 0;

    // id scenario insert
    protected $id_scenario = 1;
    
    // zip service
    private $zip_service;

    /**
     * ScenarioService constructor.
     * @param BotDbService $bot_db_service
     * @param ZipService $zip_service
     */
    public function __construct(BotDbService $bot_db_service, ZipService $zip_service)
    {
        $this->bot_db_service = $bot_db_service;
        $this->zip_service = $zip_service;
        $this->repository = $bot_db_service->getScenarioRepository();
    }

    /**
     * @param $id
     * @return array
     */
    public function getKeywords($id)
    {
        $keywords_gen = $this->bot_db_service->getScenarioKeywordRepository()->setParams(['scenario_id' => $id])->filterByParams()->getDbResult()->getGenerator();
        $keywords = [];
        foreach ($keywords_gen as $idx => $row) {
            $keywords[] = ['keyword_id' => $row['scenario_keyword_id'], 'keyword' => $row['keyword']];
        }
        return $keywords;
    }

    /**
     * 登録
     * @param $params
     * @return $id
     */
    public function createFromFormDataReturnId($params, $arr_id_scenario)
    {
        $this->id_scenario = $this->setIdInsert($this->id_scenario, $arr_id_scenario);
        //登録
        $data = $this->repository->create([
            'id' => $this->id_scenario,
            'name' => $params['name'],
            'category_id' => empty($params['category_id']) ? null : $params['category_id'],
            'order' => empty($params['order']) ? 0 : $params['order'],
        ]);
        $this->id_scenario++;
        //関連登録
        $id = $data->id;
        //$this->saveKeyword($id, $params)->saveRelation($id, $params)->saveLearningRelation($id, $params);
        return $id;
    }

    /**
     * set id scenario insert db
     * @param $id_scenario, $arr_id_scenario
     * @return $id_scenario
     */
    public function setIdInsert($id_scenario, $arr_id_scenario)
    {
        if (!in_array($id_scenario, $arr_id_scenario)) {
            return $this->id_scenario;
        }
        $this->id_scenario++;
        return $this->setIdInsert($this->id_scenario, $arr_id_scenario);
    }

    /**
     * Create connection
     * @param $id_node_input, id node (scenario id or qa copy id or qa id)
     * @param $id_node_output,  id node
     * @param $max_node_new, max node id
     * @param $qa_position, order qa
     * @param $arr_node_match
     * @param $arr_scenation_related
     * @return $this
     */
    public function createConnectionScenario($id_node_input, $id_node_output, $max_node_new, $qa_position, &$arr_node_match, &$arr_scenation_related)
    {
        if(strpos($id_node_input, 's') !== false) {
            if ($id_node_output != '') {
                $params['parent_ids'] = true;
            } else {
                $params['parent_ids'] = false;
            }
            //シナリオIDで削除
            if ($params === null) return $max_node_new;
            //親がない場合、親ID=nullで登録
            if ($params['parent_ids']) {
                $params['parent_ids_ary'][] = substr($id_node_output, 1);
            } else {
                $params['parent_ids_ary'] = [0 => null];
            }
            //delete data where scenario_id not null & parent_scenario_id is null
            $arr_scenation_related['delete_scenario_relate'][] = substr($id_node_input, 1);
            //指定された分追加
            foreach ($params['parent_ids_ary'] as $parent_id) {
                $arr_scenation_related['save_scenario_relate'][] = [
                    'scenario_id' => substr($id_node_input, 1),
                    'parent_scenario_id' => $parent_id,
                ];
            }
        }
        if (strpos($id_node_input, 'qc') !== false) {
            if (strpos($id_node_input, 'temp') !== false) {
                if ($arr_node_match[$id_node_input] ?? NULL) {
                    $arr_scenation_related['save_scenario_learing'][] = [
                        'scenario_id' => substr($id_node_output, 1),
                        'api_id' => $arr_node_match['api_id' . $id_node_input] ?? NULL,
                        'node_id' => $arr_node_match['node_' . $id_node_input] ?? 0,
                        'order' => $arr_node_match['order_' . $id_node_input] ?? NULL,
                    ];
                } else {
                    //cut node
                    $input_cut_string = trim(substr($id_node_input, 0, strrpos($id_node_input, "-t")));
                    // replace node
                    $input_result = str_replace('qc', '', $input_cut_string);
                    // covert node in - node out become array
                    $data_input = explode('_', $input_result);
                    // Set max node id
                    $max_node_new = $max_node_new + 1;
                    $params['api_ids_ary'][] = $data_input[0] ?? NULL;
                    foreach ($qa_position as $position) {
                        $idP = str_replace('node-', '', ($position['id'] ?? NULL));
                        if ($idP == $id_node_input) {
                            $order = $position['order'] ?? NULL;
                            break;
                        }
                    }
                    foreach ($params['api_ids_ary'] as $idx => $api_id) {
                        $arr_scenation_related['save_scenario_learing'][] = [
                            'scenario_id' => substr($id_node_output, 1),
                            'api_id' => $api_id,
                            'node_id' => $max_node_new,
                            'order' => $order,
                        ];
                        $arr_node_match[$id_node_input] = 1;
                        $arr_node_match['api_id' .$id_node_input] = $api_id;
                        $arr_node_match['node_'.$id_node_input] = $max_node_new;
                        $arr_node_match['order_'.$id_node_input] = $order;
                    }
                }
            } else {
                if (($arr_node_match[$id_node_input] ?? NULL)) {
                    $arr_scenation_related['save_scenario_learing'][] = [
                        'scenario_id' => substr($id_node_output, 1),
                        'api_id' => $arr_node_match['api_id' . $id_node_input] ?? NULL,
                        'node_id' => $arr_node_match['node_' . $id_node_input] ?? 0,
                        'order' => $arr_node_match['order_' . $id_node_input] ?? NULL,
                    ];
                } else {
                     //cut string
                    $input_result = str_replace('qc', '', $id_node_input);
                    $data_input = explode('_', $input_result);
                    $max_node_new = $max_node_new + 1;
                    $params['api_ids_ary'][] = $data_input[0] ?? NULL;
                    foreach ($qa_position as $position) {
                        $idP = str_replace('node-', '', ($position['id'] ?? NULL));
                        if ($idP == $id_node_input) {
                            $order = $position['order'] ?? NULL;
                            break;
                        }
                    }
                    foreach ($params['api_ids_ary'] as $idx => $api_id) {
                        $arr_scenation_related['save_scenario_learing'][] = [
                            'scenario_id' => substr($id_node_output, 1),
                            'api_id' => $api_id,
                            'node_id' => $max_node_new,
                            'order' => $order,
                        ];
                        $arr_node_match[$id_node_input] = 1;
                        $arr_node_match['api_id' . $id_node_input] = $api_id;
                        $arr_node_match['node_' . $id_node_input] = $max_node_new;
                        $arr_node_match['order_' . $id_node_input] = $order;
                    }
                }
            }
        } else if (strpos($id_node_input, 'q') !== false) {
            if (strpos($id_node_input, 'temp') !== false) {
                if (($arr_node_match[$id_node_input] ?? NULL)) {
                    $arr_scenation_related['save_scenario_learing'][] = [
                        'scenario_id' => substr($id_node_output, 1),
                        'api_id' => $arr_node_match['api_id' . $id_node_input] ?? NULL,
                        'node_id' => $arr_node_match['node_' . $id_node_input] ?? 0,
                        'order' => $arr_node_match['order_' . $id_node_input] ?? NULL,
                    ];
                } else {
                    //cut string
                    $input_cut_string = trim(substr($id_node_input, 0, strrpos($id_node_input, "-t")));
                    $id_result = str_replace('q', '', $input_cut_string);
                    $learning_repository = $this->bot_db_service->getLearningRepository();
                    $datas = $learning_repository->findOneBy(['id' => $id_result]);
                    foreach ($qa_position as $position) {
                        $idP = str_replace('node-', '', ($position['id'] ?? NULL));
                        if ($idP == $id_node_input) {
                            $order = $position['order'] ?? NULL;
                            break;
                        }
                    }
                    $params['api_ids_ary'][] = $datas['api_id'];
                    $max_node_new = $max_node_new + 1;
                    //指定された分追加
                    foreach ($params['api_ids_ary'] as $idx => $api_id) {
                        $arr_scenation_related['save_scenario_learing'][] = [
                            'scenario_id' => substr($id_node_output, 1),
                            'api_id' => $api_id,
                            'node_id' => $max_node_new,
                            'order' => $order,
                        ];
                        $arr_node_match[$id_node_input] = 1;
                        $arr_node_match['api_id' .$id_node_input] = $api_id;
                        $arr_node_match['node_'.$id_node_input] = $max_node_new;
                        $arr_node_match['order_'.$id_node_input] = $order;
                    }
                }
            } else {
                $learning_repository = $this->bot_db_service->getLearningRepository();
                $datas = $learning_repository->findOneBy(['id' => substr($id_node_input, 1)]);
                foreach ($qa_position as $position) {
                    $idP = str_replace('node-', '', ($position['id'] ?? NULL));
                    if ($idP == $id_node_input) {
                        $order = $position['order'] ?? NULL;
                        break;
                    }
                }
                $params['api_ids_ary'][] = $datas['api_id'];
                //指定された分追加
                foreach ($params['api_ids_ary'] as $idx => $api_id) {
                    $arr_scenation_related['save_scenario_learing'][] = [
                        'scenario_id' => substr($id_node_output, 1),
                        'api_id' => $api_id,
                        'node_id' => 0,
                        'order' => $order,
                    ];
                }
            }
        }
        return $max_node_new;
    }

    /**
     * 削除
     * @param $id
     */
    public function deleteFromId($id)
    {
        $this->repository->deleteOneById($id);
        //関連削除
        $this->saveKeyword($id)->saveRelation($id)->saveLearningRelation($id);
    }

    /**
     * キーワード保存
     * @param $id
     * @param $params
     * @return $this
     */
    private function saveKeyword($id, $params = null)
    {
        $keyword_repository = $this->bot_db_service->getScenarioKeywordRepository();
        $keyword_relation_repository = $this->bot_db_service->getScenarioKeywordRelationRepository();
        //シナリオIDで削除
        $keyword_relation_repository->setParams(['scenario_id' => $id])->filterByParams()->deleteByQuery();
        if ($params === null) return $this;
        //指定された分追加
        foreach ($params['multi_data'] as $row) {
            if (empty($row['keyword_id'])) {
                $keyword_id = $keyword_repository->findOrSave($row['keyword']);
            } else {
                $keyword_id = $row['keyword_id'];
            }
            $keyword_relation_repository->create([
                'scenario_id' => $id,
                'scenario_keyword_id' => $keyword_id,
            ]);
        }
        return $this;
    }

    /**
     * 一括キーワード保存
     * @param $id
     * @param $params
     * @return $this
     */
    private function saveKeywordMulti($arr_keyword)
    {
        $keyword_repository = $this->bot_db_service->getScenarioKeywordRepository();
        $keyword_relation_repository = $this->bot_db_service->getScenarioKeywordRelationRepository();
        $scenario_keyword = array();
        foreach ($arr_keyword as $keyword_data) {
            if (!$keyword_data['params']) {
                continue;
            }
            $group_no = 1;
            foreach ($keyword_data['params'] as $rows) {
                foreach ($rows as $key => $row) {
                    $keyword_id = $keyword_repository->findOrSave($row);
                    $scenario_keyword[] = array(
                        'scenario_id' => $keyword_data['id'],
                        'scenario_keyword_id' => $keyword_id,
                        'group_no' => $group_no,
                        'order' => $key + 1
                    );
                }
                $group_no++;
            }
        }
        $data = $keyword_relation_repository->createMulti($scenario_keyword);
        return $this;
    }

    /**
     * 紐づけ情報保存
     * @param $id
     * @param $params
     * @return $this
     */
    private function saveRelation($id, $params = null)
    {
        $this->bot_db_service->getScenarioRelationRepository()->clearQuery();
        $repository = $this->bot_db_service->getScenarioRelationRepository();
        //シナリオIDで削除
        $repository->setParams(['scenario_id' => $id])->filterByParams()->deleteByQuery();
        if ($params === null) return $this;
        //親がない場合、親ID=nullで登録
        if (empty($params['parent_ids'])) {
            $params['parent_ids_ary'] = [0 => null];
        }
        //指定された分追加
        foreach ($params['parent_ids_ary'] as $parent_id) {
            $repository->create([
                'scenario_id' => $id,
                'parent_scenario_id' => $parent_id,
            ]);
        }
        return $this;
    }

     /**
     * 一括紐づけ情報保存
     * @param $id
     * @param $params
     * @return $this
     */
    private function saveRelationMulti($arr_related)
    {
        $this->bot_db_service->getScenarioRelationRepository()->clearQuery();
        $repository = $this->bot_db_service->getScenarioRelationRepository();
        $repository->createMulti($arr_related);
        return $this;
    }

    /**
     * 回答紐づけ保存
     * @param $id
     * @param $params
     * @return $this
     */
    private function saveLearningRelation($id, $params = null)
    {
        $repository = $this->bot_db_service->getScenarioLearningRelationRepository();
        //シナリオIDで削除
        $repository->setParams(['scenario_id' => $id])->filterByParams()->deleteByQuery();
        if ($params === null || empty($params['api_ids'])) return $this;
        //指定された分追加
        foreach ($params['api_ids_ary'] as $idx => $api_id) {
            $repository->create([
                'scenario_id' => $id,
                'api_id' => $api_id,
                'order' => $idx + 1,
            ]);
        }
        return $this;
    }

    /**
     * リポジトリ取得
     * @return ScenarioRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return BotDbService
     */
    public function getBotDbService(): BotDbService
    {
        return $this->bot_db_service;
    }

    /**
     * Get data qa copy and caculate position node
     * @param $scenario_datas
     * @param $answer_datas
     * @return array $data
     */
    public function getDataCopyAndPosition($scenario_datas, $answer_datas)
    {
        $arr_qa_copy = array();
        foreach ($answer_datas as $key => $qa) {
            $nodeId = explode(',', object_get($qa, 'node_id'));
            $status = 0;
            foreach (array_unique($nodeId) as $node) {
                if ($node > 0) {
                    $status = 1;
                }
            }
            if ($status == 1) {
                $answer_datas[$key]->qc = 'qc';
                $arr_qa_copy['qc' . object_get($qa, 'api_id') . '_' . object_get($qa, 'node_id')] = [
                    'api_id' => object_get($qa, 'api_id'),
                    'node_id' => 'qc' . object_get($qa, 'api_id') . '_' . object_get($qa, 'node_id'),
                    'scenario_id' => object_get($qa, 'scenario_id'),
                    'question' => object_get($qa, 'question'),
                    'key_phrase' => object_get($qa, 'key_phrase'),
                    'answer' => object_get($qa, 'answer'),
                    'question' => object_get($qa, 'question'),
                    'category_id' => object_get($qa, 'category_id'),
                    'metadata' => object_get($qa, 'metadata')
                ];
            } else {
                $answer_datas[$key]->q = 'q';
            }
        }
        // Call function caculate position
        $this->caculatePosition($scenario_datas, $answer_datas);
        $arr_position = $this->tmp_position;
        $this->tmp_position = array();
        // Remove relation scenario duplicate
        foreach ($scenario_datas as $key => $scenario_data) {
            if ($scenario_data->parent_scenario_id) {
                $arr_parent_ids =  array_unique(explode(',', $scenario_data->parent_scenario_id));
                $scenario_datas[$key]->parent_scenario_id = implode(',',$arr_parent_ids);
            }
            if ($scenario_data->keyword_id) {
                $arr_keyword_ids = array_unique(explode(',', $scenario_data->keyword_id));
                $scenario_datas[$key]->keyword_id = implode(',',$arr_keyword_ids);
            }
        }
        $data['arr_position'] = $arr_position;
        $data['arr_qa_copy'] = $arr_qa_copy;
        return $data;
    }

    /**
     * Caculate position node by category
     * @param $data_senario
     * @param $data_learning
     * @return array data
     */
    private function caculatePosition($data_senario, $data_learning)
    {
        $data_position = array();
        $senario_str = 's';
        $data_senario_parent = array();
        foreach ($data_senario as $key => $sencario) {
            if (object_get($sencario, "parent_scenario_id") == null) {
                if (object_get($sencario, "order") == 0) {
                    $data_senario_parent[] =  [
                        'scenario_id' => $senario_str.object_get($sencario, 'scenario_id'),
                        'learning_id' => object_get($sencario, "learning_id"),
                        'parent_scenario' => object_get($sencario, "parent_scenario_id"),
                        'order' => 99999999
                    ];
                } else {
                    $data_senario_parent[] =  [
                        'scenario_id' => $senario_str.object_get($sencario, 'scenario_id'),
                        'learning_id' => object_get($sencario, "learning_id"),
                        'parent_scenario' => object_get($sencario, "parent_scenario_id"),
                        'order' => object_get($sencario, "order")
                    ];
                }
                
            } else {
                $data_position[$senario_str.object_get($sencario, 'scenario_id')] = [
                    'learning_id' => object_get($sencario, "learning_id"),
                    'parent_scenario' => object_get($sencario, "parent_scenario_id")
                ];
            }
        }
        $count_parent = count($data_senario_parent);
        // loop sort scenario
        for ($i = 0; $i < $count_parent - 1; $i++) {
            $min = $i;
            for ($j = $i + 1; $j < $count_parent; $j++) {
                if ($data_senario_parent[$j]['order'] < $data_senario_parent[$min]['order']) {
                    $min = $j;            
                }
            }
            $temp = $data_senario_parent[$i];
            $data_senario_parent[$i] = $data_senario_parent[$min];
            $data_senario_parent[$min] = $temp;
        }
        $data_parent_null = array();
        foreach ($data_senario_parent as $key => $sencario) {
            $data_parent_null[$sencario['scenario_id']] = [
                'learning_id' => $sencario['learning_id'],
                'parent_scenario' => $sencario['parent_scenario']
            ];
        }
        // $data_parent_null = $data_senario_parent_new;
        $learning_str = 'q';
        $learning_copy_str = 'qc';
        foreach ($data_learning as $key => $learning) {
            if (object_get($learning, 'qc') == $learning_copy_str) {
                $data_position[$learning_copy_str.object_get($learning, 'api_id'). '_' . object_get($learning, 'node_id')] = [
                    'parent_scenario' => object_get($learning, "scenario_id")
                ];
            } else if (object_get($learning, 'q') == $learning_str) {
                $data_position[$learning_str.object_get($learning, 'answer_id')] = [
                    'parent_scenario' => object_get($learning, "scenario_id")
                ];
            }
        }
        // position initialization xs
        $xs = 90;
        // position initialization ys
        $ys = 70;
        $this->ys = $ys;
        // rows node initialization
        $rows = array();
        // tree parent node initialization
        $tree = array();
        foreach ($data_parent_null as $key=> $value) {
            $rows[$key] = ['id' => $key, 'parent' => null];
            $this->recursivePositionAry($data_position, $rows);
        }
        foreach ($data_position as $key => $row) {
            if (is_array($row['parent_scenario'])) {
                $rows[$key] = ['id' => $key, 'parent' => 's' . $row['parent_scenario'][0]];
            } else {
                $rows[$key] = ['id' => $key, 'parent' => 's' . $row['parent_scenario']];
            }
        }
        $this->getTreeRows($rows, $tree);
        $parent_temp = array();
        foreach ($tree as $key => $value) {
            if (($value['data']['parent'] ?? NULL) != null) {
                $parent_temp[$key] = $value;
            }
            if (!empty(($this->tmp_position[$key] ?? NULL)) OR ($value['data']['parent'] ?? NULL) != null) {
                continue;
            }
            $this->tmp_position[$key] = array('x' => $xs, 'y' => $this->ys);
            $this->child[$key] = ['x' => $xs, 'y' => $this->ys, 'parent' => null];
            $this->recursiveTreeChild($value);
            $this->ys += 100;
        }
        foreach($parent_temp as $key => $value) {
            if (!empty(($this->tmp_position[$key] ?? NULL))) {
                continue;
            }
            $this->tmp_position[$key] = array('x' => $xs, 'y' => $this->ys);
            $this->ys += 100;
        }
        return;
    }

    /**
     * Get tree row parent - childrent
     * @param $rows
     * @param $tree
     * @return
     */
    private function getTreeRows(&$rows, &$tree) {
        foreach($rows as $key => $row) {
            if (
                false === array_key_exists($row['id'], $tree) ||
                false === array_key_exists('data', $tree[$row['id']])
            ) {
                if (!is_array($row['parent'])) {
                    $tree[$row['id']]['data'] = $row;
                }
            }
            if (!is_array($row['parent'])) {
                if (
                    $row['parent'] &&
                    (
                        false === array_key_exists($row['parent'], $tree) ||
                        false === array_key_exists('children', $tree[$row['parent']])
                    )
                ) {
                    $tree[$row['parent']]['children'] = [];
                }
                if ($row['parent']) {
                    $tree[$row['parent']]['children'][] = &$tree[$row['id']];
                }
                unset($rows[$key]);
            }
        }
        return;
    }
    /**
     * recursive get level child 
     * @param $value
     * @return array data
     */
    private function recursiveTreeChild($value) 
    {
        $childrent = $value['children'] ?? NULL;
        if ($childrent) {
            foreach ($childrent as $key => $value) {
                $parent_value = $value['data']['parent'];
                if ($key == 0) {
                    if (!empty(($this->child[$value['data']['parent']] ?? NULL))) {
                        $xs = (int) $this->child[$parent_value]['x'] + 300;
                        $ys = $this->child[$parent_value]['y'];
                        $this->child[$value['data']['parent']] = ['x' => $xs, 'y' => $ys, 'parent' => $this->child[$value['data']['parent']]['parent']];
                        $this->child[$value['data']['id']] = ['x' => $xs, 'y' => $ys, 'parent' => $parent_value];
                        $this->tmp_position[$value['data']['id']] = array('x' => $xs, 'y' => $ys);
                        // Sysn parent
                        $this->recursiveUpdateParentChild($xs, $ys, $this->child[$value['data']['parent']]['parent']);
                        $this->ys = $ys;
                    }
                } else {
                    $xs = (int) $this->child[$parent_value]['x'];
                    $ys = $this->child[$parent_value]['y'] + 100;
                    // Update Parent
                    $this->child[$value['data']['parent']] = ['x' => $xs, 'y' => $ys, 'parent' => $this->child[$value['data']['parent']]['parent']];
                    $this->child[$value['data']['id']] = ['x' => $xs, 'y' => $ys, 'parent' => $parent_value];
                    $this->tmp_position[$value['data']['id']] = array('x' => $xs, 'y' => $ys);
                    // Sysn parent
                    $this->recursiveUpdateParentChild($xs, $ys, $this->child[$value['data']['parent']]['parent']);
                    $this->ys = $ys;
                }
                $this->recursiveTreeChild($value);
            }

        }
        return;
    }

    /**
     * recursive position
     * @param $data_position
     * @param $rows
     * @return array data
     */
    private function recursivePositionAry(&$data_position, &$rows) {
        foreach($data_position as $key => $values) {
            $parent_arr =  array_unique(explode(',', $values['parent_scenario']));
            $row_keys = array_keys($rows);
            foreach ($row_keys as $key_row) {
                $key_scenario = str_replace('s', '', $key_row);
                if (in_array($key_scenario, $parent_arr)) {
                    if (($rows[$key] ?? NULL)) {
                        continue;
                    } else {
                        $rows[$key] = ['id' => $key, 'parent' => 's' . $key_scenario];
                    }
                    unset($data_position[$key]);
                    $this->recursivePositionAry($data_position, $rows);
                }
            }
        }
        return;
    }
    /**
     * recursive get level child 
     * @param $xs, $ys, $parentKey
     * @return array data
     */
    private function recursiveUpdateParentChild($xs, $ys, $parent_key) 
    {
        $key_child = array_keys($this->child);
        foreach ($key_child as $key) {
            if ($parent_key == $key) {
                $this->child[$key]['y'] = $ys;
                if ($this->child[$key]['parent'] != NULL) {
                    $this->recursiveUpdateParentChild($xs, $ys, $this->child[$key]['parent']);
                }
                
            }
        }
        return;
    }

    /**
     * Save scenario and relation
     * @param $category_id
     * @param $id_scenario
     * @param $relate_scenario
     * @param $scenario_datas
     * @param $qa_position
     * @return
     */
    public function saveScenarioEditor($category_id, $id_scenario, $relate_scenario, $scenario_datas, $qa_position)
    {
        DB::beginTransaction();
        try {
            $scenario_id = array();
            foreach ($scenario_datas as $scenario) {
                $scenario_id[] = $scenario->id;
            }
            //Remove relation scenario
            $this->repository->setParams(['id' => $scenario_id])->filterByParams()->deleteByQuery();
            $this->bot_db_service->getScenarioRelationRepository()->deleteDataDismisById($scenario_id);
            $this->bot_db_service->getScenarioLearningRelationRepository()->deleteDataDismisById($scenario_id);
            $this->bot_db_service->getScenarioKeywordRelationRepository()->deleteDataDismisById($scenario_id);
            // get all id scenario
            $arr_id_scenario = $this->repository->getData('id');
            $arr_scenation_related = array();
            foreach (($arr_id_scenario ?? []) as $key => $value) {
                $arr_id_scenario[$key] = $value->id;
            }
            foreach (($id_scenario ?? []) as $value) {
                $obj_scenario = json_decode($value);
                $params = [
                    'name' => object_get($obj_scenario, 'name'),
                    'category_id' => $category_id,
                    'order' => object_get($obj_scenario, 'order'),
                ];
                if (object_get($obj_scenario, 'multi_data')) {
                    $arr_keyword = (array) object_get($obj_scenario, 'multi_data');
                    foreach ($arr_keyword as $key => $obj) {
                        $arr_keyword[$key] = (array) $obj;
                    }
                    $params['multi_data'] = $arr_keyword;
                } else {
                    $params['multi_data'] = [];
                }
                $params['parent_ids'] = null;
                $id = $this->createFromFormDataReturnId($params, $arr_id_scenario);
                $arr_scenation_related['save_keyword'][] = array('id' => $id, 'params' => $params['multi_data'] ?? NULL);
                $arr_scenation_related['save_relation'][] = array('scenario_id' => $id, 'parent_scenario_id' => NULL);
                if (strpos(object_get($obj_scenario, 'id'), 's') !== false) {
                    $arr_node_id_tmp[object_get($obj_scenario, 'id')] = $id;
                } else {
                    $arr_node_id_tmp['s'. object_get($obj_scenario, 'id')] = $id;
                }
            }
            if ($arr_scenation_related) {
                // Save relation
                $this->saveKeywordMulti($arr_scenation_related['save_keyword'])->saveRelationMulti($arr_scenation_related['save_relation']);
            }
            $arr_scenation_related = array();
            if ($relate_scenario) {
                // Create connection
                $connection_node = array_unique($relate_scenario);
                // get node id max
                $max_node = $this->bot_db_service->getScenarioLearningRelationRepository()->getMaxNodeId()->getData();
                if ($max_node) {
                    $max_node_new = object_get($max_node[0], 'node_id');
                } else {
                    $max_node_new = 1;
                }
                $arr_node_match = array();
                foreach ($connection_node as $node) {
                    if ($node) {
                        //convert array
                        $node_connection = explode(',', $node);
                        $parent_node_id = ($arr_node_id_tmp[($node_connection[0] ?? NULL)] ?? NULL) ? 's'.$arr_node_id_tmp[$node_connection[0]] : $node_connection[0];
                        $node_id = ($arr_node_id_tmp[($node_connection['1'] ?? NULL)] ?? NULL) ? 's'.$arr_node_id_tmp[$node_connection['1']] : $node_connection['1'];
                        $max_node_new = $this->createConnectionScenario($node_id, $parent_node_id, $max_node_new, $qa_position, $arr_node_match, $arr_scenation_related);
                    }
                }
                // Save scenario - scenario, scenario - qa
                $this->bot_db_service->getScenarioRelationRepository()->clearQuery();
                $repository = $this->bot_db_service->getScenarioRelationRepository();
                if ($arr_scenation_related['delete_scenario_relate'] ?? NULL) {
                    $repository->deleteParentIdIsNull(array_unique($arr_scenation_related['delete_scenario_relate']));
                }
                if ($arr_scenation_related['save_scenario_relate'] ?? NULL) {
                    $repository->createMulti($arr_scenation_related['save_scenario_relate']);
                }
                if ($arr_scenation_related['save_scenario_learing'] ?? NULL) {
                    $this->bot_db_service->getScenarioLearningRelationRepository()->clearQuery();
                    $repository = $this->bot_db_service->getScenarioLearningRelationRepository();
                    $repository->createMulti($arr_scenation_related['save_scenario_learing']);
                }
            }
            DB::commit();
            return TRUE;
        } catch(\Exception $e) {
            DB::rollback();
            return FALSE;
        }
    }

    /**
     * 
     * Delete scenario
     * @param array id scenario
     * @return bool
     */
    public function deleteNode($arr_id = [], $route_name) {
        DB::beginTransaction();
        try {
            foreach ($arr_id as $value) {
                //delete scenario
                if (strpos($value, '-temp') === false) {
                    if (strpos($value, 's') !== false) {
                        $id = str_replace('s', '', $value);
                        $this->deleteFromId($id);
                    }
                }
            }
            $this->saveLog(config('const.function.' . $route_name . '_destroy.id'));
            DB::commit();
            return true;
        } catch(\Exception $e) {
            return false;
            DB::rollback();
        }
    }

    /**
     * 
     * Save scenario
     * @param int category_id
     * @param array id_scenario, relate_scenario, scenario_datas, qa_position
     * @param string route_name
     * @return bool
     */
    public function saveScenarioAndRelation($category_id, $id_scenario, $relate_scenario, $scenario_datas, $qa_position, $route_name)
    {
        $arr_scenation_related = $this->saveScenarioEditor($category_id, $id_scenario, $relate_scenario, $scenario_datas, $qa_position);
        if ($arr_scenation_related === FALSE) {
            return FALSE;
        }
        $this->saveLog(config('const.function.' . $route_name . '_store.id'));
        return TRUE;
    }

    /**
     * Get all Keyword
     * @return array
     */
    public function getAllKeyword() {
        $datas = $this->bot_db_service->getScenarioKeywordRepository()->getData();
        $all_keywords = [];
        foreach ($datas as $value) {
            $all_keywords[$value->keyword] = $value->keyword;
        }
        return $all_keywords;
    }
    /**
     * Process save file zip
     * @param array data_scenario
     * @return bool|path
     */
    public function saveZipFileJson($data_scenario, $dir_name)
    {
        $dir_name = $dir_name . '/' . config('scenario.import_export.dir_backup_name');
        $path_zip = config('scenario.import_export.name_zip');
        $conf_date = config('scenario.import_export.date_format');
        if (!File::isDirectory(storage_path('app/' . $dir_name))) {
            // Create forder
            File::makeDirectory(storage_path('app/' . $dir_name), 0777, true, true);
        }
        $data_file = array();
        foreach ($data_scenario as $key => $ary_scenario) {
            if (is_file(storage_path('app/' . $dir_name . $key . '.json'))) {
                // Remove file Json
                unlink(storage_path('app/' . $dir_name . $key . '.json'));
            }
            if ($ary_scenario) {
                if (Storage::disk('local')->put($dir_name . '/' . $key . '.json', json_encode($ary_scenario, JSON_UNESCAPED_UNICODE))) {
                    $data_file[] = storage_path('app/' . $dir_name . '/' . $key . '.json');
                }
            } else {
                if (Storage::disk('local')->put($dir_name . '/' . $key . '.json', '')) {
                    $data_file[] = storage_path('app/' . $dir_name . '/' . $key . '.json');
                }
            }
        }
        if (!$data_file) {
            return FALSE;
        }
        // get all data zip in directory
        $data_zip_file = glob(storage_path('app/' . $dir_name . '/*.zip'));
        if ($data_zip_file) {
            foreach ($data_zip_file as $path_file) {
                unlink($path_file);
            }
        }
        // Get Zip file
        $zip_path = storage_path('app/' . $dir_name . '/' . str_replace($conf_date, date('YmdHis'), $path_zip));
        $this->zip_service->setFiles($data_file)->compress($zip_path)->clearFiles();
        chmod($zip_path, 0775); 
        if ($this->zip_service->getResult() == $this->zip_service::RESULT_DONE) {
            return $zip_path;
        } else if ($this->zip_service->getResult() == $this->zip_service::RESULT_FAIL) {
            unlink($zip_path);
            return FALSE;
        }
        return $zip_path;
    }
    
    /**
     * Get all node scenario and qa
     * @param array arr_position
     * @param array $scenario_ary
     * @param array $qa_ary : QA Learning
     * @return array $group_node
     */
    public function groupNodeItem($arr_position, $scenario_ary, $qa_ary)
    {
        $group_node = array();
        try {
            $parent_node = [];
            foreach ($scenario_ary as $id => $scenario) {
                if ($scenario['parent']) {
                    $parent_node[] = array(
                        'id' => $id,
                        'parent_id' => array_unique(explode(',', $scenario['parent']), SORT_REGULAR)
                    );
                } else {
                    $parent_node[] = array(
                        'id' => $id,
                        'parent_id' => array()
                    );
                }
            }
            $parent_sort = array();
            // Sort scenario follow position
            foreach (array_keys($arr_position) as $id_s) {
                if (strpos($id_s, 's') !== false) {
                    $id = str_replace('s', '', $id_s);
                    foreach ($parent_node as $key => $parents) {
                        if ($parents['id'] == $id) {
                            $parent_sort[] = $parents;
                            unset($parent_node[$key]);
                            break;
                        }
                    }
                }
            }
            // Get tree view scenario
            $trees = $this->buildTree($parent_sort);
            $tree_parent = array();
            foreach ($trees as $key_tree => $tree) {
                if ($scenario_ary[$key_tree]['parent'] == null) {
                    $tree_parent[$key_tree] = $tree;
                }
            }
            // Get list node
            $list_node = $this->flatten($tree_parent);
            foreach ($list_node as $node_txt) {
                $group_node[] = explode('/', $node_txt);
            }
            // Array assign qa into scenario
            $group_node_qa = array();
            foreach ($qa_ary as $qa_obj) {
                $scenario_arr = explode(',', $qa_obj->scenario_id);
                $node_qa = '';
                foreach ($scenario_arr as $key => $id_s) {
                    foreach ($group_node as $position => $ary_group) {
                        $keys = array_search('s' . $id_s, $ary_group);
                        if ($keys !== false) {
                            if (end($ary_group) == 's' . $id_s) {
                                array_push($group_node[$position], 'q' . $qa_obj->id);
                            } else if ($scenario_ary[$id_s]['parent'] == NULL) {
                                $group_node_qa[] = ['s' . $id_s, 'q' . $qa_obj->id];
                            } else {
                                $group_node_qa[] = array_merge(array_diff($ary_group, array_splice($ary_group, $keys + 1)), ['q' . $qa_obj->id]);
                            }
                        }
                    }
                }
            }
            foreach (array_unique($group_node_qa, SORT_REGULAR) as $q => $qa_group) {
                $max = 0;
                foreach ($group_node as $key => $node) {
                    if ($node[0] == $qa_group[0]) {
                        $max = $key;
                    }
                }
                array_splice($group_node, $max + 1, 0,  [$qa_group]);
            }
            return array_unique($group_node, SORT_REGULAR);
        } catch (\Exception $e) {
            return $group_node;
        }
    }
    
    /**
     * Import file zip
     * @param Request $request
     * @param string $key_path
     * @return bool TRUE|FALSE
     */
    public function import($request, $key_path)
    {
        $zip_path = storage_path('app/' . $key_path);
        if (!is_file($zip_path)) {
            return false;
        }
        try {
            DB::beginTransaction();
            $zip = new ZipArchive;
            if ($zip->open($zip_path, ZipArchive::CREATE) === true) {
                $path = pathinfo(realpath($zip_path), PATHINFO_DIRNAME);
                $zip->extractTo($path);
                $zip->close();
                Storage::delete($zip_path);
                foreach (glob($path . '/*.json') as $path) {
                    $data_ary = json_decode(file_get_contents($path), true);
                    switch (basename($path)) {
                        case 'tbl_scenario.json':
                            $this->bot_db_service->getScenarioRepository()->deleteByQuery();
                            if ($data_ary) {
                                $this->bot_db_service->getScenarioRepository()->createMulti($data_ary);
                            }
                            break;
                        case 'tbl_scenario_relation.json':
                            $this->bot_db_service->getScenarioRelationRepository()->deleteByQuery();
                            if ($data_ary) {
                                $this->bot_db_service->getScenarioRelationRepository()->createMulti($data_ary);
                            }
                            break;
                        case 'tbl_scenario_keyword.json':
                            $this->bot_db_service->getScenarioKeywordRepository()->deleteByQuery();
                            if ($data_ary) {
                                $this->bot_db_service->getScenarioKeywordRepository()->createMulti($data_ary);
                            }
                            break;
                        case 'tbl_scenario_keyword_relation.json':
                            $this->bot_db_service->getScenarioKeywordRelationRepository()->deleteByQuery();
                            if ($data_ary) {
                                $this->bot_db_service->getScenarioKeywordRelationRepository()->createMulti($data_ary);
                            }
                            break;
                        case 'tbl_scenario_learning_relation.json':
                            $this->bot_db_service->getScenarioLearningRelationRepository()->deleteByQuery();
                            if ($data_ary) {
                                $this->bot_db_service->getScenarioLearningRelationRepository()->createMulti($data_ary);
                            }
                            break;
                    }
                    unlink($path);
                }
                unlink($zip_path);
                DB::commit();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}