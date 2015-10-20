<?php
namespace mob\Model;

use Think\Model;

/**信息模型
 * Class InfoModel
 */
class StoreInfoModel extends Model
{
    protected $tableName = 'store_goods';

    /**
     * @param string $map
     * @param int    $num
     * @param string $order
     * @return mixed
     */
    function getList($map = '', $num = 10, $order = 'create_time desc')
    {
        $rs = $this->where($map)->order($order)->findPage($num);
        foreach ($rs['data'] as $key => $v) {
            $rs['data'][$key]['data'] = D('Data')->getByInfoId($v['id']);
        }
        return $rs;
    }

    function checkOwner($mid, $info_id)
    {
        $map['uid'] = $mid;
        $map['id'] = $info_id;
        $is = D('Goods')->where($map)->count();
        return $is;
    }

    function getLimit($map = '', $num = 10, $order = 'create_time desc')
    {
        $rs =$this->where($map)->order($order)->limit($num)->select();
        foreach ($rs as $key => $v) {
            $rs[$key]['data'] = D('Data')->getByInfoId($v['id']);
        }

        return $rs;
    }

    function getById($id)
    {
        $map['id'] = $id;
        $info = $this->find($id);
        $info['data'] = D('Data')->getByInfoId($id);
        return $info;
    }
}