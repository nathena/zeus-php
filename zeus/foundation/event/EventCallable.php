<?php
namespace zeus\foundation\event;

interface EventCallable
{
	public function call(EventObject $event);
}