<?php

/**
 * Project Name: mikisan-ware
 * Description : 汎用ルーター
 * Start Date  : 2021/07/17
 * Copyright   : Katsuhiko Miki   https://striking-forces.jp
 * 
 * @author Katsuhiko Miki
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use mikisan\core\util\ANALYZER;
use mikisan\core\util\FETCHER;

$project_root = realpath(__DIR__ . "/../../../../");
require "{$project_root}/vendor/autoload.php";
require_once "{$project_root}/core/utilities/yaml/YAML.php";

require_once __DIR__  . "/TestCaseExtend.php";
require_once __DIR__ . "/../src/subclasses/FETCHER.php";
require_once __DIR__ . "/../src/subclasses/ANALYZER.php";

class ANALYZER_Test extends TestCaseExtend
{
    private $class_name = "mikisan\core\util\ANALYZER";
    private $routes;
    
    protected function setUp(): void
    {
        $yml_path       = realpath(__DIR__ . "/routes.yml");
        $this->routes   = FETCHER::fetch($yml_path);
    }
        
    /**
     * 渡されたURIを解析し、メソッド、アクション等の情報を取得する　テスト
     */
    public function test_analyze_001()
    {
        $obj    = ANALYZER::analyze("GET", [""], $this->routes);
        $this->assertTrue($obj->resolved);
        $this->assertEquals("@get",     $obj->route);
        $this->assertEquals("GET",      $obj->method);
        $this->assertEquals("home",     $obj->module);
        $this->assertEquals("index",    $obj->action);
        $this->assertCount(0,           $obj->params);
        $this->assertCount(0,           $obj->args);
    }
    
    public function test_analyze_002()
    {
        $obj    = ANALYZER::analyze("POST", [""], $this->routes);
        $this->assertFalse($obj->resolved);
        $this->assertEquals("",         $obj->route);
        $this->assertEquals("GET",      $obj->method);
        $this->assertEquals("home",     $obj->module);
        $this->assertEquals("index",    $obj->action);
        $this->assertCount(0,           $obj->params);
        $this->assertCount(0,           $obj->args);
    }
    
    public function test_analyze_003()
    {
        $obj    = ANALYZER::analyze("POST", ["check"], $this->routes);
        $this->assertTrue($obj->resolved);
        $this->assertEquals("check@wild",   $obj->route);
        $this->assertEquals("WILD",     $obj->method);
        $this->assertEquals("home",     $obj->module);
        $this->assertEquals("check",    $obj->action);
        $this->assertCount(0,           $obj->params);
        $this->assertCount(0,           $obj->args);
    }
    
    public function test_analyze_004()
    {
        $obj    = ANALYZER::analyze("GET", ["blog", "writer", "hoge", "hage", "123"], $this->routes);
        $this->assertTrue($obj->resolved);
        $this->assertEquals("blog/writer/**@get",   $obj->route);
        $this->assertEquals("GET",      $obj->method);
        $this->assertEquals("blog",     $obj->module);
        $this->assertEquals("writer",   $obj->action);
        $this->assertCount(0,           $obj->params);
        $this->assertCount(3,           $obj->args);
        $this->assertEquals("hoge",     $obj->args[0]);
        $this->assertEquals("hage",     $obj->args[1]);
        $this->assertEquals("123",      $obj->args[2]);
    }
    
    public function test_analyze_005()
    {
        $obj    = ANALYZER::analyze("GET", ["blog", "hoge", "category", "123"], $this->routes);
        $this->assertTrue($obj->resolved);
        $this->assertEquals("blog/{id}/category/{cat_id}@get",   $obj->route);
        $this->assertEquals("GET",      $obj->method);
        $this->assertEquals("blog",     $obj->module);
        $this->assertEquals("category", $obj->action);
        $this->assertCount(2,           $obj->params);
        $this->assertArrayHasKey("id",      $obj->params);
        $this->assertArrayHasKey("cat_id",  $obj->params);
        $this->assertEquals("hoge",     $obj->params["id"]);
        $this->assertEquals("123",      $obj->params["cat_id"]);
        $this->assertCount(0,           $obj->args);
    }
    
}
