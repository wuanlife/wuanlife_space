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
					'default' => 1,
					'desc' => '当前页面',
				),
			),

			'posts' => array(
				'g_id' => array(
					'name' => 'group_base_id',
					'type' => 'int',
					'min' => '1',
					'require' => true,
					'desc' => '发帖星球',
				),
				'title' => array(
					'name' => 'title',
					'type' => 'string',
					'min' => '1',
					'require' => true,
					'desc' => '帖子标题',
				),
				'text' => array(
					'name' => 'text',
					'type' => 'string',
					'min' => '1',
					'require' => true,
					'desc' => '帖子正文',
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
		$rs = array();

		$data = array(
			'name' => $this->g_name,
		);

		$domain = new Domain_Group();
		$rs = $domain->create($data);


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
		$rs = array();

		$domain = new Domain_Group();
		$rs = $domain->join($this->g_id);

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
		$rs = array();

		$domain = new Domain_Group();
		$rs = $domain->uStatus();

		return $rs;
	}

	/**
	 * 判断用户是否加入该星球
	 * @desc 判断用户是否加入该星球
	 * @return int code 操作码，1表示已加入，0表示未加入
	 * @return string msg 提示信息
	 */
	public function gStatus(){
		$rs = array();

		$domain = new Domain_Group();
		$rs = $domain->gStatus($this->g_id);

		return $rs;
	}

	/**
	 * 登出接口
	 * @desc 注销
	 * @return_ int code 操作码，1表示注销成功，0表示注销失败
	 */
	public function loginOut(){

		$domain = new Domain_Group();
		$domain->out();
	}

	/**
	 * 星球列表
	 * @desc 按成员数降序显示星球列表
	 * @return int lists 星球列表对象
	 * @return int lists.name 星球名称
	 * @return int lists.id 星球ID
	 * @return int lists.num 星球成员数
	 */
	public function lists(){
		$rs = array(
			'lists'  => array(),
			);
		$pages = 20;				//每页数量
		$domain = new Domain_Group();
		$rs['lists'] =  $domain->lists($this->page, $pages);
		return $rs;
	}

	/**
	 * 帖子发布
	 * @desc 星球帖子发布
	 * @return int code 操作码，1表示发布成功，0表示发布失败
 	 * @return object info 帖子信息对象
	 * @return int info.group_base_id 帖子所属星球ID
	 * @return int info.post_base_id 帖子ID
	 * @return string info.text 帖子正文
	 * @return int info.floor 帖子楼层
	 * @return string info.createTime 帖子发布时间
	 * @return string info.title 帖子标题
 	 * @return string msg 提示信息
	 */
	public function posts(){
		$rs = array();
		$data = array(
				'group_base_id' => $this->g_id,
				'title'         => $this->title,
				'text'          => $this->text,
			);

		$domain = new Domain_Group();
		$rs = $domain->posts($data);

		return $rs;
	}
	
}




 ?>