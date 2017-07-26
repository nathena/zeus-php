<?php
/**
 * User: nathena
 * Date: 2017/7/12 0012
 * Time: 9:05
 */

$orgina_data = 300;
//$orgina_data = 20170712;

$pri_key = file_get_contents("./license_pri.key");
$pub_key = file_get_contents("./licence_pub.key");

openssl_private_encrypt($orgina_data,$encryptData,$pri_key);
$encryptData = bin2hex($encryptData);

echo "Encrypt : {$encryptData}\n";

openssl_public_decrypt(hex2bin($encryptData),$decryptData,$pub_key);

echo "Crypt : {$decryptData}\n";