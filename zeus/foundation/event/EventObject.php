<?php
namespace bundle\event;

/**
 * event消息体
 * @author nathena
 *
 */
abstract class EventObject
{
	protected $_sender;
	protected $_listeners = [];
	
	public function __construct($_sender)
	{
		$this->_sender = $_sender;
	}
	
	public function getSender()
	{
		return $this->_sender;
	}
	
	public function getListeners()
	{
		return $this->_listeners;
	}
}