<?php

namespace App\Repositories\ResponseInfoUser;

use App\Repositories\RepositoryInterface;

/**
 * 応答情報（利用者）リポジトリインターフェース
 * Interface ResponseInfoUserRepositoryInterface
 * @package App\Repositories\ResponseInfoUser
 */
interface ResponseInfoUserRepositoryInterface extends RepositoryInterface
{

    /**
     * 保存
     * @param array $data
     * @return mixed
     */
    public function saveLog(array $data);

    /**
     * 同じ情報が存在するか
     * @param array $data
     * @return mixed
     */
    public function existsEqualsData(array $data);


}