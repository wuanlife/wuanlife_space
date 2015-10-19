<?php

namespace Group\Model;
use Think\Model;

class GroupTypeModel extends Model {
    protected $tableName='group_type';

    public function getGroupTypes(){
        $parent = $this->where(array('status' => 1,'pid'=>0))->order('sort asc')->select();
        $child =array();
        foreach($parent as $v){
            $child[$v['id']] = $this->where(array('status' => 1,'pid'=>$v['id']))->order('sort asc')->select();
        }
        return array('parent'=>$parent,'child'=>$child);
    }


    public function getGroupType($id){
        $type = S('group_type_'.$id);
        if(empty($type)){
            $type =  $this->where(array('id'=>$id,'status'=>1))->find();
            S('group_type_'.$id,$type,300);
        }
         return $type;
    }
}