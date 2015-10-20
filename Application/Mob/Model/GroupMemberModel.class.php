<?php
namespace Mob\Model;

use Think\Model;

class GroupMemberModel extends Model
{
    protected $tableName = 'group_member';

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('activity', '0', self::MODEL_INSERT),
    );

    public function addMember($data)
    {
        $data = $this->create($data);
        $res = $this->add($data);
        return $res;
    }

    public function delMember($map)
    {
          return $this->where($map)->delete();
    }

    public function setPosition($uid, $group_id, $position)
    {
        $res = $this->where(array('uid' => $uid, 'group_id' => $group_id))->setField('position', $position);
        return $res;
    }


    public function setStatus($uid, $group_id, $status)
    {
        $res = $this->where(array('uid' => $uid, 'group_id' => $group_id))->save(array('status'=>$status,'update_time'=>time()));
        return $res;
    }

    public function getIsJoin($uid, $group_id)
    {
        $status = S('group_is_join_' . $group_id . '_' . $uid);
        if (is_bool($status)) {
            $check = $this->where(array('group_id' => $group_id, 'uid' => $uid))->find();
            if (!$check) {
                //未加入群组
                $status = 0;
            } else {
                if ($check['status'] == 1) {
                    // 已加入群组并已审核
                    $status = 1;
                } else {
                    //未审核
                    $status = 2;
                }
            }
            S('group_is_join_' . $group_id . '_' . $uid, $status, 60 * 60);
        }
        return $status;
    }

    public function setLastView($group_id)
    {
        $this->where(array('group_id' => $group_id, 'uid' => is_login()))->setField('last_view', time());

    }


    public function getMember($uid, $group_id)
    {
        $member = $this->where(array('group_id' => $group_id, 'uid' => $uid, 'status' => 1))->cache('group_member_' . $group_id . '_' . $uid, 60 * 5)->find();
        return $member;
    }

    public function getMemberById($id)
    {
        $member = $this->where(array('id' => $id, 'status' => 1))->find();
        return $member;
    }


    public function getGroupIds($param)
    {
        !empty($param['field']) && $this->field($param['field']);
        !empty($param['where']) && $this->where($param['where']);
        !empty($param['limit']) && $this->limit($param['limit']);
        empty($param['order']) && $param['order'] = 'create_time desc';
        !empty($param['page']) && $this->page($param['page'], empty($param['count']) ? 10 : $param['count']);
        $this->order($param['order']);
        $list = $this->select();
        $list = getSubByKey($list, 'group_id');
        return $list;
    }

    public function getGroupAdmin($group_id){
        $uids =$this->where(array('group_id'=>$group_id,'position'=>array('egt',2),'status'=>1))->field('uid')->order('position desc')->select();

        return getSubByKey($uids,'uid');
    }

}