<?php 

class Model_User extends PhalApi_Model_NotORM {
	protected function getTableName($id){
		return 'user_base';
	}
	/*
	登录检查
	*/
	/*public function logincheckNickname ($nickname){
        return $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();
	}保留对昵称的检查，方便以后调用*/
	public function logincheckEmail ($Email){
		return $this->getORM()->select('id')->where('Email = ?', $Email)->fetchOne();
	}
	public function logincheckPassword ($password){
		return $this->getORM()->select('id')->where('password = ?', $password)->fetchOne();
	}
	/*
	注册检查
	*/
    public function regcheckNickname ($nickname){
        return $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();
	}
	public function regcheckEmail ($Email){
		return $this->getORM()->select('id')->where('Email = ?', $Email)->fetchOne();
	}

}


 