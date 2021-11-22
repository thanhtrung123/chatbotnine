<?php

namespace App\Repositories;

use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use DB;

/**
 * リポジトリ抽象クラス
 * Class AbstractRepository
 * @package App\Repositories
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /** @var Model */
    protected $model;
    /** @var Guard */
    protected $auth;
    /** @var Builder */
    protected $query;
    /**
     * @var array パラメータ
     */
    protected $params;
    /**
     * @var array DataTables用パラメータ
     */
    protected $dt_params;
    /**
     * @var bool 暗号化キー宣言済みフラグ
     */
    private $crypt_key_defined = false;

    /**
     * モデルクラス名取得
     * @return string
     */
    abstract public function getModelClass();

    /**
     * AbstractRepository constructor.
     */
    public function __construct()
    {
        $this->model = app($this->getModelClass());
        $this->query = $this->model->newQuery();

        // This instantiation may fail during a console command if e.g. APP_KEY is empty,
        // rendering the whole installation failing.
        try {
            $this->auth = app(Guard::class);
        } catch (Exception $e) {

        }
    }

    /**
     * クエリビルダ取得
     * @return \Illuminate\Database\Query\Builder|mixed
     */
    public function getQuery()
    {
        return $this->query->getQuery();
    }

    /**
     * クエリビルダクリア
     * @return \Illuminate\Database\Query\Builder
     */
    public function clearQuery()
    {
        $this->query = $this->model->newQuery();
        return $this->query->getQuery();
    }

    /**
     * DB結果クラス取得
     * @return DbResultInterface
     */
    public function getDbResult(): DbResultInterface
    {
        $query = $this->getQuery();
        $this->beforeDbResult($query);
        $result = new DbResult($query);
        $this->query = $this->model->newQuery();
        return $result;
    }

    /**
     * DB結果クラス取得前処理
     * @param $query \Illuminate\Database\Query\Builder|mixed
     */
    public function beforeDbResult($query)
    {
    }


    /**
     * IDで一件取得
     * @param $id
     * @return array|null
     */
    public function getOneById($id)
    {
        $find = $this->model->find($id);
        return $find ? $find->toArray() : null;
    }

    /**
     * ID配列で取得
     * @param array $ids
     * @return array|null
     */
    public function getByIds(array $ids)
    {
        $find = $this->model->whereIn($this->model->getKeyName(), $ids)->get();
        return $find ? $find->toArray() : null;
    }

    /**
     * 全件取得
     * @return array|null
     */
    public function getAll()
    {
        $find = $this->model->all();
        return $find ? $find->toArray() : null;
    }

    /**
     * フィルタで取得
     * @param array $filter
     * @return array|null
     */
    public function findBy(array $filter)
    {
        $builder = $this->model->newQuery();
        foreach ($filter as $key => $val) {
            $builder->where($key, $val);
        }
        $find = $builder->get();
        return $find ? $find->toArray() : null;
    }

    /**
     * フィルタで一件取得
     * @param array $filter
     * @return array|mixed
     */
    public function findOneBy(array $filter)
    {
        $builder = $this->model->newQuery();
        foreach ($filter as $key => $val) {
            $builder->where($key, $val);
        }
        $data = $builder->first();
        return $data ? $data->toArray() : [];
    }

    /**
     * IDで一件削除
     * @param $id
     * @return mixed
     */
    public function deleteOneById($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * 一件追加
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return $this->model->create($data);
    }
    
    /**
     * 一括追加
     * @param $data
     * @return mixed
     */
    public function createMulti($data)
    {
        return $this->model->insert($data);
    }

    /**
     * 一件更新
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data)
    {
        return $this->model->find($id)->update($data);
    }

    /**
     * クエリで削除
     * @return mixed
     */
    public function deleteByQuery()
    {
        $ret = $this->query->delete();
        $this->query = $this->model->newQuery();
        return $ret;
    }

    /**
     * クエリで更新
     * @param array $data
     * @return int
     */
    public function updateByQuery(array $data)
    {
        $ret = $this->query->update($data);
        $this->query = $this->model->newQuery();
        return $ret;
    }

    /**
     * パラメータセット
     * @param $params
     * @return RepositoryInterface
     */
    public function setParams($params): RepositoryInterface
    {
        if (isset($params['params'])) {
            if (is_array($params['params'])) {
                $this->dt_params = $params['params'];
            } else {
                parse_str($params['params'], $this->dt_params);
            }
        }
        $this->params = $params;
        return $this;
    }

    /**
     * パラメータ取得
     * @param $is_dt
     * @return mixed
     */
    public function getParams($is_dt = false)
    {
        return $is_dt ? $this->dt_params : $this->params;
    }

    /**
     * 並び順セット
     * @param $order
     * @return $this
     */
    public function setOrder($order): RepositoryInterface
    {
        $query = $this->getQuery();
        foreach ($order as $key => $value) {
            $query->orderBy($key, $value);
        }
        return $this;
    }

    /**
     * @param $group
     * @return RepositoryInterface
     */
    public function setGroup($group): RepositoryInterface
    {
        $query = $this->getQuery();
        $query->groupBy($group);
        return $this;
    }

    /**
     * ページネーション
     * @param $page
     * @return LengthAwarePaginator|mixed
     */
    public function paginate($page)
    {
        return $this->query->paginate($page);
    }

    /**
     * データーテーブル
     * @param array $params
     * @return DatatablesResult
     */
    public function datatable($params = []): DatatablesResult
    {
        $params = array_merge($this->params, $params);
        $this->setDatatableOrder($params);
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->paginate($params['length'] ?? 1);
        $paginate = $paginator->toArray();
        foreach (array_keys($paginate['data']) as $idx) {
            $paginate['data'][$idx]['_empty'] = '';
        }
        return new DatatablesResult($paginate['data'], $paginate['total'], $params['draw'] ?? 1);
    }

    /**
     * データーテーブル用並び順セット
     * @param $params
     */
    private function setDatatableOrder($params)
    {
        if (empty($params['order'])) return;
        foreach ($params['order'] as $order) {
            $columns = $params['columns'][$order['column']];
            if ($columns['orderable'] == 'false') continue;
            $this->query->orderBy($columns['name'], $order['dir']);
        }
    }

    /**
     * パラメータでフィルタリング実行（オーバーライト用）
     * @return RepositoryInterface
     */
    public function filterByParams(): RepositoryInterface
    {
        foreach ($this->params as $key => $val) {
            $this->query->where($key, $val);
        }
        return $this;
    }

    /**
     * トランケート
     * @return type
     */
    public function truncate()
    {
        return $this->model->truncate();
    }

    /**
     * 次の値を取得
     * @param $column
     * @return int
     */
    public function getNextId($column)
    {
        $this->query->select($column)->orderBy($column, 'desc')->lockForUpdate();
        $row = $this->getDbResult()->getOne();
        return empty($row) ? 1 : $row[$column] + 1;
    }

    /**
     * 暗号化用の値を取得
     * @param $value
     * @param bool $is_plain_column
     * @return \Illuminate\Database\Query\Expression
     */
    public function getCryptValue($value, $is_plain_column = false)
    {
        $this->cryptKeyDefine();
        $value = $this->model->getConnection()->getPdo()->quote($value);
        if ($is_plain_column)
            $ret = DB::raw("HEX(AES_ENCRYPT({$value},@aes_key))");
        else
            $ret = DB::raw("AES_ENCRYPT({$value},@aes_key)");
        return $ret;
    }

    /**
     * 復号化用の値を取得
     * @param $column
     * @param bool $is_plain_column
     * @param null $as
     * @return \Illuminate\Database\Query\Expression
     */
    public function getDecryptValue($column, $is_plain_column = false, $as = null)
    {
        $this->cryptKeyDefine();
        $column = $this->getQuery()->getGrammar()->wrap($column);
        if ($is_plain_column)
            $ret = DB::raw("AES_DECRYPT(UNHEX(TRIM({$column})),@aes_key)" . (!is_null($as) ? " as `{$as}`" : ''));
        else
            $ret = DB::raw("AES_DECRYPT({$column},@aes_key)" . (!is_null($as) ? " as `{$as}`" : ''));
        return $ret;
    }

    /**
     * 暗号化キー定義
     */
    private function cryptKeyDefine()
    {
        if ($this->crypt_key_defined) return;
        $this->crypt_key_defined = true;
        $query = $this->model->newQuery();
        $aes_key = env('AES_KEY_ENQUETE');
        $query->getConnection()->statement("SET @aes_key = UNHEX('{$aes_key}')");
    }

    /**
     * トランザクション
     * @param callable $callback
     * @param int $attempts
     * @return mixed
     * @throws \Throwable
     */
    public function transaction(Callable $callback, $attempts = null)
    {
        if ($attempts === null) $attempts = config('database.transaction_attempts');
        return DB::transaction($callback, $attempts);
    }

    /**
     * @param $key_col
     * @param $val_col
     * @param bool $add_blank
     * @return mixed|void
     */
    public function getChoice($key_col, $val_col, $add_blank = true)
    {
        $ary = array_column($this->getDbResult()->getPlainArray(), $val_col, $key_col);
        if ($add_blank) $ary = ['' => ' '] + $ary;
        return $ary;
    }

    /**
     * Get data to Array
     * @param array 
     */
    public function getData($column = '')
    {
        $query = $this->getQuery();
        if ($column != '') {
            return $query->select($column)->get()->toArray();
        }
        return $query->get()->toArray();
    }
}