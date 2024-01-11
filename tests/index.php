<?php 
include __DIR__.'/vendor/autoload.php'; 

IRoute::get('/',function(){
	echo 1;
});   
IRoute::all('user','core/user/controller/site@index');  
IRoute::all('user/<name:\w+>','core/user/controller/site@index');  
return IRoute::do(function(){
	//路由存在
	 
},function(){
	//路由不存在
	echo '路由不存在';
}); 