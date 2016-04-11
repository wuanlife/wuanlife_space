<?php

class Model_User extends PhalApi_Model_NotORM {
	protected function getTableName($id){
	return 'user_base';}
	public function login($data) 
	{
		$sql =DI()->notorm->user_base->select('id')->where('Email = ?',$data['Email'])->fetch();
		if(empty($sql))
		{
		$this->code = '0';
		$this->msg = '该邮箱尚未注册！';
		}
		else{
			   $rs =DI()->notorm->user_base->select('*')->where('Email = ?',$data['Email'])->fetch();
			   if($rs['password']!=md5($data['password']))
			   {
			   $this->code = '0';
			   $this->msg = '密码错误，请重试！';
			   }
			   else
			   {
				$this->info = array('userID' => $rs['id'], 'nickname' => $rs['nickname'], 'Email' => $rs['Email']);
				$this->code ='1';
				$this->msg = '登录成功！';
				$config = array('crypt' => new Domain_Crypt(), 'key' => 'a secrect');
                DI()->cookie = new PhalApi_Cookie_Multi($config);
				$nickname = DI()->cookie->get('nickname');
				DI()->cookie->set('nickname', $rs['nickname'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$userID = DI()->cookie->get('userID');
				DI()->cookie->set('userID', $rs['id'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$Email = DI()->cookie->get('Email');
				DI()->cookie->set('Email', $rs['Email'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				}
			   
	    
		    }
		return $this;
	}
	public function reg($data) 
	{
		$sql =DI()->notorm->user_base->select('id')->where('Email = ?',$data['Email'])->fetch();
		if(!empty($sql)){
		$this->code = '0';
		$this->msg = '该邮箱已注册！';
		}
		else{
			   $sql =DI()->notorm->user_base->select('id')->where('nickname = ?',$data['nickname'])->fetch();
		       if(!empty($sql))
			   {
		       $this->code = '0';
		       $this->msg = '该昵称已注册！';
		       }
			    else
				{
				$data['password'] = md5($data['password']);
			    $rs=DI()->notorm->user_base->insert($data);
			    $this->info = array('userID' => $rs['id'], 'nickname' => $rs['nickname'], 'Email' => $rs['Email']);
				$this->code ='1';
				$this->msg = '注册成功，并自动登录！';
				$config = array('crypt' => new Domain_Crypt(), 'key' => 'a secrect');
                DI()->cookie = new PhalApi_Cookie_Multi($config);
				$nickname = DI()->cookie->get('nickname');
				DI()->cookie->set('nickname', $rs['nickname'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$userID = DI()->cookie->get('userID');
				DI()->cookie->set('userID', $rs['id'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$Email = DI()->cookie->get('Email');
				DI()->cookie->set('Email', $rs['Email'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				}
		    }
        return $this;
	}
	public function logout()
	{
		$config = array('crypt' => new Domain_Crypt(), 'key' => 'a secrect');
		DI()->cookie = new PhalApi_Cookie_Multi($config);
		$nickname = DI()->cookie->get('nickname');
		$userID = DI()->cookie->get('userID');
		$Email = DI()->cookie->get('Email');
		if(empty($nickname&&$userID&&$Email)){
			$this->code ='0';
			$this->msg = '未登录，无需注销！';
		}
		else{
			DI()->cookie->delete('nickname');
			DI()->cookie->delete('userID');
			DI()->cookie->delete('Email');
			$this->code ='1';
			$this->msg = '注销成功！';
		}
		return $this;
	}
}	