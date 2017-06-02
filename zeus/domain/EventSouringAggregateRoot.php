<?php
/**
 * EventSouring。
 */
namespace zeus\domain;

use zeus\base\AbstractEvent;

abstract class EventSouringAggregateRoot extends AggregateRoot
{
	private $domainEvents = [];

	public function __construct($data, $idFiled = 'id')
    {
        parent::__construct($data, $idFiled);
    }

    public function getEvents()
    {
        $domainEvents       = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    protected function raise(AbstractEvent $domainEvent)
    {
        $this->domainEvents[] = $domainEvent;
    }
}
