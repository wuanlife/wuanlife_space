<?php 

class Domain_User {

    public $msg      = '';
    public $nickname = '';//保留对昵称的检查，方便以后调用
    public $Email    = '';
    public $password = '';
    public $model    = '';
    /*
	登录检查
	
	*/
	/*public function checkNi($nickname){
		if (!preg_match('/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]{1,16}+$/u', $nickname)) {
        	$this->msg = '昵称只能为中文、英文、数字，不得超过16位！';
    	} 
        if (empty($this->model->checkNickname($nickname))) {
            $this->msg = '该昵称尚未注册！';
        }
    }*/

    public function checkE($Email){
       
       /* if (!preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $Email)) {
            $this->msg = '邮箱地址格式错误';
        } */
        $rs = $this->model->checkEmail($Email);
        if (empty($rs)) {
            $this->msg = '该邮箱尚未注册！';
        }
    }

    public function checkPwd($password){

       /* if (!preg_match('/^[\s|\S]{6,50}$/u', $password)) {
            $this->msg = '密码长度为6-18位！';
        } */
        $rs = $this->model->checkPassword($password);
		if (empty($rs)) {
            $this->msg = '密码不正确，请重新输入！';
        }
    }

    public function request($data){
        $this->model    = new Model_User();
        //$this->nickname = $data['nickname'];
        $this->Email    = $data['Email'];
        $this->password = $data['password'];
    }

    public function login($data){
        $this->request($data);
        //$this->checkNi($this->nickname);
        $this->checkE($this->Email);
        
        if (empty($this->msg)) {
        $this->checkPwd($this->password);
    }

        return $this->msg;

    }


    /*
	注册检查
	
	*/
	public function checkNi1($nickname){
        $rs = $this->model->checkNickname1($nickname);

		if (!preg_match('/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]{1,16}+$/u', $nickname)) {
        	$this->msg = '昵称只能为中文、英文、数字，不得超过16位！';
    	} 

        if (!empty($rs)) {
            $this->msg = '该昵称已占用';
        }
    }

    public function checkE1($Email){
        $rs = $this->model->checkEmail1($Email);
       
        if (!preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $Email)) {
            $this->msg = '邮箱地址格式错误';
        } 

        if (!empty($rs)) {
            $this->msg = '该邮箱已占用';
        }
    }

    public function checkPwd1($password){

        if (!preg_match('/^[\s|\S]{6,18}$/u', $password)) {
            $this->msg = '密码长度为6-18位！';
        } 
    }

    public function request1($data1){
        $this->model    = new Model_User();
        $this->nickname = $data1['nickname'];
        $this->Email    = $data1['Email'];
        $this->password = $data1['password'];
    }

    public function reg($data1){
        $this->request1($data1);
        $this->checkNi1($this->nickname);
        $this->checkE1($this->Email);
        $this->checkPwd1($this->password);

        return $this->msg;

    }

}



 