<?php

namespace App\Repositories\Truth;

use App\Repositories\RepositoryInterface;

/**
 * 真理表リポジトリインターフェース
 * Interface TruthRepositoryInterface
 * @package App\Repositories\Truth
 */
interface TruthRepositoryInterface extends RepositoryInterface
{

    /**
     * ワードカウント用フィルタ
     * @return $this
     */
    public function filterWordCount(): self;

    /**
     * API_ID毎ワードカウント用フィルタ
     * @return $this
     */
    public function filterWordCountPerApiId(): self;

}