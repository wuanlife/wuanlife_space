<?php 

class Model_Login extends PhalApi_Model_NotORM {
	protected function getTableName($id){
		return 'user_base';
	}
	/*public function checkNickname ($nickname){
        return $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();
	}保留对昵称的检查，方便以后调用*/
	public function checkEmail ($Email){
		return $this->getORM()->select('id')->where('Email = ?', $Email)->fetchOne();
	}
	public function checkPassword ($password){
		return $this->getORM()->select('id')->where('password = ?', $password)->fetchOne();
	}


}


 