<?php  
/**
 * 路由
 * @since 2014
 */ 
class IRoute{
	//基础URL
	public $base_url;
	protected $method;
	static $router;  
	public $match = '/<(\w+):([^>]+)?>/';
	static $app = [];
	//相对URL
	static $index;
	/**
	*默认路由模块namespace为module
	*/
	static $r = 'controller';   
	//当前正则的URL 如 aa
	protected $_url;
	//当前URL的function 如 function(){}  
	protected $_value; 
	public $host;
	public $class = [];
	static $obj;
	//当前使用的CLASS
	static $current_class;
	//当前域名　
	static $current_domain; 
	/**
	* 初始化 
	*/
	static function init(){
		if(!isset(static::$obj))
			static::$obj = new Static;
		return static::$obj;
	}
	/**
	* uri   
	*/
	static function uri(){
		$uri = static::_uri();
		if($uri!='/'){
			$uri = substr($uri,1);
		}
		return $uri;
	}
	/**
	*内部函数  
	*/
	static function _uri(){
		//解析URL $uri 返回 /app/public/ 或  / 
		$uri = $_SERVER['REQUEST_URI']; 
		$uri = str_replace("//",'/',$uri);
		$uri = str_replace(static::host(),'',$uri);
		if(strpos($uri,'?')!==false)
			$uri = substr($uri,0,strpos($uri,'?'));
		return $uri;
	}
	/**
	* 构造函数 
	*/
	function __construct(){
		//请求方式 GET POST
 		$this->method = $_SERVER['REQUEST_METHOD'];   
 		$this->host = static::host();   
 	}  
 	/**
	* server_name 　 
	* @return   
	*/
 	static function server_name(){
 		return $_SERVER['SERVER_NAME'];
 	}
 	/**
	* host自动加http://或https://
	* @return string   
	*/
 	static function host(){
 		$top = 'http';
 		if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){
 			$top = 'https';
 		}
 		else if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 1 || $_SERVER['HTTPS'] == 'on')){
 			$top = 'https';
 		}
 		return $top."://".static::server_name(); 
 	}
 	/**
	* domain路由
	*
	* @param string $domain 　 
	* @param string $fun 　 
	* @return   call_user_func
	*/
 	static function domain($domain,$fun){ 
 		if($domain!=static::server_name()) return;
 	 	call_user_func($fun); 
 	} 
 	/**
	* 返回当前　模块　控制器　动作　以 / 相连的字符串 
	*/
 	static function string(){
 		$id = static::controller();
 		$m = $id['module']; 
 		$str = $id['id'];
 		$i = $id['action'];
  		if($m)
 			$str = $m.'/'.$str;
 		return $str.'/'.$i;
 	}
 	/**
 	* 取得控制器的 model id action
 	* [_id] => module/admin/admin
    * [action] => login
    * [module] => admin
    * [id] => admin
 	*/ 
 	static function controller(){
 	 	 $ar = static::init()->class; 
 		 $vo['_id'] = $id = str_replace('\\','/',$ar[0]); 
 		 $vo['action'] = $ar[1];  
 		 $arr = explode('/',str_replace(static::$r.'/','',$id));
 		 $vo['module'] = $arr[0];
 		 $vo['id'] = $arr[1];
 		 $vo['controller'] = $vo['id'];   
 		 return $vo;
 	}
 	/**
  	*	对GET POST all 设置router
 	*/
	protected function set_router($url,$do,$method='GET',$name=null){   
 		if(strpos($url,'|')!==false){
 			$arr = explode('|',$url);
 			if(strpos($name,'|')!==false){
 				$names = explode('|',$name);
 			}else{
				$names[0] = $name;
 			}
 			$i = 0;
 			foreach($arr as $v){
 				$this->set_router($v,$do,$method,$names[$i]);
 				$i++;
 			}
 			return;
 		}
 		if(!is_object($do) && !$name){
			$n = str_replace('@','/',$do);
			$new = str_replace('\\','/',$n);
			$name = substr($new , strpos($new,'/')+1); 
		}
 		if(strpos($url,'<')!==false){
			$url = "#^\/{$url}\$#";
		}elseif(substr($url,0,1)!='/'){
			$url = '/'.$url;
		} 
		static::$router[$method][$url] = $do;
		if($name)
			static::$router['__#named#__'][$name] = $url;
		 
	}
	static function url($url,$par = []){
		return static::init()->create_url($url,$par);
	}
	/**
	*	自动生成URL
	*/
	protected function create_url($url,$par = []){
		$url = str_replace('.','/',$url);
		$id = 'route_url'.$url.json_encode($par);
		if(isset(static::$app[$id]) && static::$app[$id]){
			return static::$app[$id];
		}  
		if(isset(static::$router['__#named#__'][$url])){
			$r = static::$router['__#named#__'][$url];
			preg_match_all($this->match, $r, $out);
			$a = $out[0];
			$b = $out[1];
		}
		else{
			$a = array();
			$b = array();
		}  
		if($b){
			$i = 0;
			foreach($b as $v){
				$r = str_replace($a[$i],$par[$v],$r);
				unset($par[$v]);
				$i++;
			}
		}  
		if(isset($r) && $r=='/') goto GT;
		if(isset($r) && substr($r,0,2) == '#^')
			$r = substr($r,4,-2);  
		if(isset($r) && substr($r,-1)=='/')
			$r = substr($r,0,-1);  
		if(!isset($r) || !$r) $r = $url; 
		GT:
		if($par) 
			$r = $r."?".http_build_query($par);  
 		$url = $this->base_url.$r;
 		$url = str_replace("//",'/',$url);
 		static::$app[$id] = $url; 
	 	return $url;	 
	}
  
	/**
	* get request
	*/
	static function get($url,$do,$name=null){
		static::init()->set_router($url,$do,'GET',$name); 
	}

	/**
	* post request
	*/
	static function post($url,$do,$name=null){
		static::init()->set_router($url,$do,'POST',$name); 
	}
	/**
	* put request
	*/
	static function put($url,$do,$name=null){
		static::init()->set_router($url,$do,'PUT',$name); 
	}
	/**
	* put request
	*/
	static function delete($url,$do,$name=null){
		static::init()->set_router($url,$do,'DELETE',$name); 
	}
	/**
	* get/post request
	*/
	static function all($url,$do,$name=null){
		static::init()->set_router($url,$do,'POST',$name); 
		static::init()->set_router($url,$do,'GET',$name);  
	}
	/**
	* 执行路由 
	*/
	static function run(){
		return static::init()->exec();
	}
	/**
	* 内部函数,	执行解析URL 到对应namespace 或 closure 
	*/
	protected function exec(){  
		//解析URL $uri 返回 /app/public/ 或  /  
		$uri = static::_uri(); 
		//取得入口路径
		$index = $_SERVER['SCRIPT_NAME'];
		$index = substr($index,0,strrpos($index,'/')); 
		$action = substr($uri,strlen($index)); 
	 	$this->base_url = $index?$index.'/':'/'; 
	 	/**
	 	*	对于未使用正则的路由匹配到直接goto
	 	*/
	 	if(isset(static::$router[$this->method][$action])&&static::$router[$this->method][$action]){
	 		$this->_value = static::$router[$this->method][$action]; 
	 	}
	 	else{
	 		$this->_value = false;
	 	}
		
		$data = []; 
		if($this->_value) goto TODO; 
		if(!isset(static::$router[$this->method])) goto NEXT;
		foreach(static::$router[$this->method] as $pre=>$class){  
			if(preg_match_all($this->match, $pre, $out)){
				//转成正则   
        foreach($out[0] as $k=>$v){ 
        	$pre = str_replace($v,"(".$out[2][$k].")",$pre);
        }  
        $pregs[$pre] = ['class'=>$class,'par'=>$out[1]]; 
			}  
		}
		NEXT:
		/**
		*	匹配当前URL是否存在路由
		*/   
		if(isset($pregs) && $pregs){
			foreach($pregs as $p=>$par){ 
				$class = $par['class'];
				if(preg_match($p,$action, $new)){ 
					unset($new[0]);
         	//根据请求设置值 $_POST $_GET
          $data = $this->set_request_value($this->array_combine($par['par'],$new));    
          $this->_url = $pre;
          $this->_value = $class;  
          goto TODO;  
        } 
               
			}
		} 
	 	if($this->_value){ 
	 		TODO:
	 		// 如果是 closure 
	 		if(is_object($this->_value) || ($this->_value instanceof Closure) )
	 			return call_user_func_array($this->_value,$data); 
	 		// 对 namespace 进行路由
	 		$this->_value = str_replace('/','\\',$this->_value); 
	 		$cls = explode('@',$this->_value);   
 			$class = $cls[0];
 			if($data){
 				// $route->get('aa',"app\controller\index@index",'home'); 
 				foreach($data as $k=>$v){
 					$class = str_replace("$".$k,$v,$class);
 				}
 			} 
 			$ac = $cls[1];
 			return $this->load_route($class,$ac,$data);
	 	}  
 	 	//加载app\admin\login.php 这类的自动router  
 		$action = trim(str_replace('/',' ',$action));
	 	$a = explode(' ',$action);
	 	if(isset($a[0])){
	 		$class = static::$r."\\".$a[0];
	 		if(isset($a[1])){
		 		$class = $class."\\".$a[1];
		 	}
	 	}
	 	if(isset($a[2])&&$a[2]){
	 		$ac = $a[2];
	 	}
	 	else{
	 		$ac = 'index';
	 	}
	 	return $this->load_route($class,$ac,$data); 
	}
	/**
	* 建议内部使用
	*/
	static function get_class(){
		return static::$current_class;
	}
	/**
	* 内部函数，支持框架内部框架
	*/
	protected function load_route($class,$ac,$data){  
		$this->class = [$class,$ac];		
		static::$current_class = $class;
		$this->class_exists($class,$ac);
		$obj = new $class;  
		if(method_exists($class,'_before'))
			call_user_func_array([$obj,"_before"],$data);
		echo call_user_func_array([$obj,$ac."Action"],$data); 
		if(method_exists($class,'_after'))
			call_user_func_array([$obj,"_after"],$data);   
	}
	/**
	* 内部函数 
	*/
	protected function class_exists($class,$ac){
		if(!class_exists($class)) throw new \Exception(' Class not exists: '.$class,400);
	 	if(!method_exists($class,$ac."Action")) throw new \Exception("$ac Action  not exists",400);
	} 
	 
	/**
	* 内部函数 ，对array_combine优化
	*/
	protected function array_combine($a=[],$b=[]){   
		 $i = 0;
		 foreach($b as $v){
		 	$out[$a[$i]] = $v;
		 	$i++;
		 } 
		 return $out; 
	}
	/**
	*	内部函数 ,根据请求设置值
	*/
	protected function set_request_value($data){  
			switch($this->method){
           		case 'GET': 
           			$_GET = array_merge($data,$_GET);
           			break;
           		case 'POST':
           			$_POST = array_merge($data,$_POST);
           			break;
           		case 'PUT':
           			$_PUT = array_merge($data,$_PUT); 
           			break;
           		case 'DELETE':
           			$_PUT = array_merge($data,$_DELETE); 
           			break;
           	} 
           	return $data;
	}
}