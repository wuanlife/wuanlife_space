<?php

namespace Group\Model;
use Think\Model;

class GroupPostCategoryModel extends Model {
    protected $tableName='group_post_category';

    public function getPostCategory($id){
        $cate = S('group_post_category_'.$id);
        if(empty($cate)){
            $cate =  $this->where(array('id'=>$id,'status'=>1))->find();
            S('group_post_category_'.$id,$cate,60*5);
        }
         return $cate;
    }
}