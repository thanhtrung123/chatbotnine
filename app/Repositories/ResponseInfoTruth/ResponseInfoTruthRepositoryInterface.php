<?php

namespace App\Repositories\ResponseInfoTruth;

use App\Repositories\RepositoryInterface;

/**
 * 応答情報（真理表）リポジトリインターフェース
 * Interface ResponseInfoTruthRepositoryInterface
 * @package App\Repositories\ResponseInfoTruth
 */
interface ResponseInfoTruthRepositoryInterface extends RepositoryInterface
{

    /**
     * 保存
     * @param array $data
     * @return bool
     */
    public function saveLog(array $data): bool;

}