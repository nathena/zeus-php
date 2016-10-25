<?php
if ( strnatcasecmp(phpversion(),'5.3') <= 0 )
{
	exit(" Must use php 5.3+");
}

require_once 'foundation/mvc/Application.php';

$application = \zeus\foundation\mvc\Application::getCurrentApplication();
$application->start();