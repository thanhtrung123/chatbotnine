<?php


namespace App\Services\File;

/**
 * ZIPサービス
 * Class ZipService
 * @package App\Services\File
 */
class ZipService
{
    /**
     * 結果：失敗
     */
    const RESULT_FAIL = -1;
    /**
     * 結果：成功
     */
    const RESULT_DONE = 1;
    /**
     * 結果：無し
     */
    const RESULT_NONE = 2;

    /**
     * @var \ZipArchive
     */
    private $zip;
    /**
     * @var array ファイルパス配列
     */
    private $files = [];
    /**
     * @var integer 結果
     */
    private $result;

    /**
     * ZipService constructor.
     */
    public function __construct()
    {
        $this->zip = new \ZipArchive();
    }

    /**
     * 圧縮
     * @param string $output ZIP出力パス
     * @param bool $unlink_files 対象ファイルを消す
     * @return $this
     */
    public function compress($output, $unlink_files = true)
    {
        $this->result = self::RESULT_NONE;
        if (empty($this->files)) return $this;
        $zip_dir = dirname($output);
        if (!file_exists($zip_dir)) mkdir($zip_dir, 0777, true);
        $this->zip->open($output, \ZipArchive::CREATE);
        foreach ($this->files as $path) {
            $this->zip->addFile($path, basename($path));
        }
        $this->zip->close();
        $this->result = file_exists($output) ? self::RESULT_DONE : self::RESULT_FAIL;
        if ($unlink_files && $this->result == self::RESULT_DONE)
            foreach ($this->files as $path) unlink($path);
        return $this;
    }

    /**
     * 対象ファイルクリア
     * @return $this
     */
    public function clearFiles()
    {
        $this->files = [];
        return $this;
    }

    /**
     * 対象ファイル追加
     * @param string $file ファイルパス
     * @return $this
     */
    public function addFile($file)
    {
        $this->files[] = $file;
        return $this;
    }

    /**
     * 対象ファイルパターンセット
     * @param string $pattern globに渡すパターン
     * @param null|string $ignore_regexp 除外ファイルパス正規表現
     * @return $this
     */
    public function setPattern($pattern, $ignore_regexp = null)
    {
        foreach (glob($pattern) as $path) {
            if ($ignore_regexp !== null && preg_match($ignore_regexp, $path)) continue;
            $this->files[] = $path;
        }
        return $this;
    }

    /**
     * 対象ファイル（配列）セット
     * @param mixed $files
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * 対象ファイル（配列）取得
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * 結果取得
     * @return integer
     */
    public function getResult()
    {
        return $this->result;
    }

}