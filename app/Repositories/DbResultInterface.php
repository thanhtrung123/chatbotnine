<?php

namespace App\Repositories;

use App\Services\DataConvertService;

/**
 * DB結果用インターフェース
 * Interface DbResultInterface
 * @package App\Repositories
 */
interface DbResultInterface
{

    /**
     * 件数取得
     * @return integer
     */
    public function getCount();

    /**
     * 配列取得（toArray）
     * @return array
     */
    public function getArray();

    /**
     * 配列取得（強制的に素の配列に戻している）
     * @return array
     */
    public function getPlainArray();

    /**
     * 一件取得
     * @return array
     */
    public function getOne();

    /**
     * ジェネレータ取得
     * @return \Generator
     */
    public function getGenerator(): \Generator;

    /**
     * チャンクで取得
     * @param callable $callback
     * @return int
     */
    public function getChunk(callable $callback): int;

    /**
     * コンバータ取得
     * @return DataConvertService
     */
    public function getConverter();

    /**
     * 取得件数セット
     * @param $limit
     * @return mixed
     */
    public function setLimit($limit);

    /**
     * オフセットをセット
     * @param $offset
     * @return mixed
     */
    public function setOffset($offset);
}