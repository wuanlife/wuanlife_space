<?php 

class Domain_Group {
	public $rs = array(
			'code' => 0, 
			'msg'  => '', 
			'info' => array(),
			);
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
		$config = array('crypt' => new Domain_Crypt(), 'key' => 'a secrect');
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

	public function create($data){
		$g_name = $data['name'];
		$this->model = new Model_Group();
		$this->checkStatus();
		$this->checkN($g_name);

		if ($this->u_status == '1' && $this->g_status =='1') {
			$result = DI()->notorm->group_base->insert($data);
			// $result = $this->model->add(group_base,$data);
			$data2 = array(
				'group_base_id' => $result['id'],
				'user_base_id'  => $this->cookie['userID'], 
			);
			$result2 = DI()->notorm->group_detail->insert($data2);
			// $result2 = $this->model->add(group_detail,$data2);
			$this->rs['info'] = $result2;
			$this->rs['info']['name'] = $result['name'];
			$this->rs['code'] = 1;
		}else{
			$this->rs['msg'] = $this->msg;
		}

		return $this->rs;
	}

	public function join($g_id){
		$this->model = new Model_Group();
		$this->checkStatus();
		$this->checkG($g_id);

		if ($this->u_status == '1' && $this->g_status == '0') {
			$data = array(
				'group_base_id' => $g_id,
				'user_base_id'  => $this->cookie['userID'],
			);

			$result = DI()->notorm->group_detail->insert($data);
			$this->rs['info'] = $result;
			$this->rs['code'] = 1;
		}else{
			$this->rs['msg'] = $this->msg;
		}

		return $this->rs;
	}

	public function uStatus(){
		$this->checkStatus();

		if ($this->u_status == '1') {
			$this->rs['info'] = $this->cookie;
			$this->rs['code'] = 1;
		}else{
			$this->rs['msg'] = $this->msg;
		}

		return $this->rs;
	}

	public function gStatus($g_id){
		$this->model = new Model_Group();
		$this->checkStatus();
		$this->checkG($g_id);

		if ($this->g_status == '1') {			
			$this->rs['code'] = 1;
			$this->rs['msg']  = $this->msg;
		}else{
			$this->rs['code'] = 0;
			$this->rs['msg']  = $this->msg;
		}

		return $this->rs;
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

	public function posts($data){
		$this->model = new Model_Group();
		$this->checkStatus();
		$this->checkG($data['group_base_id']);

		if ($this->u_status == '1' && $this->g_status == '1') {
			$b_data = array(
				'user_base_id'  => $this->cookie['userID'],
				'group_base_id' => $data['group_base_id'],
				'title'         => $data['title'],
			);
			$pb = DI()->notorm->post_base->insert($b_data);
			$time = date('Y-m-d H:i:s',time());
			$d_data = array(
				'post_base_id' => $pb['id'],
				'user_base_id' => $this->cookie['userID'],
				'text' => $data['text'],
				'floor'=> '1',
				'createTime' => $time,
			);
			$pd = DI()->notorm->post_detail->insert($d_data);
			$this->rs['code'] = 1;
			$this->rs['info'] = $pd;
			$this->rs['info']['title']=$pb['title'];
		}else{
			$this->rs['msg'] = $this->msg;
		}

		return $this->rs;
	}


}




 ?>