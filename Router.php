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
use \mikisan\core\exception\FileNotFoundException;
use \mikisan\core\exception\InvalidParameterAccessException;

require __DIR__ . "/subclasses/FETCHER.php";
require __DIR__ . "/subclasses/ANALYZER.php";

class Router
{

    const ENTRY_WEB = "web", ENTRY_CLI = "cli";

    private static $instance;
    private $accessable = ["resolved", "method", "module", "action", "params", "args"];
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
            throw new InvalidParameterAccessException("クラス: " .get_class($this) . " のパラメタ {$key} はアクセスできません。");
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
        if(!file_exists($yml_path))
        {
            throw new FileNotFoundException("引数で渡されたファイルは存在しません。[{$yml_path}]");
        }
        //
        if(self::$instance === null)
        {
            self::$instance = new self;
        }
        //
        $routes         = FETCHER::fetch($yml_path);
        $route_obj      = ANALYZER::analyze($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $routes);
        //
        $this->resolved = $route_obj->resolved;
        $this->method   = $route_obj->method;
        $this->module   = $route_obj->module;
        $this->action   = $route_obj->action;
        $this->params   = $route_obj->params;
        $this->args     = $route_obj->args;
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
