<?php

namespace App\Repositories\KeyPhrase;

use App\Repositories\RepositoryInterface;

/**
 * キーフレーズリポジトリインターフェース
 */
interface KeyPhraseRepositoryInterface extends RepositoryInterface
{
    /**
     * キーフレーズがあればID、無ければ登録してからID　取得
     * @param $word
     * @param int $type
     * @param bool $word_prioritize
     * @return mixed
     */
    public function findOrSave($word, $type = 0, $word_prioritize = false);

    /**
     * キーフレーズのID取得
     * @param $word
     * @param bool $word_prioritize
     * @return mixed
     */
    public function findOnly($word, $word_prioritize = false);

    /**
     * キーフレーズ一覧用フィルタ
     * @return $this
     */
    public function filterKeyPhraseList();

    /**
     * チョイス用フィルタ
     * @return $this
     */
    public function filterChoice();

    /**
     * 次のキーフレーズID取得
     * @return int
     */
    public function getNextKeyPhraseId();

}