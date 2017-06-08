<?php
/**
 * User: nathena
 * Date: 2017/6/8 0008
 * Time: 15:53
 */

namespace zeus\utils;

class Downloader
{
    private $download_file;
    private $data;

    public function __construct($download_file, $data = '')
    {
        $this->download_file = trim($download_file);
        $this->data = $data;
    }

    public function send()
    {
        ob_clean();

        //通用头
        header('Content-type: application/octet-stream');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Transfer-Encoding: binary");

        if (!empty($this->data)) {
            header('Content-Disposition: attachment; filename="' . $this->download_file . '"');
            echo $this->data;
            return;
        }
        if (!$this->check_file()) {
            return;
        }

        header('Content-Disposition: attachment; filename="' . basename($this->download_file) . '"');
        echo file_get_contents($this->download_file);
    }

    public function ngx_send()
    {
        ob_clean();

        if (!empty($this->data)) {
            return;
        }
        if (!$this->check_file()) {
            return;
        }

        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($this->download_file) . '"');
        //让Xsendfile发送文件
        header('X-Accel-Redirect: ' . $this->download_file);
    }

    protected function check_file()
    {
        $file = $this->download_file;
        if (stripos($file, ".") === false || stripos($file, "..") >= 0) {
            return false;
        }
        return true;
    }
}