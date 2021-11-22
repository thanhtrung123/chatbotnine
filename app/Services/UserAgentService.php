<?php

namespace App\Services;

use Constant;

/**
 * ユーザーエージェントサービス
 * Class UserAgentService
 * @package App\Services
 */
class UserAgentService
{
    /**
     * @var string UA
     */
    private $user_agent;
    /**
     * @var string ブラウザID
     */
    private $browser_id;
    /**
     * @var string ブラウザ名
     */
    private $browser_name;
    /**
     * @var string ブラウザバージョン
     */
    private $browser_version;
    /**
     * @var string OS ID
     */
    private $os_id;
    /**
     * @var string OS名
     */
    private $os_name;
    /**
     * @var string OSバージョン
     */
    private $os_version;

    /**
     * ユーザーエージェント解析実行
     * @return $this
     */
    public function analyzeUserAgent()
    {
        //ブラウザ解析
        $browser_config = config('useragent.browser');
        list($this->browser_id, $this->browser_version) = $this->analyze($browser_config);
        $this->browser_version = str_replace('_', '.', $this->browser_version);
        if (empty($this->browser_id)) $this->browser_id = config('const.useragent.browser.other.id');
        $this->browser_name = Constant::getConstName($this->browser_id, 'useragent.browser');
        //OS解析
        $os_config = config('useragent.os');
        list($this->os_id, $this->os_version) = $this->analyze($os_config);
        $this->os_version = str_replace('_', '.', $this->os_version);
        if (empty($this->os_id)) $this->os_id = config('const.useragent.os.other.id');
        $this->os_name = Constant::getConstName($this->os_id, 'useragent.os');
        return $this;
    }

    /**
     * 解析処理
     * @param array $config UA設定
     * @return array
     */
    private function analyze($config)
    {
        $version = null;
        $id = null;
        foreach ($config as $row) {
            if (!preg_match($row['match'], $this->user_agent)) continue;
            if (isset($row['version'])) {
                list($version_regexp, $version_pos) = $row['version'];
                if (preg_match($version_regexp, $this->user_agent, $match)) {
                    $version = $match[$version_pos] ?? null;
                }
            }
            $id = $row['id'];
            break;
        }
        return [$id, $version];
    }

    /**
     * UA取得
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * UAセット
     * @param mixed $user_agent
     * @return $this
     */
    public function setUserAgent($user_agent)
    {
        $this->user_agent = $user_agent;
        return $this;
    }

    /**
     * ブラウザID取得
     * @return mixed
     */
    public function getBrowserId()
    {
        return $this->browser_id;
    }

    /**
     * ブラウザバージョン取得
     * @return mixed
     */
    public function getBrowserVersion()
    {
        return $this->browser_version;
    }

    /**
     * OS ID 取得
     * @return mixed
     */
    public function getOsId()
    {
        return $this->os_id;
    }

    /**
     * OSバージョン取得
     * @return mixed
     */
    public function getOsVersion()
    {
        return $this->os_version;
    }

    /**
     * ブラウザ名取得
     * @return mixed
     */
    public function getBrowserName()
    {
        return $this->browser_name;
    }

    /**
     * OS名取得
     * @return mixed
     */
    public function getOsName()
    {
        return $this->os_name;
    }

}