<?php
/**
 * 所属项目 OnePlus.
 * 开发者: 陈一枭
 * 创建日期: 6/9/14
 * 创建时间: 2:22 PM
 * 版权所有 嘉兴想天信息科技有限公司(www.ourstu.com)
 */

namespace Common\Model;

use Think\Model;
class TalkPushModel extends Model{

    public function getAllPush(){
        $new_talks=$this->where(array('uid'=>get_uid(),'status'=>0))->select();

        foreach($new_talks as &$v){
            $v['talk']=D('Talk')->find($v['source_id']);
            $uids=D('Common/Talk')->decodeArrayByRec(explode(',',$v['talk']['uids']));
            $user=D('Common/Talk')->getFirstOtherUser($uids);
            $v['talk']['ico']=$user['avatar64'];
        }
        unset($v);
        return $new_talks;
    }
}