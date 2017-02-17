<?php
namespace zeus\foundation\event;

/**
 * event消息体
 * @author nathena
 *
 */
abstract class EventObject
{
	protected $caller;
	
	public function __construct($caller){
		$this->caller = $caller;	
	}
}