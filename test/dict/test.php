<?php
/**
 * User: nathena
 * Date: 2017/12/29 0029
 * Time: 17:55
 */


class trieTree
{
    /**
     * 替换码
     * @var string
     */
    private $replaceCode = '萌';
    /**
     * 敏感词库集合
     * @var array
     */
    private $trieTreeMap = [];
    /*
     * 要开始过滤的字符串
     * @var string
     * */
    private $beginString;
    /*
     * 要开始过滤的字符串
     * @var string
     * */
    public $endString;
    /**
     * 干扰因子集合
     * @var array
     */
    private $disturbList = ['&', '*', '#','？', '！', '￥', '（', '）', '：', '‘', '’', '“', '”', '《', '》', '，', '…', '。', '、', 'nbsp', '】', '【', '～'];

    private $wordsList;
    public function __construct($wordsList,$txt = '')
    {
        $this->wordsList = $wordsList;
        $this->beginString = $txt;
        $this->addWords();
        //替换过后的文字显示
        $this->endString = $this->filter();
    }

    public function test()
    {
        return $this->endString;
    }

    /*
     * 添加铭感词
     * 获取敏感词列表(数组)
     * 敏感词的存储方法：
     * 1：存储在txt文件中（一般的方法）
     * 2：存储在缓存（比较好的方法）
     * 我是存储在memcachd中。
     * */
    private function addWords()
    {
        //加载敏感词库
        $wordsList = $this->wordsList;
        foreach ($wordsList as $words) {
            $nowWords = &$this->trieTreeMap;
            $len = mb_strlen($words);
            for ($i = 0; $i < $len; $i++) {
                $word = mb_substr($words, $i, 1);
                if (!isset($nowWords[$word])) {
                    $nowWords[$word] = false;
                }
                $nowWords = &$nowWords[$word];
                // var_dump($nowWords);
                //print_r($this->trieTreeMap);
            }
        }
    }
    /**
     * 查找对应敏感词
     * @return array
     */
    protected function search($hasReplace=false, &$replaceCodeList = array())
    {
        $wordsList = array();
        $txtLength = mb_strlen($this->beginString);
        for ($i = 0; $i < $txtLength; $i++) {
            $wordLength = $this->checkWord($i, $txtLength);
            if ($wordLength > 0) {
                $words = mb_substr($this->beginString, $i, $wordLength);
                $wordsList[] = $words;
                $hasReplace && $replaceCodeList[] = str_repeat($this->replaceCode, mb_strlen($words));
                $i += $wordLength - 1;
            }
        }
        return $wordsList;
    }
    /**
     * 过滤敏感词
     * @return mixed
     */
    public function filter()
    {
        $replaceCodeList = array();
        $wordsList = $this->search(true, $replaceCodeList);
        if (empty($wordsList)) {
            return $this->beginString;
        }
        return str_replace($wordsList, $replaceCodeList, $this->beginString);
    }
    /**
     * 敏感词检测
     * @param $beginIndex
     * @param $length
     * @return int
     */
    private function checkWord($beginIndex, $length)
    {
        $flag = false;
        $wordLength = 0;
        $trieTree = &$this->trieTreeMap;
        for ($i = $beginIndex; $i < $length; $i++) {
            $word = mb_substr($this->beginString, $i, 1);
            if ($this->checkDisturb($word)) {
                $wordLength++;
                continue;
            }
            if (!isset($trieTree[$word])) {
                break;
            }
            $wordLength++;
            if ($trieTree[$word] !== false) {
                $trieTree = &$trieTree[$word];
            } else {
                $flag = true;
            }
        }
        $flag || $wordLength = 0;
        return $wordLength;
    }
    /**
     * 干扰因子检测
     * @param $word
     * @return bool
     */
    private function checkDisturb($word)
    {
        return in_array($word, $this->disturbList);
    }
}

