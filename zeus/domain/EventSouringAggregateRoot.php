<?php
/**
 * EventSouring 需要事件需要支持，目前暂不支持。
 */
namespace zeus\domain;

use zeus\event\EventObject;

abstract class EventSouringAggregateRoot extends AggregateRoot
{
	private $domainEvents = [];
	
	protected function __construct($data,$idFiled='id'){
		parent::__construct($data,$idFiled);
	}
	
    public function getEvents()
    {
        $domainEvents       = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    protected function raise(EventObject $domainEvent)
    {
        $this->domainEvents[] = $domainEvent;
    }
}
