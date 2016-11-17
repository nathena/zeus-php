<?php
namespace zeus\foundation\event;

abstract class EventObject
{
	protected $_handles = [];
	
	public final function __publish(){
		foreach($_handles as $_handle){
			if( $_handle instanceof EventCallable ){
				$_handle->call($this);
			}
		}
	}
}