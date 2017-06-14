<?php

namespace zeus\domain;

use zeus\base\AbstractComponent;
use zeus\base\logger\Logger;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 19:25
 */
abstract class AbstractEntity extends AbstractComponent
{
    protected $data = [];
    protected $update_data = [];//更新未来提交的数据

    private $idFiled;//uuid key

    public function __construct($data, $idFiled = 'id')
    {
        $this->idFiled = $idFiled;
        if (is_array($data)) {
            if(isset($data[$idFiled])){
                $this->setId($data[$idFiled]);
            }
            $this->setData($data);
        }
    }

    public function getIdFiled()
    {
        return $this->idFiled;
    }

    public function getId()
    {
        return $this->data[$this->idFiled];
    }

    public function setId($id)
    {
        $this->data[$this->idFiled] = trim($id);
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * 不允许更新id
     * 损失一点性能，提高代码复用性。见__set
     * @param mixed|array $data
     */
    public function setData($data)
    {
        if(is_array($data))
        {
            foreach($data as $key => $val)
            {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * 获取未更新信息
     * @return array
     */
    public function getUpdatedData()
    {
        $data = $this->update_data;
        $this->update_data = [];

        return $data;
    }

    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return '';
    }

    public function __set($key, $val)
    {
        //不允许更新id
        if ($key == $this->idFiled) {
            return;
        }

        $this->data[$key] = $val;
        $this->update_data[$key] = $val;
    }
}