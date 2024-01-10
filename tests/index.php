<?php 
include __DIR__.'/vendor/autoload.php';
$time_start = microtime(true);  
IRoute::get('/',function(){
	echo 1;
}); 
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
	echo $err;
} 