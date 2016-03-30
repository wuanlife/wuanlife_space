<?php 
/**
* 星球接口类
*/
class Api_Group extends PhalApi_Api
{
	public function getRules(){
		return array(
			'create' => array(
				'name'    => array(
					'name'    => 'name',
					'type'    => 'string',
					'require' => true,
					'min'     => '1',
					'max'     => '20',
					'desc'    => '星球名称',
				),
			),

			'join' => array(
				'groupID' => array(
					'name' => 'groupID',
					'type' => 'int',
					'require' => true,
					'min' => '1',
					'desc' => '星球ID',
				),
			),
		);
	}

	/**
	 * 星球创建接口
	 * @desc 用于创建星球
	 * @return int code 操作码，1表示创建成功，0表示创建失败
	 * @return object info 星球信息对象
	 * @return int info.id 星球ID
	 * @return string info.name 星球名称
	 * @return string msg 提示信息
	 */
	public function create(){
		$rs = array(
			'code' => 0, 
			'msg'  => '', 
			'info' => array(),
		);

		$data = array(
			'name' => $this->name,
		);

		$domain = new Domain_Group();
		$rs['msg'] = $domain->add($data);

		if (empty($rs['msg'])) {
			$result = DI()->notorm->group_base->insert($data);
			$data2 = array(
				'id'     => $result['id'],
				'userID' => DI()->cookie->get('userID'),
			);
			$result2 = DI()->notorm->group_detail->insert($data2);
			$rs['info'] = $result;
			$rs['info']['userID'] = $result2['userID'];
			$rs['code'] = 1;
			$rs['msg']  = '星球创建成功';
		}

		return $rs;
	}

	/**
	 * 加入星球接口
	 * @desc 用户加入星球
 	 * @return int code 操作码，1表示加入成功，0表示加入失败
 	 * @return string msg 提示信息
	 */
	public function join(){
		$rs = array(
			'code' => 0, 
			'msg'  => '', 
		);

		$domain = new Domain_Group();
		$rs['msg'] = $model->checkStatus();

		if (empty($rs['msg'])) {
			$data = array(
				'id'     => $this->groupID,
				'userID' =>DI()->cookie->get('userID'),
			);

			$result = DI()->notorm->group_detail->insert($data);
			$rs['code'] = 1;
			$rs['msg']  = '星球创建成功';
		}

		return $rs;
	}

	/**
	 * 判断登陆状态
	 * @desc 判断是否登录
	 * @return int code 操作码，1表示已登录，0表示未登录
	 * @return object info 状态信息对象
	 * @return int info.id 用户ID
	 * @return string info.nickname 用户昵称
	 */
	public function status(){
		$rs = array(
			'code' => 0, 
			'msg'  => '', 
			'info' => array(),
		);
		$domain = new Domain_Group();
		$rs['msg'] = $domain->checkStatus();

		if (empty($rs['msg'])) {
			$rs['info']['userID'] = DI()->cookie->get('userID');
			$rs['info']['nickname'] = DI()->cookie->get('nickname');
			$rs['code'] = 1;
			$rs['msg']  = '用户已登录！';
		}

		return $rs;
	}

	public function setc(){
		DI()->cookie->set('userID','12', $_SERVER['REQUEST_TIME'] + 60);
	}
	
}




 ?>