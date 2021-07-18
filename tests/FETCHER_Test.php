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
require "{$project_root}/vendor/autoload.php";
require "{$project_root}/core/utilities/yaml/YAML.php";

class FETCHER_Test extends TestCase
{
    
    /**
     * routes.yml を読み込み、ルートリストを取得するテスト
     */
    public function test_fetch()
    {
        $yml_path   = realpath(__DIR__ . "/routes.yml");
        $r          = FETCHER::fetch($yml_path);
        //
        $this->assertIsArray($r);
        $this->assertCount(4, $r);
        //
        $this->assertArrayHasKey("home@get", $r);
        $this->assertArrayHasKey("admin/service@get", $r);
        $this->assertArrayHasKey("admin/service/sort_service@post", $r);
        $this->assertArrayHasKey("admin/service/master/{id}/register/{num}@post", $r);
        //
        $this->assertEquals("home", $r["home@get"]["module"]);
        $this->assertEquals("index", $r["home@get"]["action"]);
        $this->assertEquals("admin/service", $r["admin/service@get"]["module"]);
        $this->assertEquals("index", $r["admin/service@get"]["action"]);
        $this->assertEquals("admin/service", $r["admin/service/sort_service@post"]["module"]);
        $this->assertEquals("sort_service", $r["admin/service/sort_service@post"]["action"]);
        $this->assertEquals("admin/service/master", $r["admin/service/master/{id}/register/{num}@post"]["module"]);
        $this->assertEquals("register", $r["admin/service/master/{id}/register/{num}@post"]["action"]);
    }

}
