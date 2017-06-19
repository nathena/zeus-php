<?php
namespace zeus\domain;

abstract class AggregateRoot extends AbstractEntity
{
    public function __construct($data=null)
    {
        parent::__construct($data);
    }
}
