<?php


namespace App\Services\File;

/**
 * CSVサービス
 * Class CsvService
 * @package App\Services\File
 */
class CsvService
{
    /**
     * 書き込みリトライ回数
     */
    const WRITE_RETRY = 5;
    /**
     * @var array CSVデータ
     */
    protected $_data_ary = array();
    /**
     * @var array ヘッダー
     */
    protected $_header;
    /**
     * @var array エスケープ
     */
    protected $_escape;
    /**
     * @var string 区切り文字
     */
    protected $_split;
    /**
     * @var string 改行文字
     */
    protected $_newline;
    /**
     * @var bool ヘッダー使用
     */
    protected $_use_header;
    /**
     * @var bool フリーCSV
     */
    protected $_use_free = false;
    /**
     * @var string 文字コード
     */
    protected $_char_code = "UTF-8";
    /**
     * @var string ラップ文字
     */
    protected $_wrap = '"';
    /**
     * @var bool 自動ラップ
     */
    protected $_use_auto_wrap = true;

    /**
     * CSV用ライブラリ
     * @param array $header :ヘッダー
     * @param array $esc_setting :エスケープ設定
     * @param string $split :区切り文字
     * @param string $new_line :改行文字
     */
    public function __construct(array $header = array(), array $esc_setting = array(), $split = ',', $new_line = "\n")
    {
        if (empty($header)) {
            $this->_use_header = false;
        } else {
            $this->_header = $header;
            $this->_use_header = true;
        }
        $this->_split = $split;
        $this->_newline = $new_line;
        if (empty($esc_setting)) {
            $esc_setting["\""] = "\"\"";
        }
        $this->_escape = $esc_setting;
    }

    /**
     * ヘッダーセット(freeCSVでは使用不可)
     * @param array $header ヘッダー
     * @return $this
     */
    public function setHeader(array $header)
    {
        $this->_header = array();
        if (array_values($header) === $header) {
            foreach ($header as $col) {
                $this->_header[$col] = $col;
            }
        } else {
            $this->_header = $header;
        }
        $this->_use_header = true;
        $this->_use_free = false;
        return $this;
    }

    /**
     * ヘッダー使用フラグ
     * @param bool $flag
     * @return $this
     */
    public function setUseHeader($flag)
    {
        $this->_use_header = $flag;
        return $this;
    }

    /**
     * free CSV 使用（データ入力前に呼ぶこと)
     * @return $this
     */
    public function useFreeCsv()
    {
        $this->_use_free = true;
        return $this;
    }

    /**
     * 文字コードセット
     * @param string $char_code
     * @return $this
     */
    public function setCharCode(string $char_code)
    {
        $this->_char_code = $char_code;
        return $this;
    }

    /**
     * スプリッタセット
     * @param string $split
     * @return $this
     */
    public function setSplit(string $split)
    {
        $this->_split = $split;
        return $this;
    }

    /**
     * 改行セット
     * @param string $newline
     * @return $this
     */
    public function setNewline(string $newline)
    {
        $this->_newline = $newline;
        return $this;
    }

    /**
     * 自動エスケープ
     * @param bool $use_auto_wrap
     * @return $this
     */
    public function setUseAutoWrap(bool $use_auto_wrap)
    {
        $this->_use_auto_wrap = $use_auto_wrap;
        return $this;
    }

    /**
     * 行追加
     * @param array $row
     * @return $this
     */
    public function addRow($row)
    {
        //free modeではそのまま入れる
        if ($this->_use_free) {
            $this->_data_ary[] = $row;
            return $this;
        }
        //通常時はヘッダーを考慮する
        $data = array();
        if (empty($this->_header)) {
            foreach ($row as $key => $val) {
                $this->_header[$key] = $key;
                $data[$key] = $val;
            }
        } else {
            foreach ($this->_header as $key => $val) {
                if (array_key_exists($key, $row)) {
                    $data[$key] = $row[$key];
                }
            }
        }
        $this->_data_ary[] = $data;
        return $this;
    }

    /**
     * データ追加（2次元配列）
     * @param array $data_ary
     * @return $this
     */
    public function addDataArray(array $data_ary)
    {
        foreach ($data_ary as $row) {
            $this->addRow($row);
        }
        return $this;
    }

