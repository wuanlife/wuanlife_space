<?php 

class Domain_Group {

	public $msg   = '';
	public $name  = '';
	public $model = '';

	public function checkN($name){
		if (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u', $name)) {
			$this->msg = '小组名只能为中文、英文、数字或者下划线，但不得超过20字节！';
			return $this->msg;
		}

        if (!empty($this->model->checkName($name))) {
            $this->msg = '该星球已创建！';
            return $this->msg;
        }
	}

	public function checkStatus(){
		$userID = DI()->cookie->get('userID');

		if (empty($userID)) {
			$this->msg = '用户尚未登录！';
			return $this->msg;
		}
	}

	public function check(){
		$this->checkStatus();
		$this->checkN($this->name);
	}

	public function request($data){
		$this->model = new Model_Group();
		$this->name = $data['name'];
	}

	public function add($data){
		$this->request($data);
		$this->check();

		return $this->msg;
	}
}




 ?>