<?php

namespace App\Repositories;

use Illuminate\Database\Query\Builder;

/**
 * リポジトリインターフェース
 * Interface RepositoryInterface
 * @package App\Repositories
 */
interface RepositoryInterface
{
    /**
     * モデルクラス名種痘
     * @return string
     */
    public function getModelClass();

    /**
     * クエリビルダ取得
     * @return Builder
     */
    public function getQuery();

    /**
     * クエリビルダクリア
     * @return \Illuminate\Database\Query\Builder
     */
    public function clearQuery();

    /**
     * DB結果クラス取得
     * @return DbResultInterface
     */
    public function getDbResult(): DbResultInterface;

    /**
     * DB結果取得前処理
     * @param $query \Illuminate\Database\Query\Builder|mixed
     */
    public function beforeDbResult($query);

    /**
     * IDで一件取得
     * @param $id
     * @return array
     */
    public function getOneById($id);

    /**
     * ID配列で取得
     * @param array $ids
     * @return mixed
     */
    public function getByIds(array $ids);

    /**
     * 全件取得
     * @return array
     */
    public function getAll();

    /**
     * フィルタで取得
     * @param array $filter
     * @return mixed
     */
    public function findBy(array $filter);

    /**
     * フィルタで一件取得
     * @param array $filter
     * @return mixed
     */
    public function findOneBy(array $filter);

    /**
     * パラメータセット
     * @param $params
     * @return $this
     */
    public function setParams($params): self;

    /**
     * パラメータゲット
     * @param $is_dt
     * @return mixed
     */
    public function getParams($is_dt);

    /**
     * 並び順セット
     * @param $order
     * @return $this
     */
    public function setOrder($order): self;

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group): self;

    /**
     * ページネーション
     * @param $page
     * @return mixed
     */
    public function paginate($page);

    /**
     * データテーブル
     * @param array $params
     * @return DatatablesResult
     */
    public function datatable($params = []): DatatablesResult;

    /**
     * IDで一件削除
     * @param $id
     * @return mixed
     */
    public function deleteOneById($id);

    /**
     * 一件追加
     * @param $data
     * @return mixed
     */
    public function create($data);
    
    /**
     * 一括追加
     * @param $data
     * @return mixed
     */
    public function createMulti($data);

    /**
     * 一件更新
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data);

    /**
     * クエリビルダから削除
     * @return mixed
     */
    public function deleteByQuery();

    /**
     * クエリビルダから更新
     * @param array $data
     * @return mixed
     */
    public function updateByQuery(array $data);

    /**
     * パラメータでフィルタリング実行
     * @return $this
     */
    public function filterByParams(): self;

    /**
     * トランケート
     * @return type
     */
    public function truncate();

    /**
     * 次の値を取得
     * @param $column
     * @return mixed
     */
    public function getNextId($column);

    /**
     * トランザクション
     * @param callable $callback
     * @param int $attempts
     * @return mixed
     */
    public function transaction(Callable $callback, $attempts = 1);

    /**
     * @param $key_col
     * @param $val_col
     * @param bool $add_blank
     * @return mixed
     */
    public function getChoice($key_col, $val_col, $add_blank = true);

}
