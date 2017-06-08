<?php
/**
 * User: nathena
 * Date: 2017/6/8 0008
 * Time: 14:08
 */

namespace zeus\utils;


class Uploader
{
    /**
     * 文件头部信息，十六进制信息，取前4位
     * JPEG (jpg)，文件头：FFD8FFe1
     * PNG (png)，文件头：89504E47
     * GIF (gif)，文件头：47494638
     * TIFF (tif)，文件头：49492A00
     * Windows Bitmap (bmp)，文件头：424D
     * CAD (dwg)，文件头：41433130
     * Adobe Photoshop (psd)，文件头：38425053
     * Rich Text Format (rtf)，文件头：7B5C727466
     * XML (xml)，文件头：3C3F786D6C HTML
     * (html)，文件头：68746D6C3E
     * Email [thorough only]  (eml)，文件头：44656C69766572792D646174653A
     * Outlook Express (dbx)，文件头：CFAD12FEC5FD746F
     * Outlook (pst)，文件头：2142444E
     * MS Word/Excel (xls.or.doc)，文件头：D0CF11E0
     * MS Access (mdb)，文件头：5374616E64617264204A
     * WordPerfect (wpd)，文件头：FF575043
     * Postscript (eps.or.ps)，文件头：252150532D41646F6265
     * Adobe Acrobat (pdf)，文件头：255044462D312E
     * Quicken (qdf)，文件头：AC9EBD8F
     * Windows Password (pwl)，文件头：E3828596
     * ZIP Archive (zip)，文件头：504B0304
     * RAR Archive (rar)，文件头：52617221
     * Wave (wav)，文件头：57415645
     * AVI (avi)，文件头：41564920
     * Real Audio (ram)，文件头：2E7261FD
     * Real Media (rm)，文件头：2E524D46
     * MPEG (mpg)，文件头：000001BA
     * MPEG (mpg)，文件头：000001B3
     * Quicktime (mov)，文件头：6D6F6F76
     * Windows Media (asf)，文件头：3026B2758E66CF11
     * MIDI (mid)，文件头：4D546864
     * MP4 (mp4)，文件头：文件头：00000020667479706d70
     */

    //取前4位,十六进制
    private static $file_hex_headers = [
        'jpg' => ['FFD8FFe1', 'FFD8FFE0'],
        'jpeg' => 'FFD8FFe1',
        'gif' => '47494638',
        'png' => '89504E47',
        'tif' => '49492A00',
        'bmp' => '424D',
        'dwg' => '41433130',
        'psd' => '38425053',
        'rtf' => '7B5C727466',
        'xml' => '3C3F786D6C',
        'htm' => '68746D6C3E',
        'html' => '68746D6C3E',
        'html5' => '68746D6C3E',
        'eml' => '44656C69766572792D646174653A',
        'dbx' => 'CFAD12FEC5FD746F',
        'pst' => '2142444E',
        'xls' => 'D0CF11E0',
        'doc' => 'D0CF11E0',
        'mdb' => '5374616E64617264204A',
        'wpd' => 'FF575043',
        'eps' => '252150532D41646F6265',
        'ps' => '252150532D41646F6265',
        'pdf' => '255044462D312E',
        'qdf' => 'AC9EBD8F',
        'pwl' => 'E3828596',
        'zip' => '504B0304',
        'rar' => '52617221',
        'wav' => '57415645',
        'avi' => '41564920',
        'ram' => '2E7261FD',
        'rm' => '2E524D46',
        'mpg' => ['000001BA', '000001B3'],
        'mov' => '6D6F6F76',
        'asf' => '3026B2758E66CF11',
        'mid' => '4D546864',
        'mp4' => '00000020667479706d70',
    ];

    public static function add_file_header($ext, $bin)
    {
        $ext = strtolower($ext);
        if (!isset(self::$file_hex_headers[$ext])) {
            self::$file_hex_headers[$ext] = $bin;
        } else {
            $new_bin = [];
            $old_bin = self::$file_hex_headers[$ext];
            $new_bin = is_array($old_bin) ? array_merge($new_bin, $old_bin) : [$old_bin];
            $new_bin[] = $bin;
            self::$file_hex_headers[$ext] = $new_bin;
        }
    }

