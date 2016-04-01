<?php 

class Model_Login extends PhalApi_Model_NotORM {
	protected function getTableName($id){
		return 'base';
	}
	/*public function checkNickname ($nickname){
        return $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();
	}*/
	public function checkEmail ($Email){
		return $this->getORM()->select('id')->where('Email = ?', $Email)->fetchOne();
	}
	public function checkPassword ($password){
		return $this->getORM()->select('id')->where('Email = ?', $password)->fetchOne();
	}


}


 