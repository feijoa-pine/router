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

class ANALYZER
{
    /**
     * 先頭と末尾の / を取り除く
     * 
     * @param   string  $request_uri
     * @return  string
     */
    private static function to_naked(string $request_uri): string
    {
        $temp   = preg_replace("|/+|u", "/", $request_uri);
        if($temp === "/")   { return ""; }
        return preg_replace("|^/?([^/].+[^/])/?|u", "$1", $temp);
    }
    
    /**
     * 渡されたURIを解析し、メソッド、アクション等の情報を取得する
     *
     * @param   string  $request_method     $_SERVER["REQUEST_METHOD"]  例）GET
     * @param   string  $request_uri        $_SERVER["REQUEST_URI"]     例）/admin/user/
     * @param   array   $routes             routes.ymlに設定されているルート情報
     * @return  \stdClass                   オブジェクト full_command, command, action, params
     */
    public static function analyze(string $request_method, string $request_uri, array $routes): \stdClass
    {
        $request_path       = self::to_naked($request_uri);
        
        foreach($routes as $route => $setting)
        {
            list($route_path, $temp_method)    = explode("@", $route);
            $route_method   = isset($temp_method) ? strtoupper($temp_method) : "WILD";
            
            if($route_method !== "WILD" && $route_method !== $request_method)   { continue; }  // HTTPメソッド判定
            
            $route_parts    = explode("/", $route_path);
            $request_parts  = explode("/", $request_path);
            $params         = [];
            $args           = [];
            
            if(!self::collate($route_parts, $request_parts, $params, $args))     { continue; }
            
            // マッチ（ルート決定）        
            return self::set_route_obj($route, $route_method, $setting, $params, $args);
        }
        
        // ルートが見つからない
        return self::default_route_obj();
    }
    
    /**
     * ルートの照合処理
     * 
     * @param   array   $route_parts        routes.yml のルートパス要素配列
     * @param   array   $request_parts      リクエストされたルートパス要素配列
     * @return  bool                        適合する場合 true を返す
     */
    private static function collate(array $route_parts, array $request_parts, array &$params, array &$args): bool
    {
        $has_wildcard   = ($route_parts[count($route_parts) - 1] === "**") ? true : false; 
        
        // ルートの照合処理
        $i  = 0;
        foreach($route_parts as $route)
        {
            // 埋め込みパラメタの処理
            if(preg_match("/{.+}/u", $route))
            {
                $id             = preg_replace("/{(.+)}/u", "$1", $route);  // {id}埋め込みパラメタをembedded配列に格納
                $params[$id]    = $request_parts[$i];
                $i++;
                continue;
            }
            if($route === "**")                 { break; }          // wildcard　の場合はここで照合終了
            if($route !== $request_parts[$i])   { return false; }   // マッチング
            $i++;
        }
        
        // wildcard　ではない場合、リクエストされたルートパスの方が長い場合は非マッチ
        if(!$has_wildcard && isset($request_parts[$i]))  { return false; }
        
        $args   = array_slice($request_parts, $i);
        
        return true;
    }
    
    /**
     * ルートオブジェクトを生成して返す
     * 
     * @param   string  $route
     * @param   string  $route_method
     * @param   array   $setting
     * @param   array   $params
     * @param   array   $args
     * @return  \stdClass
     */
    private static function set_route_obj(string $route, string $route_method, array $setting, array $params, array $args): \stdClass
    {
        $obj            = new \stdClass();
        $obj->resolved  = true;
        $obj->route     = $route;
        $obj->method    = $route_method;
        $obj->module    = $setting["module"];
        $obj->action    = $setting["action"];
        $obj->params    = $params;
        $obj->args      = $args;
        return $obj;
    }
    
    /**
     * ルートが見つからない場合のデフォルトルートオブジェクト
     * 
     * @return \stdClass
     */
    private static function default_route_obj(): \stdClass
    {
        $obj            = new \stdClass();
        $obj->resolved  = false;
        $obj->route     = "";
        $obj->method    = "GET";
        $obj->module    = "home";
        $obj->action    = "index";
        $obj->params    = [];
        $obj->args      = [];
        return $obj;
    }
    
}
