<?php 
/**
 * 登录注册服务类
 */

class Api_User extends PhalApi_Api{

	public function getRules(){
		return array(

			'login' => array(
				/*'nickname' => array(
					'name'    => 'nickname', 
					'type'    => 'string', 
					'require' => true, 
					'min'     => '1', 
					'max'     => '16', 
					'desc'    => '用户昵称'
				),*/

				'Email'    => array(
					'name'    => 'Email', 
					'type'    => 'string', 
					'require' => true, 
					'min'     => '1', 
					'max'     => '100', 
					'desc'    => '用户邮箱'     
				),

				'password' => array(
					'name'    => 'password', 
					'type'    => 'string', 
					'require' => true, 
					'min'     => '1', 
					'max'     => '100', 
					'desc'    => '用户密码'
				),
			),
			'reg' => array(
				'nickname' => array(
					'name'    => 'nickname', 
					'type'    => 'string', 
					'require' => true, 
					'min'     => '1', 
					'max'     => '100', 
					'desc'    => '用户昵称'
				),

				'Email'    => array(
					'name'    => 'Email', 
					'type'    => 'string', 
					'require' => true, 
					'min'     => '1', 
					'max'     => '100', 
					'desc'    => '用户邮箱'     
				),

				'password' => array(
					'name'    => 'password', 
					'type'    => 'string', 
					'require' => true, 
					'min'     => '1', 
					'max'     => '100', 
					'desc'    => '用户密码'
				),
			),
            
		);
	}

/**
 * 登录接口
 * @desc 用于验证并登录用户
 * @return int code 操作码，1表示登录成功，0表示登录失败
 * @return object info 用户信息对象
 * @return int info.id 用户ID
 * @return string info.nickname 用户昵称
 * @return string msg 提示信息
 * 
 */
	public function login(){

		$rs = array('code' => 0, 'msg' => '','info' => array());
		$data = array(
			//'nickname' => $this->nickname,
			'Email'    => $this->Email,
			'password' => $this->password,
		
		);
        
		$data['password'] = md5($data['password']);
		$domain = new Domain_User();
		$rs['msg'] = $domain->login($data);

		if (empty($rs['msg'])) {
            
			$result = DI()->notorm->user_base->select('Email,nickname,id')->where('Email',$data['Email'])->fetch();
			if (!empty($result['Email'])) {
				$rs['info'] = array('userID' => $result['id'], 'nickname' => $result['nickname'], 'Email' => $result['Email']);
				$rs['code'] = 1;
				$rs['msg'] = '登录成功！';
				$nickname = DI()->cookie->get('nickname');
				$config = array('crypt' => new Api_Cookie(), 'key' => 'a secrect');
                DI()->cookie = new PhalApi_Cookie_Multi($config);
				DI()->cookie->set('nickname', $result['nickname'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$userID = DI()->cookie->get('userID');
				DI()->cookie->set('userID', $result['id'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$Email = DI()->cookie->get('Email');
				DI()->cookie->set('Email', $result['Email'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
			}

		}
		

		return $rs;

	}

/**
 * 注册接口
 * @desc 用于验证并注册用户
 * @return int code 操作码，1表示注册成功，0表示注册失败
 * @return object info 用户信息对象
 * @return int info.id 用户ID
 * @return string info.nickname 用户昵称
 * @return string msg 提示信息
 * 
 */
	public function reg(){

		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$data = array(
			'nickname' => $this->nickname,
			'Email'    => $this->Email,
			'password' => $this->password,
		);

		$domain = new Domain_User();
		$rs['msg'] = $domain->reg($data);

		if (empty($rs['msg'])) {
			$data['password'] = md5($data['password']);
			$result = DI()->notorm->user_base->insert($data);

			if (!empty($result['id'])) {
				$rs['info'] = array('userID' => $result['id'], 'nickname' => $result['nickname'], 'Email' => $result['Email']);
				$rs['code'] = 1;
				$rs['msg'] = '注册成功，并自动登录！';
				$config = array('crypt' => new Api_Cookie(), 'key' => 'a secrect');
                DI()->cookie = new PhalApi_Cookie_Multi($config);
				$nickname = DI()->cookie->get('nickname');
				DI()->cookie->set('nickname', $result['nickname'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$userID = DI()->cookie->get('userID');
				DI()->cookie->set('userID', $result['id'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
				$Email = DI()->cookie->get('Email');
				DI()->cookie->set('Email', $result['Email'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
			}

		}

		return $rs;

	}




}
