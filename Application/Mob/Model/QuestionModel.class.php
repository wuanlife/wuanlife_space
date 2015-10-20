<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-5
 * Time: 下午1:03
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Mob\Model;


use Common\Model\ContentHandlerModel;
use Think\Model;

class QuestionModel extends Model{

    public function editData($data)
    {
        $contentHandler=new ContentHandlerModel();
        if(isset($data['description'])){
            $data['description']=$contentHandler->filterHtmlContent($data['description']);
        }

        if($data['id']){
            $data['update_time']=time();
            $res=$this->save($data);
            if($res){
                action_log('edit_question','question',$data['id'],get_uid());
            }
        }else{
            !$data['category']&&$data['category']=1;
            $data['create_time']=$data['update_time']=time();
            $res=$this->add($data);
            if($res){
                action_log('add_question','question',$res,get_uid());
            }
        }
        return $res;
    }

    public function getData($id)
    {
        if($id>0){
            $map['id']=$id;
            $data=$this->where($map)->find();
            if($data){
                $data['user']=query_user(array('uid','space_url','nickname','avatar64'),$data['uid']);
                $data['category_info']=D('Question/QuestionCategory')->info($data['category']);
                $contentHandler=new ContentHandlerModel();
                $data['description']=$contentHandler->displayHtmlContent($data['description']);
            }
            return $data;
        }
        return null;
    }

    public function getListPageByMap($map,$page=1,$order='create_time desc',$r=20,$field='*')
    {
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->page($page,$r)->order($order)->field($field)->select();
            $contentHandler=new ContentHandlerModel();
            foreach($list as &$val){
                $val['description']=$contentHandler->displayHtmlContent($val['description']);
            }
        }
        return array($list,$totalCount);
    }

    public function getList($map,$field='*',$limit=0,$order='create_time desc')
    {
        if($limit){
            $list=$this->where($map)->field($field)->order($order)->limit($limit)->select();
        }else{
            $list=$this->where($map)->field($field)->select();
        }
        $contentHandler=new ContentHandlerModel();
        foreach($list as &$val){
            $val['description']=$contentHandler->displayHtmlContent($val['description']);
        }
        return $list;
    }
} 