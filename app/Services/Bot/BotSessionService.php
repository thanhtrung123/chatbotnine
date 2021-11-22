<?php

namespace App\Services\Bot;

use Illuminate\Contracts\Session\Session;

/**
 * チャットボット用セッションサービス
 * Class BotSessionService
 * @package App\Services\Bot
 */
class BotSessionService
{
    /**
     * セッションキー
     */
    const SESSION_KEY = 'BOT';
    /**
     * @var Session
     */
    private $session;
    /**
     * @var array セッションデータ
     */
    private $data;

    /**
     * BotSessionService constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->sync();
    }

    /**
     * 同期
     * @return $this
     */
    public function sync()
    {
        $this->data = $this->session->get(self::SESSION_KEY);
        return $this;
    }

    /**
     * 取得
     * @param string $key キー
     * @param mixed $default 規定値
     * @return mixed|null
     */
    public function get($key = null, $default = null)
    {
        $this->sync();
        if ($key === null) return $this->data;
        else return $this->data[$key] ?? $default;
    }

    /**
     * セット
     * @param array|string $data データ
     * @param string $key キー
     * @return $this
     */
    public function set($data, $key = null)
    {
        if ($key === null) $this->data = $data;
        else $this->data[$key] = $data;
        return $this;
    }

    /**
     * 保存
     */
    public function save()
    {
        $this->session->put(self::SESSION_KEY, $this->data);
    }

    /**
     * セッションID取得
     * @return string
     */
    public function getSessionId()
    {
        return $this->session->getId();
    }

    /**
     * 削除
     * @return $this
     */
    public function delete()
    {
        $this->session->forget(self::SESSION_KEY);
        $this->sync();
        return $this;
    }

    /**
     * 聞き返しフラグ
     * @return bool
     */
    public function isHearBack()
    {
        $this->sync();
        return (isset($this->data['hear_back_flg']) && $this->data['hear_back_flg'] == true);
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

}