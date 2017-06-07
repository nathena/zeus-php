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
    protected $id;

    private $idFiled;//uuid key
    private $update_data = [];//更新未来提交的数据

    public function __construct($data, $idFiled = 'id')
    {

        if (!empty($data) && is_array($data)) {
            $this->data = $data;
            if (isset($data[$idFiled])) {
                $this->id = trim($data[$idFiled]);
            }
        }
        $this->idFiled = $idFiled;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * 不允许更新id
     * @param mixed|array $data
     */
    public function setData($data)
    {
        //不允许更新id
        if (isset($data[$this->idFiled])) {
            Logger::debug(self::class . " {$this->id} setData unset {$data[$this->idFiled]} ");
            unset($data[$this->idFiled]);
        }

        $this->data = array_merge($this->data, $data);
        $this->update_data = array_merge($this->update_data, $data);
    }

    public function updateData()
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