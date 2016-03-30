<?php

/**
 * 数据库接口服务类
 */
class Api_DB extends PhalApi_Api
{
    public function getRules()
    {
        return array(
            'insert' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
                'name' => array('name' => 'name', 'require' => true, 'desc' => '用户名称'),
                'note' => array('name' => 'note', 'require' => true, 'desc' => '用户日志'),
            ),
            'select' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            ),
            'update' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
                'name' => array('name' => 'name', 'require' => true, 'desc' => '用户名称'),
                'note' => array('name' => 'note', 'require' => true, 'desc' => '用户日志'),
            ),
            'delete' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            ),
        );
    }

    /**
     * 插入语句
     * @desc 用于插入
     * @return int id 新增列的id
     */
    public function insert()
    {
        $data = array(
            'id' => $this->id,
            'name' => $this->name,
            'note' => $this->note,
        );
        $result = DI()->notorm->user->insert($data);
        return $result['id'];

    }

    

    /**
     * 查询语句
     * @desc 用于查询
     * @return array data 结果集
     */
//     public function select(){
//         $data = array();
// //        $data[] = DI()->notorm->user->select('name, note')->where('id', $this->id)->fetch();
// //         $data[] = DI()->notorm->user->select('name, note')->where('id = ?', $this->id)->fetchAll();
//          $data[] = DI()->notorm->user->select('name, note')->where('id != ?', $this->id)->fetchRows();
//         return $data;
//     }

//    public function select(){
//        $model = new Model_DB();
//        return $model->getByUserId($this->id);
//    }

    public function select()
    {
        $model = new Model_DB();
        return $model->get($this->id);
    }

    /**
     * 修改语句
     * @desc 用于修改
     */
    public function update()
    {
        $data = array(
            'name' => $this->name,
            'note' => $this->note,
        );
        $result = DI()->notorm->user->where('id', $this->id)->update($data);

        if ($result === false) {
            throw new PhalApi_Exception_BadRequest('修改数据失败');
        }
    }

    /**
     * 删除语句
     * @desc 用于修改
     */
    public function delete()
    {
        $result = DI()->notorm->user->where('id', $this->id)->delete();

        if ($result === false) {
            throw new PhalApi_Exception_BadRequest('删除数据失败');
        }
    }
}