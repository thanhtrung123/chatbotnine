<?php

namespace App\Services\Bot;

use App\Services\RepositoryServiceInterface;
use App\Repositories\ResponseInfo\ResponseInfoRepositoryInterface;
use App\Repositories\ResponseInfoTruth\ResponseInfoTruthRepositoryInterface;

/**
 * 応答情報サービス
 * Class ResponseInfoService
 * @package App\Services\Bot
 */
class ResponseInfoService implements RepositoryServiceInterface
{
    /**
     * @var ResponseInfoRepositoryInterface
     */
    private $repository;
    /**
     * @var ResponseInfoTruthRepositoryInterface
     */
    private $truth_repository;

    /**
     * ResponseInfoService constructor.
     * @param ResponseInfoRepositoryInterface $repository
     * @param ResponseInfoTruthRepositoryInterface $truth_repository
     */
    public function __construct(ResponseInfoRepositoryInterface $repository, ResponseInfoTruthRepositoryInterface $truth_repository)
    {
        $this->repository = $repository;
        $this->truth_repository = $truth_repository;
    }

    /**
     * リポジトリ取得
     * @return ResponseInfoRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * 真理表リポジトリ取得
     * @return ResponseInfoTruthRepositoryInterface
     */
    public function getTruthRepository()
    {
        return $this->truth_repository;
    }

    /**
     * 詳細取得
     * @param integer $id tbl_response_info.id
     * @return \App\Repositories\DbResultInterface
     */
    public function getDetail($id)
    {
        $base_data = $this->repository->getOneById($id);
        return $this->repository->setParams([
            'user_ip' => $base_data['user_ip'],
            'chat_id' => $base_data['chat_id'],
            'talk_id' => $base_data['talk_id'],
            'not_status' => $base_data['status'],
        ])->filterByParams()->getDbResult();
    }

    /**
     * 真理表詳細取得
     * @param integer $id tbl_response_info.id
     * @return \App\Repositories\DbResultInterface
     */
    public function getTruthDetail($id)
    {
        $base_data = $this->repository->getOneById($id);
        return $this->truth_repository->setParams([
            'info_id' => $base_data['id'],
        ])->filterByParams()->getDbResult();
    }

    /**
     * 集計取得
     * @param integer $base 集計基準ID
     * @param integer $type 集計種類ID
     * @param string $date 集計日付
     * @return array
     */
    public function getAggregate($base, $type, $date)
    {
        $data = [];
        $result = $this->repository->filterAggregate($base, $type, $date)->getDbResult()->getGenerator();
        foreach ($result as $row) {
            $data[] = [
                'aggregate_date' => $date,
                'group_id' => $row['group_id'] ?? null,
                'group_string' => $row['group_string'] ?? null,
                'aggregate_base' => $base,
                'aggregate_type' => $type,
                'total_value' => $row['cnt'],
            ];
        }
        return $data;
    }
}