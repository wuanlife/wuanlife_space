<?php 
/**
 * 登录服务类
 */

class Api_Login extends PhalApi_Api{

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
					/*'min'     => '1', 
					'max'     => '32', */
					'desc'    => '用户邮箱'     
				),

				'password' => array(
					'name'    => 'password', 
					'type'    => 'string', 
					'require' => true, 
					'min'     => '1', 
					'max'     => '18', 
					'desc'    => '用户密码'
				),
			),
			'select' => array(
                'id' => array('name' => 'id', 'desc' => '用户Id'),
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

		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$data = array(
			//'nickname' => $this->nickname,
			'Email'    => $this->Email,
			'password' => $this->password,
		);

		
		$domain = new Domain_Login();
		$rs['msg'] = $domain->login($data);

		if (empty($rs['msg'])) {
			$data['password'] = md5($data['password']);
			$result = DI()->notorm->base->select($data);
			if (!empty($result['Email'])) {
				$rs['info'] = array('userID' => $result['id'], 'nickname' => $result['nickname'], 'Email' => $result['Email']);
				$rs['code'] = 1;
				$rs['msg'] = '登录成功！';
			}

		}
		

		return $rs;

	}
     public function select(){
    $data   = array();
    $data[] = DI()->notorm->base->select('password')->fetchRows();
    return $data;
}




}





 