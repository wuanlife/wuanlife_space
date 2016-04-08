<?php 

class Domain_User {

    /*
	µÇÂ¼¼ì²é
	
	*/

    public function login($data){
        $this->model    = new Model_User();
        //$this->nickname = $data['nickname'];
        $this->Email    = $data['Email'];
        $this->password = $data['password'];
		$model = new Model_User();
        $rs = $model->login($data);
        return $rs;
	}
    /*
	×¢²á¼ì²é
	
	*/
	
    public function reg($data){
		$this->model    = new Model_User();
        $this->nickname = $data['nickname'];
        $this->Email    = $data['Email'];
        $this->password = $data['password'];
		$model = new Model_User();
        $rs = $model->reg($data);
        return $rs;
    }

}



 