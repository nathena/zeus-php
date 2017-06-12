<?php
namespace test_bus;

use zeus\sandbox\ApplicationContext;

include_once "bootstrap.php";

ApplicationContext::currentContext()->getEventBus()->subscribe("","");