<?php 

class Model_Reg extends PhalApi_Model_NotORM {
    protected function getTableName($id){
		return 'user_base';
	}
	public function checkNickname ($nickname){
        return $this->getORM()->select('id')->where('nickname = ?', $nickname)->fetchOne();
	}
	public function checkEmail ($Email){
		return $this->getORM()->select('id')->where('Email = ?', $Email)->fetchOne();
	}

                                              }


 