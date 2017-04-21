<?php

namespace zeus\domain;

use CodelyTv\Shared\Domain\Bus\Event\DomainEvent;

abstract class AggregateRoot
{
	protected $data = [];
	
    private $domainEvents = [];

    final public function pullDomainEvents()
    {
        $domainEvents       = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function raise(DomainEvent $domainEvent)
    {
        $this->domainEvents[] = $domainEvent;
    }
    
    public function data(){
    	return $this->data;
    }
    
    public function __get($key){
    	if(isset($this->data[$key])){
    		return $this->data[$key];
    	}
    	return '';
    }
    
    public function __set($key,$val){
    	$this->data[$key] = $val;
    }
}
