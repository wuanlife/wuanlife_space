<?php 

class Domain_Group {

	public $msg   = '';
	public $model = '';
	public $cookie = array();
	public $status = '0';

	public function checkN($g_name){
		if (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u', $g_name)) {
			$this->msg = '小组名只能为中文、英文、数字或者下划线，但不得超过20字节！';
			return $this->msg;
		}

        if (!empty($this->model->checkName($g_name))) {
            $this->msg = '该星球已创建！';
            return $this->msg;
        }
	}

	public function checkG($g_id){
		if (!empty($this->model->checkGroup($this->cookie['userID'], $g_id))) {
            $this->msg = '已加入该星球！';
            return $this->msg;
        }else{
        	$this->status = '1';
        }
	}

	public function checkStatus(){
		$this->cookie['userID']   = DI()->cookie->get('userID');
		$this->cookie['nickname'] = DI()->cookie->get('nickname');

		if (empty($this->cookie['userID'])) {
			$this->msg = '用户尚未登录！';
			return $this->msg;
		}else{
			$this->status = '1';
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