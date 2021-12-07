<?php


namespace App\Services\Bot;


use App\Repositories\EnqueteAnswer\EnqueteAnswerRepositoryInterface;
use Illuminate\Contracts\Session\Session;

/**
 * チャットボットアンケートサービス
 * Class BotEnqueteService
 * @package App\Services\Bot
 */
class BotEnqueteService
{
    /**
     * @var EnqueteAnswerRepositoryInterface
     */
    private $enquete_repository;
    /**
     * @var Session
     */
    private $session;

    /**
     * BotEnqueteService constructor.
     * @param EnqueteAnswerRepositoryInterface $enquete_repository
     * @param Session $session
     */
    public function __construct(EnqueteAnswerRepositoryInterface $enquete_repository, Session $session)
    {
        $this->enquete_repository = $enquete_repository;
        $this->session = $session;
    }

    /**
     * アンケート登録
     * @param array $params フォームパラメータ
     * @return mixed
     */
    public function createEnquete(array $params)
    {
        $this->enquete_repository->transaction(function () use ($params) {
            list($form_id, $form_setting) = $this->getFormDataFromHash($params['form_hash'], $params['key'] ?? null);
            $date = date('YmdHis');
            $questions = $params['question'];
            $post_id = $this->enquete_repository->getNextId('post_id');
            foreach ($form_setting['items'] as $q_cd => $item) {
                if (!isset($questions[$q_cd])) {
                    $questions[$q_cd] = null;
                } else if (is_array($questions[$q_cd])) {
                    $questions[$q_cd] = implode(',', $questions[$q_cd]);
                }
                $this->enquete_repository->createData($params + [
                        'form_id' => $form_id,
                        'post_id' => $post_id,
                        'question_code' => $q_cd,
                        'answer' => $questions[$q_cd],
                        'posted_at' => $date,
                    ], $item['is_crypt']);
            }
        });
        return;
    }

    /**
     * ハッシュからアンケートフォーム取得
     * @param string $form_hash アンケートフォームハッシュ
     * @param string|null $enquete_key
     * @return array
     */
    public function getFormDataFromHash($form_hash, $enquete_key = null)
    {
        $form_settings = __('enquete.form');
        $form_id = null;
        $form_setting = [];
        foreach ($form_settings as $id => $setting) {
            $hash = $this->makeFormHash($id, $enquete_key);
            if ($form_hash !== $hash) continue;
            $form_setting = $setting;
            $form_id = $id;
        }
        return [$form_id, $form_setting];
    }

    /**
     * フォームID→ハッシュ
     * @param string $form_id
     * @param string|null $enquete_key
     * @return string
     */
    public function makeFormHash($form_id, $enquete_key = null)
    {
        if (empty($enquete_key)) {
            $enquete_key = $this->session->getId();
        }
        return md5($form_id . $enquete_key);
    }

    /**
     * @param Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * アンケート回答リポジトリ取得
     * @return EnqueteAnswerRepositoryInterface
     */
    public function getEnqueteAnswerRepository(): EnqueteAnswerRepositoryInterface
    {
        return $this->enquete_repository;
    }

}