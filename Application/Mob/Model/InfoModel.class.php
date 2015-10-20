<?php
namespace mob\Model;

use Think\Model;

/**信息模型
 * Class InfoModel
 */
class InfoModel extends Model
{
    protected $tableName = 'cat_info';

    /**
     * @param string $map
     * @param int    $num
     * @param string $order
     * @return mixed
     */
    function getList($map = '', $num = 10, $order = 'create_time desc')
    {
        $rs = D('cat_info')->where($map)->order($order)->findPage($num);
        foreach ($rs['data'] as $key => $v) {
            $rs['data'][$key]['data'] = D('Data')->getByInfoId($v['id']);
        }

       // dump($rs);exit;
        return $rs;
    }

    function checkOwner($mid, $info_id)
    {
        $map['uid'] = $mid;
        $map['info_id'] = $info_id;
        $is = D('cat_info')->where($map)->count();
        return $is;
    }

    function getLimit($map = '', $num = 10, $order = 'create_time desc')
    {
        $rs = D('cat_info')->where($map)->order($order)->limit($num)->select();
        foreach ($rs as $key => $v) {
            $rs[$key]['data'] = D('Data')->getByInfoId($v['info_id']);
        }

        return $rs;
    }

    function getById($id)
    {
        $map['id'] = $id;
        $info = $this->find($id);
        $info['data'] = D('StoreData')->getByInfoId($id);
        return $info;
    }

    public function syncToFeed($info_id, $title, $content, $uid)
    {
        $d['content'] = '';
        $d['body'] = getShort($content, 100) . '【' . $title . '】' . '&nbsp;' . U('cat/Index/info', array('info_id' => $info_id));
        $feed = D('Feed')->syncToFeed($d['body'], $uid, '0');
        // $feed = model('Feed')->put($uid, 'weiba', 'weiba_post', $d, $post_id, 'weiba_post');
        return $feed['feed_id'];
    }
}