class TrieWord
{
    /**
     * 替换码
     * @var string
     */
    private $replaceCode = '*';
    /**
     * 敏感词库集合
     * @var array
     */
    private $trieTreeMap = [];
    /*
     * 要开始过滤的字符串
     * @var string
     * */
    private $beginString;
    /*
     * 要开始过滤的字符串
     * @var string
     * */
    public $endString;
    /**
     * 干扰因子集合
     * @var array
     */
    private $disturbList = ['&', '*', '#','？', '！', '￥', '（', '）', '：', '‘', '’', '“', '”', '《', '》', '，', '…', '。', '、', 'nbsp', '】', '【', '～'];

    private $wordsList;

    private static $instance;

    public static function getInstance($wordsList)
    {
        if(!isset(self::$instance)){
            self::$instance = new self($wordsList);
        }
        return self::$instance;
    }



    public function transform($txt)
    {
        $this->beginString = $txt;
        //替换过后的文字显示
        $this->endString = $this->filter();

        return $this->endString;
    }

    public function test()
    {
        echo $this->endString;
    }

    /*
     * 添加铭感词
     * 获取敏感词列表(数组)
     * 敏感词的存储方法：
     * 1：存储在txt文件中（一般的方法）
     * 2：存储在缓存（比较好的方法）
     * 我是存储在memcachd中。
     * */
    private function addWords()
    {
        //加载敏感词库
        $wordsList = $this->wordsList;
        foreach ($wordsList as $words) {
            $nowWords = &$this->trieTreeMap;
            $len = mb_strlen($words);
            for ($i = 0; $i < $len; $i++) {
                $word = mb_substr($words, $i, 1);
                if (!isset($nowWords[$word])) {
                    $nowWords[$word] = false;
                }
                $nowWords = &$nowWords[$word];
                // var_dump($nowWords);
                //print_r($this->trieTreeMap);
            }
        }
    }
    /**
     * 查找对应敏感词
     * @return array
     */
    protected function search($hasReplace=false, &$replaceCodeList = array())
    {
        $wordsList = array();
        $txtLength = mb_strlen($this->beginString);
        for ($i = 0; $i < $txtLength; $i++) {
            $wordLength = $this->checkWord($i, $txtLength);
            if ($wordLength > 0) {
                $words = mb_substr($this->beginString, $i, $wordLength);
                $wordsList[] = $words;
                $hasReplace && $replaceCodeList[] = str_repeat($this->replaceCode, mb_strlen($words));
                $i += $wordLength - 1;
            }
        }
        return $wordsList;
    }
    /**
     * 过滤敏感词
     * @return mixed
     */
    public function filter()
    {
        $replaceCodeList = array();
        $wordsList = $this->search(true, $replaceCodeList);
        if (empty($wordsList)) {
            return $this->beginString;
        }
        return str_replace($wordsList, $replaceCodeList, $this->beginString);
    }
    /**
     * 敏感词检测
     * @param $beginIndex
     * @param $length
     * @return int
     */
    private function checkWord($beginIndex, $length)
    {
        $flag = false;
        $wordLength = 0;
        $trieTree = &$this->trieTreeMap;
        for ($i = $beginIndex; $i < $length; $i++) {
            $word = mb_substr($this->beginString, $i, 1);
            if ($this->checkDisturb($word)) {
                $wordLength++;
                continue;
            }
            if (!isset($trieTree[$word])) {
                break;
            }
            $wordLength++;
            if ($trieTree[$word] !== false) {
                $trieTree = &$trieTree[$word];
            } else {
                $flag = true;
            }
        }
        $flag || $wordLength = 0;
        return $wordLength;
    }

    /**
     * 干扰因子检测
     * @param $word
     * @return bool
     */
    private function checkDisturb($word)
    {
        return in_array($word, $this->disturbList);
    }

    private function __construct($wordsList)
    {
        $this->wordsList = $wordsList;
        $this->addWords();
    }
}


$trieData = file_get_contents("trie.dat");
$trieData = explode(",",$trieData);
$transform = TrieWord::getInstance($trieData);
echo $transform->transform("仓井真善插逼忍空辱我中华骚aaa法轮功包二奶");
$test = new trieTree($trieData,"仓井真善插逼忍空辱我中华骚aaa法轮功包二奶");
$test->test();