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

require __DIR__ . "/subclasses/FETCH_ROUTES.php";

class ROUTER
{

    const ENTRY_WEB = "web", ENTRY_CLI = "cli";

    private static $instance;
    private $route = null;

    /**
     * Singleton
     * 
     * @return ROUTER
     */
    public static function instance(): ROUTER
    {
        if(self::$instance === null)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function fetch_routes(string $yml_path): ROUTER
    {
        if(!file_exists($yml_path))
        {
            throw new FileNotFoundException("引数で渡されたファイルは存在しません。[{$yml_path}]");
        }
        $routes = FETCH_ROUTES::fetch($yml_path);
    }

    public static function distribute(): string
    {
        return self::get_entry_type();
    }

    private static function get_entry_type(): string
    {
        return isset($_SERVER["SERVER_NAME"]) ? self::ENTRY_WEB : self::ENTRY_CLI
        ;
    }

}
