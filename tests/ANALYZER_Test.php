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
use mikisan\core\util\ANALYZER;
use mikisan\core\util\FETCHER;

require_once __DIR__  . "/TestCaseExtend.php";

class ANALYZER_Test extends TestCaseExtend
{
    private $class_name = "mikisan\core\util\ANALYZER";
    
    /**
     * 先頭と末尾の / を取り除くテスト
     */
    public function test_to_naked()
    {
        $this->assertEquals("home/index", $this->callMethod($this->class_name, "to_naked", ["/home/index"]));
        $this->assertEquals("home/index", $this->callMethod($this->class_name, "to_naked", ["home/index/"]));
        $this->assertEquals("home/index", $this->callMethod($this->class_name, "to_naked", ["/home/index/"]));
        //
        $this->assertEquals("home/index", $this->callMethod($this->class_name, "to_naked", ["///home///index"]));
        $this->assertEquals("home/index", $this->callMethod($this->class_name, "to_naked", ["home///index///"]));
        $this->assertEquals("home/index", $this->callMethod($this->class_name, "to_naked", ["///home//index///"]));
        //
        $this->assertEquals("", $this->callMethod($this->class_name, "to_naked", ["/"]));
        $this->assertEquals("", $this->callMethod($this->class_name, "to_naked", ["////"]));
    }
    
    /**
     * 渡されたURIを解析し、メソッド、アクション等の情報を取得する　テスト
     */
    public function test_analyze()
    {
        $yml_path   = realpath(__DIR__ . "/routes.yml");
        $routes     = FETCHER::fetch($yml_path);
        //
        $obj    = ANALYZER::analyze("GET", "/", $routes);
        $this->assertTrue($obj->resolved);
        $this->assertEquals("@get",     $obj->route);
        $this->assertEquals("GET",      $obj->method);
        $this->assertEquals("home",     $obj->module);
        $this->assertEquals("index",    $obj->action);
        $this->assertCount(0,           $obj->params);
        $this->assertCount(0,           $obj->args);
        //
        $obj    = ANALYZER::analyze("POST", "/", $routes);
        $this->assertFalse($obj->resolved);
        $this->assertEquals("",         $obj->route);
        $this->assertEquals("GET",      $obj->method);
        $this->assertEquals("home",     $obj->module);
        $this->assertEquals("index",    $obj->action);
        $this->assertCount(0,           $obj->params);
        $this->assertCount(0,           $obj->args);
        //
        $obj    = ANALYZER::analyze("POST", "/check", $routes);
        $this->assertTrue($obj->resolved);
        $this->assertEquals("check@wild",   $obj->route);
        $this->assertEquals("WILD",     $obj->method);
        $this->assertEquals("home",     $obj->module);
        $this->assertEquals("check",    $obj->action);
        $this->assertCount(0,           $obj->params);
        $this->assertCount(0,           $obj->args);
    }
}
