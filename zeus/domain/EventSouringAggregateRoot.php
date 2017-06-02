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


    public function commit(){
	    //TODO
        $events = $this->getEvents();
        foreach ($events as $event){
            $this->publishMessage($event);
        }
    }

    protected function raise(AbstractEvent $domainEvent)
    {
        $this->domainEvents[] = $domainEvent;
    }


}
