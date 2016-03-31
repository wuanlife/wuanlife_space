<?php 

class Model_Login extends PhalApi_Model_NotORM {

	protected function getTableName($Email){
		return 'base';
	}
	/*public function checkNickname ($nickname){
        return $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();
	}*/
	public function checkEmail ($Email){
		return $this->getORM()->select('Email')->where('Email = ?', $Email)->fetchOne();
	}
	public function checkPassword($Email){
		return $this->getORM()->select('password')->where('Email = ?', $Email)->fetchOne();
	}
}


 