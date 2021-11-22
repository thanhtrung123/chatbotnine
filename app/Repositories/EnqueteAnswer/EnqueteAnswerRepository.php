<?php

namespace App\Repositories\EnqueteAnswer;

use App\Models\EnqueteAnswer;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use DB;

/**
 * アンケート回答リポジトリ
 * Class EnqueteAnswerRepository
 * @package App\Repositories\EnqueteAnswer
 */
class EnqueteAnswerRepository extends AbstractRepository implements EnqueteAnswerRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return EnqueteAnswer::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return RepositoryInterface
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        $params = array_merge(($this->dt_params ?? []), ($this->params ?? []));
        // Params
        if (!empty($params['post_id'])) {
            $query->where('post_id', $params['post_id']);
        }
        if (!empty($params['chat_id'])) {
            $query->where('chat_id', $params['chat_id']);
        }
        if (!empty($params['date_s'])) {
            $query->whereColumn(DB::raw("DATE_FORMAT(posted_at, '%Y%m%d')"), '>=', DB::raw("DATE_FORMAT('{$params['date_s']}', '%Y%m%d')"));
        }
        if (!empty($params['date_e'])) {
            $query->whereColumn(DB::raw("DATE_FORMAT(posted_at, '%Y%m%d')"), '<=', DB::raw("DATE_FORMAT('{$params['date_e']}', '%Y%m%d')"));
        }
        
        return $this;
    }

    /**
     * 回答登録
     * @param array $data
     * @param bool $is_crypt
     * @return mixed
     */
    public function createData(array $data, $is_crypt = false)
    {
        if ($is_crypt)
            $data['answer'] = $this->getCryptValue($data['answer'], true);
        return $this->create($data);
    }

    /**
     * 復号化フィルタ
     * @param $form_id
     * @return $this|EnqueteAnswerRepositoryInterface
     */
    public function filterDecrypt($form_id)
    {
        $base_tbl = $this->model->getTable();
        $query = $this->getQuery();
        $query->select();
        $form_items = config('enquete.form.' . $form_id . '.items');
        $crypt_items = array_keys(array_filter($form_items, function($item) {
            return $item['is_crypt'] ?? false;
        }));
        $query->selectRaw(
            "CASE WHEN `{$base_tbl}`.`question_code` IN (" . implode(',', $crypt_items) . ') THEN ' . 
            $this->getDecryptValue("{$base_tbl}.answer", true) . " ELSE `{$base_tbl}`.`answer` END `answer`"
        );
        
        return $this;
    }

    /**
     * アンケート回答数
     * @return int|mixed
     */
    public function getEnqueteAnswerCount()
    {
        return $this->query->count(DB::raw('distinct post_id'));
    }

    /**
     * エクスポート用フィルタ
     * @return $this
     */
    public function filterExport(): EnqueteAnswerRepositoryInterface
    {
        $base_tbl = $this->model->getTable();
        $query = $this->getQuery();
        $query->orderBy("{$base_tbl}.id");
        return $this;
    }
    
    /**
     * Get data enquete answer
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $question_code
     * @param $ip
     * @param $channel
     * 
     * @return mixed
     */
    public function getEnqueteAnswer($start_date_data, $end_date_data, $question_code, $ip, $channel)
    {
        $query = $this->clearQuery()
            ->select('tbl_enquete_answer.answer' , 'tbl_enquete_answer.question_code')
            ->join('tbl_response_info', 'tbl_response_info.chat_id', '=', $this->model->getTable().'.chat_id')
            ->where('form_id', '=', config('const.enquete.form_id.user_form.id'))
            ->where('posted_at', '>=', $start_date_data . ' 00:00:00')
            ->where('posted_at', '<=', $end_date_data . ' 23:59:59')
            ->whereIn('question_code', $question_code);
        // Check IP
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        // Check Channel
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        $query = $query->groupBy('tbl_enquete_answer.id')->get();
        return $query;
    }
}