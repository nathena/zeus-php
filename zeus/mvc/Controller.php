<?php

namespace zeus\mvc;

abstract class Controller
{
	public function getRequest(){
	    return Application::getInstance()->getRequest();
    }

    public function getResponse(){
        return Application::getInstance()->getResponse();
    }

    public function getView($tpl_path){
        return new View($tpl_path);
    }

    public function forward($url_path){
        Application::getInstance()->dispatch($url_path);
    }

	public function errorHandler(\Exception $e)
	{
		throw $e;
	}


}