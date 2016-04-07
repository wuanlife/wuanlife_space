<?php 

class Domain_Group {

	public $msg   = '';
	public $model = '';
	public $cookie = array();
	public $u_status = '0';
	public $g_status = '0';

	public function checkN($g_name){
		$rs = $this->model->checkName($g_name);
		if (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u', $g_name)) {
			$this->msg = '小组名只能为中文、英文、数字或者下划线，但不得超过20字节！';
		}elseif (!empty($rs)) {
            $this->msg = '该星球已创建！';
        }else{
        	$this->g_status = '1';
        }
	}

	public function checkG($g_id){
		$rs = $this->model->checkGroup($this->cookie['userID'], $g_id);
		if (!empty($rs)) {
            $this->msg = '已加入该星球！';
            $this->g_status = '1';
            // return $this->msg;
        }else{
        	$this->g_status = '0';
        	$this->msg = '未加入该星球！';
        }
	}

	public function checkStatus(){
		$config = array('crypt' => new Api_Cookie(), 'key' => 'a secrect');
		DI()->cookie = new PhalApi_Cookie_Multi($config);
		$this->cookie['userID']   = DI()->cookie->get('userID');
		$this->cookie['nickname'] = DI()->cookie->get('nickname');

		if (empty($this->cookie['userID'])) {
			$this->msg = '用户尚未登录！';
			$this->u_status = '0';
			// return $this->msg;
		}else{
			$this->u_status = '1';
			$this->msg = '用户已登录！';
		}
		
	}

	public function create($g_name){
		$this->model = new Model_Group();
		$this->checkStatus();
		$this->checkN($g_name);
	}

	public function join($g_id){
		$this->model = new Model_Group();
		$this->checkStatus();
		$this->checkG($g_id);
	}

	public function out(){
		DI()->cookie->delete('userID');
		DI()->cookie->delete('nickname');
	}

	public function lists($page,$pages){
		$this->model  = new Model_Group();
		$all_num      = $this->model->getAllNum();				//总条
		$page_num     =empty($pages)?20:$pages;					//每页条数
		$page_all_num =ceil($all_num/$page_num);				//总页数
		$page         =empty($page)?1:$page;					//当前页数
		$page         =(int)$page;								//安全强制转换
		$limit_st     =($page-1)*$page_num;						//起始数
		return $this->model->lists($limit_st, $page_num);
	}


}




 ?>