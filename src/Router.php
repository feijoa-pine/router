<?php

/**
 * Project Name: mikisan-ware
 * Description : ルーター
 * Start Date  : 2021/07/17
 * Copyright   : Katsuhiko Miki   https://striking-forces.jp
 * 
 * @author Katsuhiko Miki
 */
declare(strict_types=1);

namespace mikisan\core\util;
use \mikisan\core\exception\InvalidParameterAccessException;

require_once __DIR__ . "/subclasses/FETCHER.php";
require_once __DIR__ . "/subclasses/ANALYZER.php";

class Router
{
    const ENTRY_WEB = "web", ENTRY_CLI = "cli";

    private static $instance;
    private $accessable = ["resolved", "route", "method", "module", "action", "params", "args"];
    private $resolved   = false;
    private $method     = "";
    private $module     = "";
    private $action     = "";
    private $params     = [];
    private $args       = [];
    
    /**
     * ゲッター
     * 
     * @param   mixed    $key
     * @return  mixed
     * @throws  InvalidParameterAccessException
     */
    public function __get($key)
    {
        if(!in_array($key, $this->accessable, true))
        {
            throw new InvalidParameterAccessException($this, $key);
        }
        
        return $this->{$key};
    }
    
    /**
     * Singleton
     * 
     * @return ROUTER
     */
    public static function instance(): Router
    {
        if(self::$instance === null)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * routes.yml を解析し、リクエストに対応するルートを決定する
     * 
     * @param string $yml_path
     * @return string
     * @throws FileNotFoundException
     */
    public static function resolve(string $yml_path): Router
    {
        if(self::$instance === null)
        {
            self::$instance = new self;
        }
        //
        $routes         = FETCHER::fetch($yml_path);
        $route_obj      = ANALYZER::analyze($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $routes);
        //
        self::$instance->resolved   = $route_obj->resolved;
        self::$instance->route      = $route_obj->route;
        self::$instance->method     = $route_obj->method;
        self::$instance->module     = $route_obj->module;
        self::$instance->action     = $route_obj->action;
        self::$instance->params     = $route_obj->params;
        self::$instance->args       = $route_obj->args;
        //
        return self::$instance;
    }

    public static function distribute(): string
    {
        return self::get_entry_type();
    }

    private static function get_entry_type(): string
    {
        return isset($_SERVER["SERVER_NAME"]) ? self::ENTRY_WEB : self::ENTRY_CLI;
    }

}
