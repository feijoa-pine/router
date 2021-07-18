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

use PHPUnit\Framework\TestCase;
use mikisan\core\util\Router;

require_once __DIR__  . "/TestCaseExtend.php";
require_once __DIR__  . "/../src/Router.php";

class ROUTER_Test extends TestCaseExtend
{
    private $class_name = "mikisan\core\util\Router";
    
    public function test_resolve()
    {
        $yml_path   = __DIR__ . "/routes.yml";
        $_SERVER["REQUEST_METHOD"]  = "GET";
        $_SERVER["REQUEST_URI"]     = "/";
        //
        $route      = Router::resolve($yml_path);
        $this->assertTrue($route->resolved);
        $this->assertEquals("@get",     $route->route);
        $this->assertEquals("GET",      $route->method);
        $this->assertEquals("home",     $route->module);
        $this->assertEquals("index",    $route->action);
        $this->assertCount(0,           $route->params);
        $this->assertCount(0,           $route->args);
    }
    
    /*
    public function test_distribute()
    {
        $_SERVER["SERVER_NAME"] = "striking-forces.jp";
        
        $type   = ROUTER::distribute();
        
        $this->assertEquals("web", $type);
    }
     */
}
