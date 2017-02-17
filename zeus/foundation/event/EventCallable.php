<?php
namespace zeus\foundation\event;

/**
 * event handler
 * @author nathena
 *
 */
interface EventCallable
{
	public function call(EventObject $event);
}