<?php
/**
 * EventSouring。
 */
namespace zeus\domain;


use zeus\base\event\AbstractEvent;
use zeus\base\event\EventMessage;
use zeus\sandbox\ApplicationContext;

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
            ApplicationContext::currentContext()->getEventBus()->publish(new EventMessage($this,$event));
        }
    }

    protected function raise(AbstractEvent $domainEvent)
    {
        $this->domainEvents[] = $domainEvent;
    }


}
