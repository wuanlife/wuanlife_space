<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:14
 */

namespace Mob\Model;

use Think\Model;

class catModel extends Model
{
    protected $tableName = 'cat_info';

    public function getList($map, $page ="", $count = "", $order = 'create_time desc')
    {
        $info_list = S('INFO_LIST');
        //   dump($info_list);exit;
        if (!$info_list) {
            $totalCount = $this->where($map)->count();
            if ($totalCount) {
                $info_list = $this->where($map)->order($order)->page($page, $count)->select();

                foreach ($info_list as &$val) {
                    $val['user'] = query_user(array('nickname', 'avatar32', 'avatar64', 'space_mob_url'), $val['uid']);
                    $val['detail'] = $this->getInfo($val['id']);
                }
            }
            unset($val);
            S('INFO_LIST', $info_list, 600);
        }
        return array($info_list, $totalCount);
    }

    public function getInfo($id)
    {
        $info = $this->find($id);
        $field_list = D('CatField')->where(array('entity_id' => $info['entity_id']))->select();
        $field_list_l = array_column($field_list, 'name');
        $field_list = array_combine($field_list_l, $field_list);

        foreach ($field_list as &$v) {
            $v['data'] = D('CatData')->where(array('field_id' => $v['id'], 'info_id' => $info['id']))->find();
            $v['data'] = $v['data']['value'];
            if (strpos($v['name'], 'zhaopian') === 0) {
                if (empty($v['data'])) {
                    $v['data'] = NUll;
                } else {
                    $v['data'] = getThumbImageById($v['data']);
                }
            }
        }
        return $field_list;
    }
}