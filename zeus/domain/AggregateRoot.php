<?php
namespace zeus\domain;

abstract class AggregateRoot extends AbstractEntity
{
    public function __construct($data, $idFiled = 'id')
    {
        parent::__construct($data, $idFiled);
    }
}
