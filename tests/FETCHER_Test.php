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
use mikisan\core\util\FETCHER;

$project_root = realpath(__DIR__ . "/../../../../");
require_once "{$project_root}/vendor/autoload.php";
require_once "{$project_root}/core/utilities/yaml/YAML.php";
require_once "{$project_root}/core/exceptions/FileNotFoundException.php";
require_once "{$project_root}/core/exceptions/FileOpenFailedException.php";

require_once __DIR__  . "/TestCaseExtend.php";
require_once __DIR__ . "/../src/subclasses/FETCHER.php";

class FETCHER_Test extends TestCaseExtend
{
    private $class_name = "mikisan\core\util\FETCHER";
    
    /**
     * ルートの /index アクション表記を省略する　のテスト
     */
    public function test_reformat_route()
    {
        $this->assertEquals("service/sort_service@post", $this->callMethod($this->class_name, "reformat_route", ["service/sort_service@post"]));
        $this->assertEquals("service@get", $this->callMethod($this->class_name, "reformat_route", ["service/index@get"]));
        $this->assertEquals("@get", $this->callMethod($this->class_name, "reformat_route", ["index@get"]));
        $this->assertEquals("@get", $this->callMethod($this->class_name, "reformat_route", ["@get"]));
    }
    
    /**
     * routes.yml を読み込み、ルートリストを取得する　のテスト
     */
    public function test_fetch()
    {
        $yml_path   = realpath(__DIR__ . "/routes.yml");
        $r          = FETCHER::fetch($yml_path);
        //
        $this->assertIsArray($r);
        $this->assertCount(7, $r);
        //
        $this->assertArrayHasKey("@get", $r);
        $this->assertEquals("home", $r["@get"]["module"]);
        $this->assertEquals("index", $r["@get"]["action"]);
        //
        $this->assertArrayHasKey("check@wild", $r);
        $this->assertEquals("home", $r["check@wild"]["module"]);
        $this->assertEquals("check", $r["check@wild"]["action"]);
        //
        $this->assertArrayHasKey("admin/service@get", $r);
        $this->assertEquals("admin/service", $r["admin/service@get"]["module"]);
        $this->assertEquals("index", $r["admin/service@get"]["action"]);
        //
        $this->assertArrayHasKey("admin/service/sort_service@post", $r);
        $this->assertEquals("admin/service", $r["admin/service/sort_service@post"]["module"]);
        $this->assertEquals("sort_service", $r["admin/service/sort_service@post"]["action"]);
        //
        $this->assertArrayHasKey("admin/service/master/{id}/register/{num}@post", $r);
        $this->assertEquals("admin/service/master", $r["admin/service/master/{id}/register/{num}@post"]["module"]);
        $this->assertEquals("register", $r["admin/service/master/{id}/register/{num}@post"]["action"]);
        //
        $this->assertArrayHasKey("blog/writer/**@get", $r);
        $this->assertEquals("blog", $r["blog/writer/**@get"]["module"]);
        $this->assertEquals("writer", $r["blog/writer/**@get"]["action"]);
        //
        $this->assertArrayHasKey("blog/{id}/category/{cat_id}@get", $r);
        $this->assertEquals("blog", $r["blog/{id}/category/{cat_id}@get"]["module"]);
        $this->assertEquals("category", $r["blog/{id}/category/{cat_id}@get"]["action"]);
    }
    
}
