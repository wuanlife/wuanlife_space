<?php 
/**
 * 注册服务类
 */

class Api_Reg extends PhalApi_Api{

	public function getRules(){
		return array(

			'register' => array(
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
 * 注册接口
 * @desc 用于验证并注册用户
 * @return int code 操作码，1表示注册成功，0表示注册失败
 * @return object info 用户信息对象
 * @return int info.id 用户ID
 * @return string info.nickname 用户昵称
 * @return string msg 提示信息
 * 
 */
	public function register(){

		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$data = array(
			'nickname' => $this->nickname,
			'Email'    => $this->Email,
			'password' => $this->password,
		);

		$domain = new Domain_Reg();
		$rs['msg'] = $domain->reg($data);

		if (empty($rs['msg'])) {
			$data['password'] = md5($data['password']);
			$result = DI()->notorm->user_base->insert($data);

			if (!empty($result['id'])) {
				$rs['info'] = array('userID' => $result['id'], 'nickname' => $result['nickname'], 'Email' => $result['Email']);
				$rs['code'] = 1;
				$rs['msg'] = '注册成功！';
			}

		}

		return $rs;

	}




}





 