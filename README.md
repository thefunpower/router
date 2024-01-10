# IRouter
 
路由

## 安装

~~~
composer require thefunpower/router
~~~

## 开始
~~~
$time_start = microtime(true);
try {  
	$IRoute = IRoute::run();  
	if($IRoute){
		echo $IRoute;
		$time_end = microtime(true);
		$used = number_format($time_end - $time_start,2);
		echo "\n<!--total used time:". $used.'s-->';
	}
}catch (Exception $e) {  
	$err = $e->getMessage(); 
} 
~~~

## 生成URL

~~~
IRouter::create_url($url,$par = []);
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

## 多模块

加载顺序 `core` `app` `modules`

~~~
<?php 
namespace core\user\controller;

class site{
	
	public function action_index(){
		echo 'core';
	}
}
~~~

~~~
<?php 
namespace app\user\controller;

class site{

	public function action_index(){
		echo 111;
	}
}

~~~
 

## LICENSE

[Apache License 2.0](LICENSE)

