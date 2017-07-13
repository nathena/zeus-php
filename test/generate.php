<?php
/**
 * User: nathena
 * Date: 2017/7/11 0011
 * Time: 16:53
 */
$config = array(
    "config" => 'openssl.cnf',
    "private_key_bits" => 4096,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

//生成证书
$res = openssl_pkey_new($config);
openssl_pkey_export($res,$privateKey,null,$config);
$publicKey = openssl_pkey_get_details($res);
$publicKey = $publicKey["key"];

file_put_contents("./license_pri.key",$privateKey);
file_put_contents("./licence_pub.key",$publicKey);
