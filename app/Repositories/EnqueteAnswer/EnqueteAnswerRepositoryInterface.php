<?php

namespace App\Repositories\EnqueteAnswer;

use App\Repositories\RepositoryInterface;

/**
 * アンケート回答リポジトリインターフェース
 * Interface EnqueteAnswerRepositoryInterface
 * @package App\Repositories\EnqueteAnswer
 */
interface EnqueteAnswerRepositoryInterface extends RepositoryInterface
{
    /**
     * 回答登録
     * @param array $data
     * @param bool $is_crypt
     * @return mixed
     */
    public function createData(array $data, $is_crypt = false);


    /**
     * 復号化フィルタ
     * @param $form_id
     * @return $this
     */
    public function filterDecrypt($form_id);
    
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
    public function getEnqueteAnswer($start_date_data, $end_date_data, $question_code, $ip, $channel);
}