<?php
namespace zeus\util;
/**
 *
 * 基础验证
 */
class Validator 
{
    //Email
    private static $Email = '/^\w+([-_\.]*\w+)*@\w+([-_\.]*\w+)*\.\w+([-_\.]*\w+)*/';
    //EmailSend
    private static $EmailSend = '/^(?:[^\u0391-\uFFE5]|[-_\.])+@(?:[^\u0391-\uFFE5]|[-_\.])+$/';
    //Emailend
    private static $Emailend = '/^\w+([-.]\w+)*\.\w+([-.]\w+)*$/';
    //Mobile
    private static $Mobile = '/^((\d{2,3})|(\d{3}\-))?1(3|5|8)\d{9}$/';
    //Phone
    private static $Phone = '/^[\d\-\+\.\(\)]{1,30}$/';
    //Url
    private static $Url = '/^(?:http:\/\/|https:\/\/|ftp:\/\/)?[A-Za-z0-9]+[/=\?%\-&_~`@[\]\':+!]*([^<>""])*$/';
    //Host
    private static $Host = '/^\w+([-.]\w+)*\.\w+([-.]\w+)*$/';
    //Currency
    private static $Currency = '/^\d+(\.\d+)?$/';
    //ip
    private static $ip = '/^(\d){1,3}\.(\d){1,3}\.(\d){1,3}\.(\d){1,3}$/';
    //English
    private static $English = '/^[A-Za-z]+$/';
    //Chinese
    private static $Chinese = '/^[\u0391-\uFFE5]+$/';
    //UnSafe
    private static $UnSafe = '/([~!@#\$%\^&\*\+\s\(\)\[\]\{\}<>\?\/\'"]+)/';
    //TextUnSafe
    private static $TextUnSafe = '/([~#\$%\^&\*\+\(\)\[\]\{\}<>\/\'"]+)/';
    //SimpleUnSafe
    private static $SimpleUnSafe = '/[\/\*\?"\'<>\|]/';
    //specialChar
    private static $specialChar = '/[\/\*\?"\'<>\|\s\-\+&!]/';
    //身份证
    private static $Code = '/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/';

    /**
     * 验证IP
     *
     * @param String $ip
     * @return boolean
     */
    public static function is_ip($ip) {
        if (preg_match(self::$ip, trim($ip))) {
            $ipArr = explode('.', $ip);
            foreach ($ipArr as $_ip) {
                if ($_ip > 255) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 验证email
     *
     * @param String $email
     * @return boolean
     */
    public static function is_email($email) {
        if (preg_match(self::$Email, trim($email))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否发件人
     *
     * @param String $email_send
     * @return boolean
     */
    public static function is_email_send($email_send) {
        if (preg_match(self::$EmailSend, trim($email_send))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否邮箱后缀
     *
     * @param String $email_end
     * @return boolean
     */
    public static function is_email_end($email_end) {
        if (preg_match(self::$Emailend, trim($email_end))) {
            return true;
        }
        return false;
    }

    /**
     * 验证手机
     *
     * @param String $mobile
     * @return boolean
     */
    public static function is_mobile($mobile) {
        if (preg_match(self::$Mobile, trim($mobile))) {
            return true;
        }
        return false;
    }

    /**
     * 验证电话
     *
     * @param String $phone
     * @return boolean
     */
    public static function is_phone($phone) {
        if (preg_match(self::$Phone, trim($phone))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否uri
     *
     * @param String $uri
     * @return boolean
     */
    public static function is_uri($uri) {
        if (preg_match(self::$Url, trim($uri))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否主机
     *
     * @param String $host
     * @return boolean
     */
    public static function is_host($host) {
        if (preg_match(self::$Host, trim($host))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否是货币
     *
     * @param String $currency
     * @return boolean
     */
    public static function is_currency($currency) {
        if (preg_match(self::$Currency, trim($currency))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否是西文
     *
     * @param String $english
     * @return boolean
     */
    public static function is_english($english) {
        if (preg_match(self::$English, trim($english))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否中文
     *
     * @param String $chinese
     * @return boolean
     */
    public static function is_chinese($chinese) {
        if (preg_match(self::$Chinese, trim($chinese))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否不安全字符
     *
     * @param String $text
     * @return boolean
     */
    public static function is_unSafe($text) {
        if (preg_match(self::$UnSafe, trim($text))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否不安全字符
     *
     * @param String $text
     * @return boolean
     */
    public static function is_TextUnSafe($text) {
        if (preg_match(self::$TextUnSafe, trim($text))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否不安全字符
     *
     * @param String $text
     * @return boolean
     */
    public static function is_SimpleUnSafe($text) {
        if (preg_match(self::$SimpleUnSafe, trim($text))) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否不安全字符
     *
     * @param String $text
     * @return boolean
     */
    public static function is_specialChar($text) {
        if (preg_match(self::$specialChar, trim($text))) {
            return true;
        }
        return false;
    }

    /**
     * 获取密码强度
     *
     * @abstract
     * 0非法  1长度不对  2弱 3中 4强  -1为空密码
     * @param String $passwd
     * @return int
     */
    public static function getCipherStrength($passwd) {
        //0非法 1长 2弱 3中 4强 -1为空
        //双字节
        $passwd = trim($passwd);
        $cc = '/[\u0391-\uFFE5]/';
        if (self::is_specialChar(passwd) || preg_match($cc, passwd)) {
            //特殊字符或者空格
            return 0;
        }

        $len = mb_strlen($passwd, 'UTF-8');
        if ($len == 0) {
            return -1;
        }

        if ($len < 6 || $len > 36) {
            //密码长度6-30
            return 1;
        }

        //全部小写字母
        $lw = '/^[a-z]+$/';
        //全部大写字母
        $uw = '/^[A-Z]+$/';
        //全部为数字
        $n = '/^\d+$/';
        //全部为特殊字符
        $sw = '/^[~#\$%\^\(\)\[\]\{\},:;\.`_=@]+$/';
        if (preg_match($lw, passwd)) {
            return 2;
        }
        if (preg_match($uw, passwd)) {
            return 2;
        }
        if (preg_match($n, passwd)) {
            return 2;
        }
        if (preg_match($sw, passwd)) {
            return 2;
        }

        //强 `~@#$%^()_=;:[]{},.    \/*?"'<>|-+&!
        $s = '/[~#\$%\^\(\)\[\]\{\},:;\.`_=@]/';
        $ss = '/[a-z]/';
        $uss = '/[A-Z]/';
        $sss = '/\d/';
        if (preg_match($s, passwd) && preg_match($ss, passwd)
                && preg_match($uss, passwd) && preg_match($sss, passwd)) {
            return 4;
        }
        //中
        return 3;
    }
    
    /**
     * 验证是否身份证号
     *
     * @param String $text
     * @return boolean
     */
    public static function is_Code($text) {
    	if (preg_match(self::$Code, trim($text))) {
    		return true;
    	}
    	return false;
    }

}

?>