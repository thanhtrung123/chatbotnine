<?php

namespace App\Services\Bot\Truth;

use App\Repositories\ResponseInfoTruth\ResponseInfoTruthRepositoryInterface;

/**
 * 真理表用応答ログサービス
 * Class TruthLogService
 * @package App\Services\Bot\Truth
 */
class TruthLogService
{
    /**
     * @var bool 有効フラグ
     */
    private $enabled = true;
    /**
     * @var ResponseInfoTruthRepositoryInterface
     */
    private $info_repository;
    /**
     * @var integer tbl_response_info.id
     */
    private $info_id;
    /**
     * @var string YESワード
     */
    private $yes_word;
    /**
     * @var string NOワード
     */
    private $no_word;

    /**
     * TruthLogService constructor.
     * @param ResponseInfoTruthRepositoryInterface $info_repository
     */
    public function __construct(ResponseInfoTruthRepositoryInterface $info_repository)
    {
        $this->info_repository = $info_repository;
    }

    /**
     * 作成
     * @return bool
     */
    public function create()
    {
        $data = [
            'info_id' => $this->info_id,
            'yes_word' => $this->yes_word,
            'no_word' => $this->no_word,
        ];
        $this->clear();
        return $this->enabled ? $this->info_repository->saveLog($data) : false;
    }

    /**
     * クリア
     * @return $this
     */
    public function clear()
    {
        $this->setInfoId(0)
            ->setYesWord(null)
            ->setNoWord(null);
        return $this;
    }

    /**
     * 紐づけID
     * @return mixed
     */
    public function getInfoId()
    {
        return $this->info_id;
    }

    /**
     * YESワード
     * @return mixed
     */
    public function getYesWord()
    {
        return $this->yes_word;
    }

    /**
     * NOワード
     * @return mixed
     */
    public function getNoWord()
    {
        return $this->no_word;
    }

    /**
     * 紐づけIDセット
     * @param $info_id
     * @return $this
     */
    public function setInfoId($info_id)
    {
        $this->info_id = $info_id;
        return $this;
    }

    /**
     * YESワードセット
     * @param $yes_word
     * @return $this
     */
    public function setYesWord($yes_word)
    {
        $this->yes_word = $yes_word;
        return $this;
    }

    /**
     * NOワードセット
     * @param $no_word
     * @return $this
     */
    public function setNoWord($no_word)
    {
        $this->no_word = $no_word;
        return $this;
    }

    /**
     * 有効
     * @param $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
}