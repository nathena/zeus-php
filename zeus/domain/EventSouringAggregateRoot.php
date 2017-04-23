<?php

namespace zeus\domain;

use zeus\event\DomainEvent;

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

    protected function raise(DomainEvent $domainEvent)
    {
        $this->domainEvents[] = $domainEvent;
    }
}
