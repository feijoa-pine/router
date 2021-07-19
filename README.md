# 汎用ルーター

PHPの汎用ルーターです。

シングルトンパターンを採用しています。

## 使い方

静的メソッド resolve() の引数に、ルート構造（YAML形式）を記したファイルへのパスを渡す事で、ルート解決されます。

```
use \mikisan\core\util\Router;
$route  = Router::resolve("/path/to/route.yml");
```

## ルート構造（YAML形式）の例

```
routes:
    index@get:
        module: home
        action: index
    admin:
        service:
            index@get:
                module: admin/service
                action: index
            sort_service@post:
                module: admin/service
                action: sort_service
            master:
                "{id}/register/{num}@post":
                    module: admin/service/master
                    action: register
    blog:
        writer/**@get:
            module: blog
            action: writer
    make:
        "{target_structure}/{target_module}@cli":
            module: build
            action: make
```

以下に、上記 route.yml を使用した場合のルート解決結果を記します。

### 1. WEBアクセスでのルート解決結果

#### ドキュメントルートへのアクセス

GET http(s)://dome.domain/

```
$route->resolved:   true
$route->route:      @get
$route->method:     GET
$route->module:     home
$route->action:     index
```

#### POSTメソッドでのアクセス

POST http(s)://dome.domain/admin/service/sort_service

```
$route->resolved:   true
$route->route:      admin/service/sort_service@post
$route->method:     POST
$route->module:     admin/service
$route->action:     sort_service
```

#### 許可されていないメソッドでのアクセス

GET http(s)://dome.domain/admin/service/sort_service

```
$route->resolved:   false
$route->route:      
$route->method:     GET
$route->module:     home
$route->action:     index
```

#### 埋め込みパラメタを使ったアクセス

POST http(s)://dome.domain/admin/service/1/register/2

```
$route->resolved:   true
$route->route:      admin/service/{id}/register/{num}@post
$route->method:     POST
$route->module:     admin/service
$route->action:     register
$route->params:     ["id" => "1", "num" => "2"]
```

#### 引数を使ったアクセス

GET http(s)://dome.domain/blog/writer/favorit/food/123

```
$route->resolved:   true
$route->route:      blog/writer/**@get
$route->method:     GET
$route->module:     blog
$route->action:     writer
$route->args:       ["favorit", "food", "123"]
```

### 2. CLIアクセスでのルート解決結果

php some_program.php make controller test

```
$route->resolved:   true
$route->route:      make/{target_structure}/{target_module}@cli
$route->method:     CLI
$route->module:     build
$route->action:     make
$route->params:     ["target_structure" => "controller", "target_module" => "test"]
```

