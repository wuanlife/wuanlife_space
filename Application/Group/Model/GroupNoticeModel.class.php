<?php
namespace Group\Model;
use Think\Model;

class GroupNoticeModel extends Model {


    protected $tableName='group_notice';
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    public  function getNotice($group_id)
    {
        $notice = $this->where('group_id=' . $group_id)->cache('group_notice_'.$group_id,60*60)->find();
        return $notice;
    }

    public function addNotice($data){
        $data = $this->create($data);
        if(!$data) return false;
        $result = $this->add($data,array(),true);
        if(!$result) {
            return false;
        }

        S('group_notice_'.$data['group_id'],null);
        return $result;
    }

}