    public static function get_file_header($file)
    {
        $file_info = explode(".", $file);
        $ext = end($file_info);
        $bin = self::_get_file_header($file);

        return [$ext, $bin];
    }

    private static function _get_file_header($file)
    {
        if(!is_file($file)){
            throw new \RuntimeException("获取文件头失败");
        }
        $fh = fopen($file, "rb");
        $head = fread($fh, 4);
        fclose($fh);

        return strtoupper(bin2hex($head));
    }

    //上传错误
    private $err_arr;
    private $max_size;
    private $mime_arr;
    private $filedata;
    private $msg;
    private $processed = false;

    private $tmp_name;
    private $tmp_path;

    public function __construct($filedata, $msg = "")
    {
        $this->err_arr = [
            1 => '上传的文件超过php.ini中的upload_max_filesize选项限制的值:' . ini_get('upload_max_filesize'),
            2 => '上传的文件超过隐藏表的的MAX_FILE_SIZE指定的值',
            3 => '文件只有部分被上传',
            4 => '没有文件被上传',
            6 => '找不到临时路径',
            7 => '文件写入失败',
            8  => 'A PHP extension stopped the file upload.',
            9  => 'The uploaded file exceeds the user-defined max file size.',
            10 => 'The uploaded file is not allowed.',
            11 => 'The specified upload directory does not exist.',
            12 => 'The specified upload directory is not writable.',
            13 => 'Unexpected error.'
        ];
        //判断文件大小
        $this->max_size = 1024 * 1024 * 10;
        //判断文件扩展
        $this->mime_arr = [
            'image/gif',
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/pjpeg',
            'image/x-png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/x-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/pdf',
            'application/zip',
            'application/octet-stream',
        ];

        $this->filedata = trim($filedata);
        $this->msg = trim($msg);
    }

    public function setMaxSize($max_size)
    {
        $this->max_size = $max_size;
    }

    public function addAllowdMimeType($mime_types)
    {
        $this->mime_arr = array_merge($this->mime_arr, $mime_types);
    }

    public function getName()
    {
        $this->process();
        return $this->tmp_name;
    }

    public function getPath()
    {
        $this->process();
        return $this->tmp_path;
    }

    public function process()
    {
        if ($this->processed) {
            return;
        }
        $this->processed = true;

        $filedata = $this->filedata;
        if (!empty($_FILES[$filedata])) {
            $_file = $_FILES[$filedata];
            if (4 == $_file['error']) {
                return;
            }
            if (!empty($_file['error'])) {
                throw new \RuntimeException($this->msg . '上传失败：' . $this->err_arr[$_file['error']]);
            }

            if ($_file['size'] > $this->size) {
                throw new \RuntimeException($this->msg . '上传文件过大, 不能超过：' . intval($this->size / 1024 / 1024) . 'M');
            }
            //判断MIME类型
            if (!in_array($_file['type'], $this->mime_arr)) {
                throw new \RuntimeException($this->msg . '上传文件格式错误，只允许图片或pdf文件');
            }

            //判断是否是所上传的文件
            if (!is_uploaded_file($_file['tmp_name'])) {
                throw new \RuntimeException($this->msg . '上传出错，请稍后再试');
            }

            $tmp_name = $_file['name'];
            $tmp_path = $_file['tmp_name'];

            $tmp_name_ext = explode(".",$tmp_name);
            $this->check_file_header(end($tmp_name_ext),self::_get_file_header($tmp_path));

            $this->tmp_name = $tmp_name;
            $this->tmp_path = $tmp_path;
        }
    }

    private function check_file_header($ext,$bin)
    {
        $ext = strtolower($ext);
        $bin = strtoupper($bin);

        if(!isset(self::$file_hex_headers[$ext])){
            throw new \RuntimeException($this->msg . '文件后缀不允许');
        }

        $headers = self::$file_hex_headers[$ext];
        if(is_string($headers)){
            $headers = [$headers];
        }
        foreach($headers as $header){
            if($header == $bin){
                return;
            }
        }
        throw new \RuntimeException($this->msg . '文件格式异常');
    }
}