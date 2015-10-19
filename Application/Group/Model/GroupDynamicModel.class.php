<?php
namespace Group\Model;
use Think\Model;

class GroupDynamicModel extends Model
{
    protected $tableName = 'group_dynamic';

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    public function addDynamic($data)
    {
        $data = $this->create($data);
        $res = $this->add($data);
        return $res;
    }


    public function getDynamic($id){
        $dynamic = $this->where(array('id'=>$id,'status'=>1))->cache('group_dynamic_'.$id,60*5)->find();
        return $dynamic;
    }

}