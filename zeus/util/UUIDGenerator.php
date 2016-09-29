<?php
namespace zeus\util;

class UUIDGenerator
{
	public static function numberNo($type='',$length=2)
	{
		$no = $type.date("Ymdhis");
		list($millisecond, $sec) = explode(" ", microtime());
		$millisecond = sprintf("%03d",$millisecond*1000);
	
		return $no.$millisecond.self::randNumber($length);
	}
	
	public static function charNo($type='',$length=2)
	{
		$no = $type.date("Ymdhis");
		list($millisecond, $sec) = explode(" ", microtime());
		$millisecond = sprintf("%03d",$millisecond*1000);
	
		return $no.$millisecond.self::randChar($length);
	}
	
	/**
	 * 随机安全字符
	 * @param number $length
	 * @return string
	 */
	public static function randChar($length=2)
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
	public static function randNumber($length=2)
	{
		$str = "";
		$strPol = "0123456789";
		$max = strlen($strPol)-1;
	
		for($i=0;$i<$length;$i++)
		{
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}
		return $str;
	}
}