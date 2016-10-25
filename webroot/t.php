<?php
define('ZEUS_START_TIME', microtime(true));
define('ZEUS_START_MEM', memory_get_usage());

echo ZEUS_START_MEM;

echo '<hr>';
echo '<br>',microtime(true)-ZEUS_START_TIME;
echo '<br>',memory_get_usage();
echo '<br>';