<?php
namespace zeus\foundation\event;

class EventManager
{
	public static $events = [
		'application\models\user\UserAddPaymentEvent'=>[
			'application\models\user\handler\UserAddPaymentEventHandler',
		],
	];
}