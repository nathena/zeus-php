<?php
namespace zeus\event;

interface EventCallable
{
	public function call(EventObject $event);
}