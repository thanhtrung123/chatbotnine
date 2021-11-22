<?php

namespace App\Repositories\KeyPhrase;

use App\Models\KeyPhrase;
use App\Repositories\AbstractRepository;
use App\Repositories\KeyPhrase\KeyPhraseRepositoryInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use App\Repositories\type;
use DB;

/**
 * キーフレーズリポジトリ
 * Class KeyPhraseRepository
 * @package App\Repositories\KeyPhrase
 */
class KeyPhraseRepository extends AbstractRepository implements KeyPhraseRepositoryInterface
{
    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return KeyPhrase::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return RepositoryInterface
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        //DT
        if (!empty($this->dt_params['keyword'])) {
            $this->byKeyword($this->dt_params['keyword'], ['word', 'replace_word', $this->model->getTable() . '.key_phrase_id']);
        }
        if (!empty($this->dt_params['disabled'])) {
            $query->whereIn('disabled', $this->dt_params['disabled']);
        }
        //Para
        if (isset($this->params['without_delete'])) {
            $query->where('disabled', '=', config('const.common.disabled.no.id'));
//            $query->where('key_phrase_status', '!=', config('const.truth.key_phrase_status.delete.id'));
        }
        if (isset($this->params['key_phrase'])) {
            $key_phrase = $this->params['key_phrase'];
            $query->where(function ($wq) use ($key_phrase) {
                $wq->where('original_word', $key_phrase)
                    ->orWhere('word', $key_phrase)
                    ->orWhere('replace_word', $key_phrase);
            });
        }
        if (isset($this->params['sys_word'])) {
            $key_phrase = $this->params['sys_word'];
            $query->where(function ($wq) use ($key_phrase) {
                $wq->where('original_word', $key_phrase)
                    ->orWhere('word', $key_phrase);
            });
        }
        if (isset($this->params['replace_words'])) {
            $query->whereIn('replace_word', $this->params['replace_words']);
        }
        return $this;
    }

    /**
     * キーフレーズ一覧用フィルタ
     * @return $this|\App\Repositories\KeyPhrase\KeyPhraseRepositoryInterface
     */
    public function filterKeyPhraseList()
    {
        $base_tbl = $this->model->getTable();
        $query = $this->getQuery();
        $query->select(["{$base_tbl}.*", DB::raw('count(tbl_truth.key_phrase_id) as cnt')])
            ->leftJoin('tbl_truth', 'tbl_truth.key_phrase_id', '=', "{$base_tbl}.key_phrase_id")
            ->groupBy("{$base_tbl}.key_phrase_id", 'word');
        return $this;
    }

    /**
     * チョイス用フィルタ
     * @return $this|\App\Repositories\KeyPhrase\KeyPhraseRepositoryInterface
     */
    public function filterChoice()
    {
        $query = $this->getQuery();
        $query->select("*", DB::raw('COALESCE(replace_word,word) as key_phrase'));
        return $this;
    }


    /**
     * キーフレーズがあればID、無ければ登録してからID　取得
     * @param $word
     * @param null $type
     * @param bool $word_prioritize
     * @return int|mixed|null
     */
    public function findOrSave($word, $type = null, $word_prioritize = false)
    {
        $id = $this->findOnly($word, $word_prioritize);
        if (empty($id)) {
            $id = $this->getNextKeyPhraseId();
            $this->query->create([
                'key_phrase_id' => $id,
                'word' => $word,
                'original_word' => $word,
                'type' => $type === null ? config('const.truth.key_phrase_type.auto.id') : $type,
            ]);
        }
        return $id;
    }

    /**
     * キーフレーズのID取得
     * @param $word
     * @param bool $word_prioritize
     * @return mixed|null
     */
    public function findOnly($word, $word_prioritize = false)
    {
        if ($word_prioritize) {
            $data = $this->findOneBy(['word' => $word]);
            if (empty($data))
                $data = $this->findOneBy(['replace_word' => $word]);
        } else {
            $data = $this->findOneBy(['replace_word' => $word]);
            if (empty($data))
                $data = $this->findOneBy(['word' => $word]);
        }
        if (empty($data))
            $data = $this->findOneBy(['original_word' => $word]);
        return $data['key_phrase_id'] ?? null;
    }

    /**
     * 次のキーフレーズID取得
     * @return int
     */
    public function getNextKeyPhraseId()
    {
        return parent::getNextId('key_phrase_id');
    }

}