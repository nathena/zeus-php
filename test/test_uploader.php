<?php
namespace test_uploader;
/**
 * User: nathena
 * Date: 2017/6/8 0008
 * Time: 14:33
 */
use zeus\utils\Uploader;

include_once "bootstrap.php";

$header = Uploader::get_file_header(CURRENT_DIR.DS."1.png");
print_r($header);
