<?php

namespace App\Repositories\Learning;

use App\Repositories\DbResultInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\type;

/**
 * interface QuestionRepositoryInterface
 * 学習データリポジトリインターフェース
 */
interface LearningRepositoryInterface extends RepositoryInterface
{

    /**
     * キーワードで絞込
     * @param $keyword
     * @return mixed
     */
    public function findByKeyword($keyword);

    /**
     * 学習データ取得
     * @return DbResultInterface
     */
    public function getLearningData(): DbResultInterface;

    /**
     * 次のAPI_ID取得
     * @return int
     */
    public function getNextApiId(): int;

    /**
     * キーボード一覧用フィルタ
     * @return $this
     */
    public function filterKeyPhraseList(): self;

    /**
     * キーフレーズ追加フィルタ
     * @return $this
     */
    public function filterAddKeyPhrase(): self;

    /**
     * Join table scenario by table learning.category_id
     * @return $this
     */
    public function joinScenario($category_id = null, $node = null, $scenario_id): self;    
}