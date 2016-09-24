<?php
namespace zeus\util;

class UuidHelper
{
	public function numberNo($type='')
	{
		$no = $type.date("Ymdhis");
		list($millisecond, $sec) = explode(" ", microtime());
		$millisecond = sprintf("%03d",$millisecond*1000);
	
		return $no.$millisecond.$this->get_rand_number();
	}
	
	public function charNo($type='')
	{
		$no = $type.date("Ymdhis");
		list($millisecond, $sec) = explode(" ", microtime());
		$millisecond = sprintf("%03d",$millisecond*1000);
	
		return $no.$millisecond.$this->get_rand_number();
	}
	
	/**
	 * 随机安全字符
	 * @param number $length
	 * @return string
	 */
	public function randChar($length=2)
	{
		$str = "";
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;
	
		for($i=0;$i<$length;$i++)
		{
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}
		return $str;
	}
	
	//随机数字
	public function randNumber($min = 0, $max = 10, $length=2)
	{
		$num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
		$format= "%0{$length}d";
		return sprintf($format, $num*10);
	}
}