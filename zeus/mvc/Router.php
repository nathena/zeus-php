<?php

namespace zeus\mvc;

use zeus\mvc\exception\ControllerNotFoundException;
use zeus\mvc\exception\RoutingRepeatedException;
use zeus\sandbox\ConfigManager;
use zeus\utils\XssCleaner;

class Router
{
    private static $all_routers = [];
    private static $all_module_routers = [];

    protected $controller;
    protected $action;
    protected $params = [];

    private $uri_path;

    public static function addRouter($router, $handler)
    {
        if (isset(self::$all_routers[$router])) {
            throw new RoutingRepeatedException();
        }
        self::$all_routers[$router] = $handler;
    }

    public static function addModule($module, $ns_prefix)
    {
        if (isset(self::$all_module_routers[$module])) {
            throw new RoutingRepeatedException();
        }
        self::$all_module_routers[$module] = $ns_prefix;
    }

    public static function getAllRouter()
    {
        return self::$all_routers;
    }

    public static function getAllModuleRouter()
    {
        return self::$all_module_routers;
    }

    public function __construct($uri_path)
    {
        $uri_path = trim($uri_path, "/");
        $this->uri_path = empty($uri_path) ? "/" : $uri_path;
        $this->route();
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParams()
    {
        return $this->params;
    }

    private function route()
    {
        if ("/" == $this->uri_path && isset(self::$all_routers["/"])) {
            $this->controller = self::$all_routers["/"];
            $this->action = ConfigManager::config("router.default_controller_action");
            return;
        }

        //rewrite
        if ($this->routerUriRewrite()) {
            return;
        }

        ///module/controller?/action?/params?
        if ($this->routeModulePath()) {
            return;
        }

        throw new ControllerNotFoundException($this->uri_path);
    }

    private function routerUriRewrite()
    {
        if ("/" == $this->uri_path) {
            return false;
        }

        $rewrite = self::$all_routers;
        if (!empty($rewrite) && is_array($rewrite)) {
            foreach ($rewrite as $pattern => $replacement) {
                $pattern = trim($pattern, "/");
                if (preg_match("#^$pattern$#", $this->uri_path)) {
                    $rule = preg_replace("#^$pattern$#", $replacement, $this->uri_path);
                    $rule = explode("@", $rule);

                    if (class_exists($rule[0]))//autoload
                    {
                        $this->controller = $rule[0];
                        if (count($rule) > 1) {
                            $rule = explode("#", $rule[1]);
                            $this->action = $rule[0];
                            if (count($rule) > 1) {
                                $this->merge_params(explode(",", $rule[1]));
                            }
                        } else {
                            $this->action = ConfigManager::config("router.default_controller_action");
                        }

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * /module/controller?/action?/params?
     * @return bool
     */
    private function routeModulePath()
    {
        if ("/" == $this->uri_path) {
            return false;
        }

        $seg_fragment = explode("/", $this->uri_path);
        if (empty($seg_fragment)) {
            return false;
        }

        $fragment = array_shift($seg_fragment);
        if (!isset(self::$all_module_routers[$fragment])) {
            $controller = ConfigManager::config("router.default_model") . "\\" . ucfirst($fragment) . "Controller";
            if (class_exists($controller)) {
                $this->controller = $controller;
                if (empty($seg_fragment)) {
                    $this->action = ConfigManager::config("router.default_controller_action");
                } else {
                    $this->action = array_shift($seg_fragment);
                    if (!empty($seg_fragment)) {
                        $this->merge_params($seg_fragment);
                    }
                }
                return true;
            }
            return false;
        }

        $controller_packpage = self::$all_module_routers[$fragment];
        if (empty($seg_fragment)) {
            $controller = $controller_packpage . "\\" . ConfigManager::config("router.default_controller");
            if (class_exists($controller)) {
                $this->controller = $controller;
                $this->action = ConfigManager::config("router.default_controller_action");
                return true;
            }
            return false;
        }

        $fragment = array_shift($seg_fragment);
        $controller = $controller_packpage . "\\" . ucfirst($fragment) . "Controller";
        if (class_exists($controller)) {
            $this->controller = $controller;
            if (empty($seg_fragment)) {
                $this->action = ConfigManager::config("router.default_controller_action");
            } else {
                $this->action = array_shift($seg_fragment);
                if (!empty($seg_fragment)) {
                    $this->merge_params($seg_fragment);
                }
            }

            return true;
        }

        $controller = $controller_packpage . "\\" . ConfigManager::config("router.default_controller");
        if (class_exists($controller)) {
            $this->controller = $controller;
            $this->action = $fragment;
            if (!empty($seg_fragment)) {
                $this->merge_params($seg_fragment);
            }
            return true;
        }

        return false;
    }

    private function merge_params(array $params)
    {
        $params = XssCleaner::doClean($params);
        $this->params = array_merge($this->params, $params);
    }
}