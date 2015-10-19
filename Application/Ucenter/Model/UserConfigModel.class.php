<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-9
 * Time: 下午3:56
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Ucenter\Model;


use Think\Model;

class UserConfigModel extends Model
{
    public function addData($data=array())
    {
        $res=$this->add($data);
        return $res;
    }

    public function findData($map=array())
    {
        $res=$this->where($map)->find();
        return $res;
    }

    public function saveValue($map=array(),$value='')
    {
        $res=$this->where($map)->setField('value',$value);
        return $res;
    }
} 