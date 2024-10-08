# IRouter
 
路由

## 安装

~~~
composer require thefunpower/router
~~~

路由加载优先级 `core` > `app` > `modules`

## rewrite

~~~
location / {
  if (!-e $request_filename){
    rewrite ^(.*)$ /index.php last;
  }
}
~~~

## 目录

~~~
core/user/controller/site.php
app/invoice/controller/site.php
~~~

演示代码

~~~
<?php 
namespace app\user\controller;

class site{ 

	public function action_index(){
		return 'site';
	} 

}
~~~

需要返回jsons数据可用

~~~
return ['data'=>''];
~~~


## 开始
~~~
IRoute::get('/',function(){
	echo 1;
});   
IRoute::all('user','core/user/controller/site@index');   
return IRoute::do(function(){
	//路由存在
	 
},function(){
	//路由不存在
	echo '路由不存在';
	//取具体错误信息
	pr(IRoute::$err);
}); 
~~~

## 取当前的模块、控制器、动作
~~~ 
protected $package;
protected $module;
protected $controller;
protected $action;

$route = IRoute::get_action();
$this->package = $route['package'];
$this->module = $route['module'];
$this->controller = $route['controller'];
$this->action = $route['action'];
$model = $this->model;
~~~

## 生成URL

~~~
IRouter::create_url($url,$par = []);
~~~

## 控制器名称

默认全小写，如需改成首字母大写

~~~
IRouter::$controller_name = 'ucfirst';
~~~

## 更多规则

~~~ 
IRoute::get('/',function(){
	echo 1;
});
IRoute::all('login/<name:\w+>','app\login\$name@index','login');  
//aa 为url地址，home为生成url链接所用的名称 	
IRoute::get('aa',"app\controller\index@index",'home'); 
IRoute::get('post/<id:\d+>/<g:\d+>',"app\controller\index@test",'post');
IRoute::get('payadmin','app\pay\admin@list');
IRoute::get('payadmin/<page:\d+>','app\pay\admin@list');
IRoute::domain('user2.api.com',function(){
	IRoute::get('/',function(){
		echo 111;		
	});
	IRoute::get('test',function(){
		echo 'test';		
	});
});
Route::get('post/<id:\d+>|post',function(){    
},'@post');
IRoute::get('post/<id:\d+>|post',function(){    
},'@post|$po');
~~~

## composer 

~~~
"autoload": {
    "psr-4": {
        "core\\": "core",
        "app\\": "app",
        "modules\\": "modules"
    }
}
~~~


## LICENSE

[Apache License 2.0](LICENSE)

