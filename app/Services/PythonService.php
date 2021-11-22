<?php


namespace App\Services;

/**
 * MEMO:未使用
 * Pythonサービス
 * Class PythonService
 * @package App\Services
 */
class PythonService
{
    /**
     * @var array 出力
     */
    private $output;
    /**
     * @var integer 結果
     */
    private $ret_val;
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $py_cmd;
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $work_dir;

    /**
     * PythonService constructor.
     */
    public function __construct()
    {
        $this->py_cmd = config('app.python.command');
        $this->work_dir = config('app.python.directory');
    }

    /**
     * 実行
     * @param string $cmd コマンド
     * @return string
     */
    public function exec($cmd)
    {
        chdir($this->work_dir);
        return exec("{$this->py_cmd} {$cmd}", $this->output, $this->ret_val);
    }

    /**
     * 作業ディレクトリ取得
     * @return mixed
     */
    public function getWorkDir()
    {
        return $this->work_dir;
    }

    /**
     * 作業ディレクトリセット
     * @param mixed $work_dir
     * @return $this
     */
    public function setWorkDir($work_dir)
    {
        $this->work_dir = $work_dir;
        return $this;
    }

    /**
     * Pythonコマンドセット
     * @param \Illuminate\Config\Repository|mixed $py_cmd
     * @return $this
     */
    public function setPyCmd($py_cmd)
    {
        $this->py_cmd = $py_cmd;
        return $this;
    }

    /**
     * 結果取得
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * 返却値取得
     * @return mixed
     */
    public function getRetVal()
    {
        return $this->ret_val;
    }

}