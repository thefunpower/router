<?php 
namespace app\user\controller;

class site{

	public function action_index(){
		echo 111;
	}

	public function action_test(){
		return 'test core:: '. \IRoute::url('user/site/test');
	}

}