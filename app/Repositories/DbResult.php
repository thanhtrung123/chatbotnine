<?php

namespace App\Repositories;

use App\Repositories\Traits\DataConvert;
use App\Services\DataConvertService;
use Illuminate\Database\Query\Builder;

/**
 * DB結果用クラス(Eloquant依存)
 * Class DbResult
 * @package App\Repositories
 */
class DbResult implements DbResultInterface
{
    /* @var Builder */
    private $query;
    /* @var DataConvertService */
    private $converter;

    /**
     * DbResult constructor.
     * @param $query
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->converter = app(DataConvertService::class);
    }

    /**
     * 件数取得
     * @return int
     */
    public function getCount()
    {
        return $this->query->count();
    }

    /**
     * 配列で取得
     * @return mixed
     */
    public function getArray()
    {
        $data = $this->query->get();
        return $data ? $this->converter->convertData($data->toArray()) : [];
    }

    /**
     * 配列で取得（強制的に素の配列にする）
     * @return mixed
     */
    public function getPlainArray()
    {
        $data = $this->getArray();
        foreach ($data as &$row) {
            $row = (array)$row;
        }
        return $data;
    }

    /**
     * 一件取得
     * @return array
     */
    public function getOne()
    {
        $ary = $this->setLimit(1)->getArray();
        return empty($ary) ? [] : (array)$ary[0];
    }

    /**
     * チャンクで取得
     * @param callable $callback
     * @param int $chunk_size
     * @return int
     */
    public function getChunk(callable $callback, $chunk_size = 100): int
    {
        $cnt = 0;
        $this->query->chunk($chunk_size, function ($models) use ($callback, &$cnt) {
            foreach ($models as $model) {
                if ($model instanceof \stdClass) {
                    $row = (array)$model;
                } else {
                    $row = $model->toArray();
                }
                $callback($this->converter->convertRow($row, $cnt));
                $cnt++;
            }
        });
        return $cnt;
    }

    /**
     * ジェネレータを取得
     * @return \Generator
     */
    public function getGenerator(): \Generator
    {
        foreach ($this->query->get() as $idx => $model) {
            if ($model instanceof \stdClass) {
                $row = (array)$model;
            } else {
                $row = $model->toArray();
            }
            yield $this->converter->convertRow($row, $idx);
        }
    }

    /**
     * データーコンバータ取得
     * @return DataConvertService
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * 件数制限
     * @param integer $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->query->limit($limit);
        return $this;
    }

    /**
     * オフセット
     * @param integer $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->query->offset($offset);
        return $this;
    }
}