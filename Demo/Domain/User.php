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

    public function logincheckE($Email){
       
       /* if (!preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $Email)) {
            $this->msg = '邮箱地址格式错误';
        } */
           $checkEmail=$this->model->logincheckEmail($Email);
        if (empty($checkEmail)) {
            $this->msg = '该邮箱尚未注册！';
        }
    }

    public function logincheckPwd($password){

       /* if (!preg_match('/^[\s|\S]{6,18}$/u', $password)) {
            $this->msg = '密码长度为6-18位！';
        } */
		$checkPassword=$this->model->logincheckPassword($password);
		if (empty($checkPassword)) {
            $this->msg = '密码不正确，请重新输入！';
        }
    }

    public function loginrequest($data){
        $this->model    = new Model_User();
        //$this->nickname = $data['nickname'];
        $this->Email    = $data['Email'];
        $this->password = $data['password'];
    }

    public function login($data){
        $this->loginrequest($data);
        //$this->checkNi($this->nickname);
        $this->logincheckE($this->Email);
        if (empty($this->msg)) {
        $this->logincheckPwd($this->password);
    }

        return $this->msg;

    }


    /*
	注册检查
	
	*/
	public function regcheckNi($nickname){

		if (!preg_match('/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]{1,16}+$/u', $nickname)) {
        	$this->msg = '昵称只能为中文、英文、数字，不得超过16位！';
    	} 
		$checkNickname=$this->model->regcheckNickname($nickname);
        if (!empty($checkNickname)) {
            $this->msg = '该昵称已占用';
        }
    }

    public function regcheckE($Email){
       
        if (!preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $Email)) {
            $this->msg = '邮箱地址格式错误';
        } 
		$checkEmail=$this->model->regcheckEmail($Email);
        if (!empty($checkEmail)) {
            $this->msg = '该邮箱已占用';
        }
    }

    public function regcheckPwd($password){

        if (!preg_match('/^[\s|\S]{6,18}$/u', $password)) {
            $this->msg = '密码长度为6-18位！';
        } 
    }

    public function regrequest($data){
        $this->model    = new Model_User();
        $this->nickname = $data['nickname'];
        $this->Email    = $data['Email'];
        $this->password = $data['password'];
    }

    public function reg($data){
        $this->regrequest($data);
        $this->regcheckNi($this->nickname);
        $this->regcheckE($this->Email);
        $this->regcheckPwd($this->password);

        return $this->msg;

    }

}



 