<?php 
/**
* 星球接口类
*/
class Api_Group extends PhalApi_Api
{
	public function getRules(){
		return array(
			'create' => array(
				'g_name'    => array(
					'name'    => 'name',
					'type'    => 'string',
					'require' => true,
					'min'     => '1',
					'max'     => '20',
					'desc'    => '星球名称',
				),
			),

			'join' => array(
				'g_id' => array(
					'name' => 'group_base_id',
					'type' => 'int',
					'require' => true,
					'min' => '1',
					'desc' => '星球ID',
				),
			),

			'gStatus' => array(
				'g_id' => array(
					'name' => 'group_base_id',
					'type' => 'int',
					'require' => true,
					'min' => '1',
					'desc' => '星球ID',
				),
			),

			'lists' => array(
				'page' => array(
					'name' => 'page',
					'type' => 'int',
					'require' => false,
					'desc' => '当前页面，空则为第一页',
				),
				'pages' => array(
					'name' => 'pages',
					'type' => 'int',
					'require' => false,
					'desc' => '每页数量，空则每页20条',
				),
			),
		);
	}

	/**
	 * 星球创建接口
	 * @desc 用于创建星球
	 * @return int code 操作码，1表示创建成功，0表示创建失败
	 * @return object info 星球信息对象
	 * @return int info.group_base_id 星球ID
	 * @return string info.user_base_id 创建者ID
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
			'name' => $this->g_name,
		);

		$domain = new Domain_Group();
		$domain->create($this->g_name);

		if ($domain->msg == '') {
			$result = DI()->notorm->group_base->insert($data);
			$data2 = array(
				'group_base_id' => $result['id'],
				'user_base_id'  => $domain->cookie['userID'], 
			);
			$result2 = DI()->notorm->group_detail->insert($data2);
			$rs['info'] = $result2;
			$rs['info']['name'] = $result['name'];
			$rs['code'] = 1;
		}else{
			$rs['msg'] = $domain->msg;
		}

		return $rs;
	}

	/**
	 * 加入星球接口
	 * @desc 用户加入星球
 	 * @return int code 操作码，1表示加入成功，0表示加入失败
 	 * @return object info 星球信息对象
	 * @return int info.group_base_id 加入星球ID
	 * @return string info.user_base_id 加入者ID
 	 * @return string msg 提示信息
	 */
	public function join(){
		$rs = array(
			'code' => 0, 
			'info' => array(),
			'msg'  => '', 
		);

		$domain = new Domain_Group();
		$domain->join($this->g_id);

		if ($domain->msg == '') {
			$data = array(
				'group_base_id' => $this->g_id,
				'user_base_id'  => $domain->cookie['userID'],
			);

			$result = DI()->notorm->group_detail->insert($data);
			$rs['info'] = $result;
			$rs['code'] = 1;
		}else{
			$rs['msg'] = $domain->msg;
		}

		return $rs;
	}

	/**
	 * 判断用户登陆状态
	 * @desc 判断是否登录
	 * @return int code 操作码，1表示已登录，0表示未登录
	 * @return object info 状态信息对象
	 * @return int info.id 用户ID
	 * @return string info.nickname 用户昵称
	 * @return string msg 提示信息
	 */
	public function uStatus(){
		$rs = array(
			'code' => 0, 
			'msg'  => '', 
			'info' => array(),
		);
		$domain = new Domain_Group();
		$domain->checkStatus();

		if ($domain->status == '1') {
			$rs['info'] = $domain->cookie;
			$rs['code'] = 1;
		}else{
			$rs['msg'] = $domain->msg;
		}

		return $rs;
	}

	/**
	 * 判断用户是否加入该星球
	 * @desc 判断是否登录
	 * @return int code 操作码，1表示已加入，0表示未加入
	 * @return string msg 提示信息
	 */
	public function gStatus(){
		$rs = array(
			'code' => 1, 
			'msg'  => '',
		);

		$domain = new Domain_Group();
		$domain->join($this->g_id);

		if ($domain->msg == '') {			
			$rs['code'] = 0;
		}else{
			$rs['code'] = 1;
			$rs['msg']  = $domain->msg;
		}

		return $rs;
	}

	/**
	 * 登出接口
	 * @desc 注销
	 * @return_ int code 操作码，1表示注销成功，0表示注销失败
	 */
	public function loginOut(){
		// $rs = array(
		// 	'code' => 1, 
		// 	'msg'  => '', 
		// );

		$domain = new Domain_Group();
		$domain->out();

		// if ($domain->status == '1') {
		// 	$rs['code'] = 0;
		// }

		// return $rs;
	}

	/**
	 * 星球列表
	 * @return int lists 星球列表对象
	 * @return int lists.name 星球名称
	 * @return int lists.id 星球ID
	 * @return int lists.num 星球成员数
	 */
	public function lists(){
		$rs = array(
			'lists'  => array(),
		);

		$domain = new Domain_Group();
		$rs['lists'] =  $domain->lists($this->page, $this->pages);
		return $rs;
	}

	// public function setc(){
	// 	DI()->cookie->set('userID','1212', $_SERVER['REQUEST_TIME'] + 600);
	// }
	
}




 ?>