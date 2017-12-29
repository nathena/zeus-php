<?php
/**
 * User: nathena
 * Date: 2017/9/22 0022
 * Time: 10:37
 */

if(function_exists("event_base_new")){
    echo 1;
}else{
    echo 2;
}

echo phpinfo();