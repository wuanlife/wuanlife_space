<?php 

class Model_Check extends PhalApi_Model_NotORM {

	// public $rs = array('code' => 0, 'msg' => '', 'info' => array());
	// public $nickname = $data['nickname'];
 //    public $Email    = $data['Email'];
 //    public $password = $data['password'];

	protected function getTableName($id){
		return 'base';
	}

	public function checkNickname ($nickname){

        // if (!preg_match('/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]{1,16}+$/u', $nickname)) {
        //     $rs['msg'] = '昵称只能为中文、英文、数字，不得超过16位！';
        //     return $rs;
        // }

        // $info = $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();

        // if (!empty($info)) {
        // 	$rs['code'] = 1;
        // 	$rs['msg'] = '该昵称已存在';
        // 	$rs['info'] = $info;
        // 	return $rs;
        // } else {
        // 	$rs['code'] = 2;
        // 	return $rs;
        // }
        
        return $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();
	}

	public function checkEmail ($Email){
		return $this->getORM()->select('id')->where('Email = ?', $Email)->fetchOne();
	}


}


 