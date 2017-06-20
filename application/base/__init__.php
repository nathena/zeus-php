<?php
namespace base;
//base url rewrite
use zeus\mvc\Router;

Router::addRouter('/favicon.ico',EmptyController::class);
Router::addRouter('/welcome',WelcomePlatformController::class);