    /**
     * データのクリア
     * @param $header
     * @return $this
     */
    public function clear($header = false)
    {
        $this->_data_ary = array();
        if ($header) {
            $this->_header = array();
        }
        return $this;
    }

    /**
     * CSVファイル追記
     * @param string $file_path :ファイルパス
     * @param string $char_code :文字コード
     * @return $this
     * @throws \Exception
     */
    public function append($file_path, $char_code = null)
    {
        return $this->output($file_path, $char_code, true);
    }

    /**
     * CSVファイル出力
     * @param string $file_path :ファイルパス
     * @param string $char_code :文字コード
     * @param bool $append : 追記
     * @return $this
     * @throws \Exception
     */
    public function output($file_path, $char_code = null, $append = false)
    {
        $file_dir = dirname($file_path);
        $exists_file = file_exists($file_path);
        $csvStr = $this->_create_csv($char_code, $append && $exists_file);
        if (!file_exists($file_dir)) mkdir($file_dir, 0777, true);
        for ($r_cnt = 1; $r_cnt <= self::WRITE_RETRY; $r_cnt++) {
            $ret = file_put_contents($file_path, $csvStr, ($append ? FILE_APPEND : 0) | LOCK_EX);
            if ($ret !== false) break;
            usleep(500 * 1000);
        }
        if ($r_cnt === self::WRITE_RETRY) {
            throw new \Exception('CSV Write Error!');
        }
        return $this;
    }

    /**
     * CSV生成文字列取得
     * @param string $char_code :文字コード
     * @return string
     */
    public function strout($char_code = null)
    {
        return $this->_create_csv($char_code);
    }

    /**
     * CSV生成
     * @param $char_code :文字コード
     * @param bool $append : 追記
     * @return bool|false|string|string[]|null
     */
    private function _create_csv($char_code = null, $append = false)
    {
        if ($char_code === null) {
            $char_code = $this->_char_code;
        }
        $csv_str = "";
        $csv_row = array();
        //ヘッダー生成
        if ($this->_use_header && !$this->_use_free && !$append) {
            foreach ($this->_header as $key => $val) {
                $csv_row[] = $val;
            }
            $csv_str .= implode($this->_split, $csv_row) . $this->_newline;
        }
        //データ生成
        foreach ($this->_data_ary as $row) {
            $csv_row = array();
            if ($this->_use_free) {
                //freeモード
                if (!is_array($row)) $row = array($row);
                foreach ($row as $val) {
                    $csv_row[] = $this->_csv_write($val);
                }
            } else {
                //通常モード
                foreach ($this->_header as $key => $val) {
                    if (array_key_exists($key, $row)) {
                        $csv_row[] = $this->_csv_write($row[$key]);
                    } else {
                        $csv_row[] = '';
                    }
                }
            }
            $csv_str .= implode($this->_split, $csv_row) . $this->_newline;
        }

        if ($char_code != 'UTF-8BOM') {
            $encode = mb_detect_encoding($csv_str, "ASCII,JIS,UTF-8,SJIS-WIN,SJIS,EUCJP-WIN,EUC_JP");
            if (strtoupper($char_code) != $encode && $encode != 'UTF-8BOM') $csv_str = mb_convert_encoding($csv_str, $char_code, $encode);
        } else {
            // UTF-8 BOM付の場合
            $tmp_csv = $csv_str;
            $csv_str = pack('C*', 0xEF, 0xBB, 0xBF);
            $csv_str .= $tmp_csv;
        }
        return $csv_str;
    }

    /**
     * CSVデータ用
     * @param string $p_str
     * @return mixed|string
     */
    private function _csv_write($p_str)
    {
        $prev_p_str = $p_str;
        foreach ($this->_escape as $esc_char => $search_char) {
            $p_str = str_replace($esc_char, $search_char, $p_str);
        }
        if ($this->_use_auto_wrap) {
            if ($p_str != $prev_p_str || preg_match('/\n/', $p_str) || strpos($p_str, $this->_split) !== false)
                $p_str = $this->_wrap . $p_str . $this->_wrap;
        } else {
            $p_str = $this->_wrap . $p_str . $this->_wrap;
        }
        return $p_str;
    }

}