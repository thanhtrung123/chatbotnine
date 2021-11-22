<?php

namespace App\Services\Admin;

use App\Services\RepositoryServiceInterface;
use App\Repositories\EnqueteAnswer\EnqueteAnswerRepositoryInterface;

/**
 * アンケート回答管理サービス
 * Class EnqueteService
 * @package App\Services\Admin
 */
class EnqueteService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;

    /**
     * @var EnqueteAnswerRepositoryInterface
     */
    private $repository;

    /**
     * EnqueteService constructor.
     * @param EnqueteAnswerRepositoryInterface $repository
     */
    public function __construct(EnqueteAnswerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * アンケートの項目のIDをラベルに変更し、必要な情報を追加する
     * @param collection $collection
     * @param string $form_id
     * @return collection
     */
    public function getItemsToLabel($collection, $form_id)
    {
        $form_setting = $this->getFormSettings($form_id);
        foreach ($collection->toArray() as $item) {
            $this->getItemToLabel($item, $form_setting);
        }
        return $collection;
    }

    /**
     * アンケートの項目のIDをラベルに変更し、必要な情報を追加する（1行）
     * @param $item
     * @param $form_setting
     */
    public function getItemToLabel(&$item, $form_setting)
    {
        $enq_items = $form_setting['items'];
        // 質問
        $item->question = $enq_items[$item->question_code]['question'];
        // 種類
        $item->type = $enq_items[$item->question_code]['type'];
        // 回答の選択肢
        if (!empty($enq_items[$item->question_code]['items'])) {
            $answer_ary = [];
            foreach (explode(',', $item->answer) as $answer) {
                if (!empty($enq_items[$item->question_code]['items'][$answer])) {
                    $answer_ary[] = $enq_items[$item->question_code]['items'][$answer];
                }
            }
            $item->answer = implode(',', $answer_ary);
        }
    }

    /**
     * アンケートの項目を一定の形式に整形する
     * @param string $form_id
     * @return array $form_settings
     */
    public function getFormSettings($form_id)
    {
        $form_settings = config('enquete.form.' . $form_id);
        foreach ($form_settings['items'] as $q_cd => $items) {
            if (isset($items['items'])) {
                $new_items = [];
                foreach ($items['items'] as $item_cd => $item) {
                    if (is_array($item)) {
                        $new_items[$item['id']] = $item['name'];
                    } else {
                        $new_items[$item_cd] = $item;
                    }
                }
                $form_settings['items'][$q_cd]['items'] = $new_items;
            }
        }
        return $form_settings;
    }

    /**
     * リポジトリ取得
     * @return EnqueteAnswerRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

}