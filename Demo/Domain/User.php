<?php

class Domain_User {

    /*
    µÇÂ¼¼ì²é

    */

    public function login($data){
        $model = new Model_User();
        $rs = $model->login($data);
        return $rs;
    }
    /*
    ×¢²á¼ì²é

    */

    public function reg($data){
        $model = new Model_User();
        $rs = $model->reg($data);
        return $rs;
    }
    /*
	×¢Ïú¼ì²é

    */
    public function logout(){
        $model = new Model_User();
        $rs = $model->logout();
        return $rs;
    }

}



