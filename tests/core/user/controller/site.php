<?php 
namespace core\user\controller;

class site{
	protected $name = '';
	public function before($name = '')
	{
		$this->name = $name;
		 
	}

	public function after()
	{
		 
	}

	public function action_index(){ 
		return ['name'=>$this->name,'url'=>\IRoute::url('user/site/index')];
	}

	public function action_test(){
		echo 'test core:: '. \IRoute::url('user/site/test');
	}
}