<?php
/**
 * 数据库接口服务类
 */
class Api_DB extends PhalApi_Api{
    public function getRules(){
        return array(
            'insert' => array(
                'id'    => array('name' => 'id', 'require' => true, 'desc' => '用户Id'),
                'name'  => array('name' => 'name', 'require' => true, 'desc' => '用户名称'),
                'phone' => array('name' => 'phone', 'require' => true, 'desc' => '用户手机号码'),
            ),
            'select' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => '用户Id'),
            ),
            'update' => array(
                'id'    => array('name' => 'id', 'require' => true, 'desc' => '用户Id'),
                'name'  => array('name' => 'name', 'require' => true, 'desc' => '用户名称'),
                'phone' => array('name' => 'phone', 'require' => true, 'desc' => '用户手机号码'),
            ),
            'delete' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => '用户Id'),
            ),
        );
    }
	/**
 * 查询
 * @return array data 结果集
 */
public function select(){
    $data   = array();
    $data[] = DI()->notorm->base->select('nickname,email')->where('id', $this->id)->fetch();
    $data[] = DI()->notorm->base->select('nickname,email')->where('id = ?', $this->id)->fetchAll();
    $data[] = DI()->notorm->base->select('nickname,email')->where('id != ?', $this->id)->fetchRows();
    return $data;
}
}