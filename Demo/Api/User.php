<?php 
/**
 * 登录注册服务类
 */

class Api_User extends PhalApi_Api{

	public function getRules(){
		return array(

			'login' => array(
				'Email'    => array(
					'name'    => 'Email', 
					'type'    => 'string', 
					'require' => true,
					'desc'    => '用户邮箱'     
				),

				'password' => array(
					'name'    => 'password', 
					'type'    => 'string', 
					'require' => true,
					'desc'    => '用户密码'
				),
			),
			'reg' => array(
				'nickname' => array(
					'name'    => 'nickname', 
					'type'    => 'string', 
					'require' => true,
					'desc'    => '用户昵称'
				),

				'Email'    => array(
					'name'    => 'Email', 
					'type'    => 'string', 
					'require' => true,
					'desc'    => '用户邮箱'     
				),

				'password' => array(
					'name'    => 'password', 
					'type'    => 'string', 
					'require' => true,
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
		$rs = array('code' => '', 'msg' => '','info' => array());
		$data = array(
			'Email'    => $this->Email,
			'password' => $this->password,
			);
		$domain = new Domain_User();
		$rs = $domain->login($data);
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
		$rs = array('code' => '', 'msg' => '', 'info' => array());
		$data = array(
			'nickname' => $this->nickname,
			'Email'    => $this->Email,
			'password' => $this->password,
		    );
		$domain = new Domain_User();
		$rs = $domain->reg($data);
		return $rs;

	}
/**
 * 注销接口
 * @desc 用于清除用户登录状态
 */
	public function logout() {
		$rs = array('code' => '', 'msg' => '');
        $domain = new Domain_User();
        $rs = $domain->logout();
        return $rs;
	}
}