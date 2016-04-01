<?php 

class Domain_Login {

    public $msg      = '';
    //public $nickname = '';保留对昵称的检查，方便以后调用
    public $Email    = '';
    public $password = '';
    public $model    = '';

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

        if (empty($this->model->checkEmail($Email))) {
            $this->msg = '该邮箱尚未注册！';
        }
    }

    public function checkPwd($password){

       /* if (!preg_match('/^[\s|\S]{6,50}$/u', $password)) {
            $this->msg = '密码长度为6-18位！';
        } */
		if (empty($this->model->checkPassword($password))) {
            $this->msg = '密码不正确，请重新输入！';
        }
    }

    public function request($data){
        $this->model    = new Model_Login();
        //$this->nickname = $data['nickname'];
        $this->Email    = $data['Email'];
        $this->password = $data['password'];
    }

    public function login($data){
        $this->request($data);
        //$this->checkNi($this->nickname);
        $this->checkE($this->Email);
        $this->checkPwd($this->password);

        return $this->msg;

    }

}



 