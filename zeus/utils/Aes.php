<?php
namespace zeus\utils;

class Aes
{
	private $key;
	private $iv;
	
	private $algorithm;
	private $option;
	
	public function __construct($key,$algorithm = 'AES-128-CBC',$iv=null) {
		$this->key = $key;
		$this->iv  = empty($iv) ? str_repeat(chr(0), 16) : trim($iv);
		
		$this->algorithm = $algorithm;
		$this->option    = OPENSSL_RAW_DATA;
	}
	
	public function encrypt($encrypt) {
		
		$passcrypt = openssl_encrypt($encrypt, $this->algorithm, $this->key, $this->option,$this->iv);
		
		return strtoupper(bin2hex($passcrypt));
	}
	
	public function decrypt($decrypt) {
		
		$decoded = hex2bin($decrypt);
		
		$decrypted = openssl_decrypt($decoded, $this->algorithm, $this->key, $this->option,$this->iv);
		
		return $decrypted;
	}
	
	
	public function encryptBase64($encrypt) {
	
		$passcrypt = openssl_encrypt($encrypt, $this->algorithm, $this->key, $this->option,$this->iv);
	
		return base64_encode($passcrypt);
	}
	
	public function decryptBase64($decrypt) {
	
		$decoded = base64_decode($decrypt);
	
		$decrypted = openssl_decrypt($decoded, $this->algorithm, $this->key, $this->option,$this->iv);
	
		return $decrypted;
	}
}