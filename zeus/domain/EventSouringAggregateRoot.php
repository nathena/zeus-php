<?php
/**
 * EventSouring。
 */

namespace zeus\domain;


use zeus\base\event\AbstractEvent;
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

    protected function raise(AbstractEvent $event)
    {
        $this->domainEvents[] = $event;
        $this->handle($event);
    }

    protected function handle(AbstractEvent $domainEvent)
    {

    }

    protected function getEvents()
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }
}
