<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:14
 */

namespace Group\Model;

use Think\Model;

class GroupModel extends Model
{
    protected $tableName = 'group';
    protected $_validate = array(
        array('title', '1,99999', '标题不能为空', self::EXISTS_VALIDATE, 'length'),
        array('title', '0,100', '标题太长', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('post_count', '0', self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );


    public function editGroup($data)
    {

        $data = $this->create($data);
        if (!$data) return false;
        $result = $this->save($data);
        if (!$result) {
            return false;
        }
        action_log('edit_group', 'Group', $data['id'], is_login());
        S('group_' . $data['id'], null);
        return $result;
    }


    public function createGroup($data)
    {
        $data = $this->create($data);
        //对帖子内容进行安全过滤
        if (!$data) return false;
        $result = $this->add($data);
        if (!$result) {
            return false;
        }
        action_log('add_group', 'Group', $result, is_login());
        //返回帖子编号
        return $result;
    }

    public function getGroup($id)
    {
        $group = S('group_' . $id);
        if (is_bool($group)) {
            $group = $this->where(array('id' => $id, 'status' => 1))->find();
            if ($group) {
                $group['user'] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), $group['uid']);
                $group['user']['group_count'] = $this->getUserGroupCount($group['uid']);
                S('group_' . $id, $group, 60 * 60);
            }
        }
        if ($group) {
            $group['member_count'] = D('GroupMember')->where(array('group_id' => $group['id'], 'status' => 1))->cache('group_member_count_'.$group['id'],60*5)->count();
        }

        return $group;
    }


    public function getUserGroupCount($uid)
    {
        return $this->where(array('uid' => $uid, 'status' => 1))->count();
    }


    public function delGroup($id)
    {
        $res = $this->where(array('id' => $id))->setField('status', -1);
        S('group_' . $id, null);
        return $res;
    }


}
