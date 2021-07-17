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
use mikisan\core\util\ROUTER;

require __DIR__  ."/../ROUTER.php";

class ROUTER_Test extends TestCase
{
    public function test_distribute()
    {
        $_SERVER["SERVER_NAME"] = "striking-forces.jp";
        
        $type   = ROUTER::distribute();
        
        $this->assertEquals("web", $type);
    }
}
