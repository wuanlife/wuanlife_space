<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-7
 * Time: 下午2:04
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Mob\Model;


use Think\Model;

class QuestionSupportModel extends Model{

    public function addData($tableName="Question",$row,$type=1)
    {
        $data['tablename']=$tableName;
        $data['row']=$row;
        $data['type']=$type;
        $data['uid']=get_uid();
        $result=$this->add($data);
        return $result;
    }

    public function getData($map)
    {
        $data=$this->where($map)->find();
        return $data;
    }

    /**
     * 获取支持用户
     * @param $map
     * @return array|null
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getSupportUsers($map)
    {
        $uids=$this->where($map)->field('uid')->order('id asc')->limit(10)->select();
        $users=array();
        foreach($uids as $val){
            $users[]=query_user(array('uid','nickname','space_url'),$val['uid']);
        }
        return $users;
    }
} 