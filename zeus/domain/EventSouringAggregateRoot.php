<?php
/**
 * EventSouring。
 */

namespace zeus\domain;


use zeus\base\event\EventInterface;
use zeus\base\event\EventMessage;
use zeus\sandbox\ApplicationContext;

abstract class EventSouringAggregateRoot extends AbstractEntity
{
    private $domainEvents = [];

    public function __construct($data = null)
    {
        parent::__construct($data);
    }

    public function commit()
    {
        $events = $this->getEvents();
        foreach ($events as $event) {
            ApplicationContext::currentContext()->getEventBus()->publish(new EventMessage($this, $event));
        }
    }

    protected function raise(EventInterface $event)
    {
        $this->domainEvents[] = $event;
        $this->handle($event);
    }

    protected function handle(EventInterface $domainEvent)
    {

    }

    protected function getEvents()
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }
}
