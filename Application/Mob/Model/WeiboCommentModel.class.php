<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-10
 * Time: PM9:01
 */

namespace Mob\Model;

use Think\Model;

class WeiboCommentModel extends Model
{
    protected $_validate = array(
        array('content', '1,99999', '内容不能为空', self::EXISTS_VALIDATE, 'length'),
        array('content', '0,500', '内容太长', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );

    public function addComment($uid, $weibo_id, $content, $comment_id = 0)
    {

        //写入数据库
        $data = array('uid' => $uid, 'content' => $content, 'weibo_id' => $weibo_id, 'comment_id' => $comment_id);
        $data = $this->create($data);
        if (!$data) return false;
        $comment_id = $this->add($data);

        //增加微博评论数量
        M('Weibo')->where(array('id' => $weibo_id))->setInc('comment_count');

      //  S('weibo_' . $weibo_id, null);
        //返回评论编号
        return $comment_id;
    }

    public function deleteComment($comment_id)
    {
        //获取微博编号
        $weiboCommentModel=D('WeiboComment');
        $comment = $weiboCommentModel->find($comment_id);
        if ($comment['status'] == -1) {
            return false;
        }
        $weibo_id = $comment['weibo_id'];

        //将评论标记为已经删除

        $weiboCommentModel->where(array('id' => $comment_id))->setField('status', -1);

        //减少微博的评论数量
        D('Weibo')->where(array('id' => $weibo_id))->setDec('comment_count');
       // S('weibo_' . $weibo_id, null);
        //返回成功结果
        return true;
    }

    public function getComment($id){
/*        $comment = S('weibo_comment_'.$id);
        if(!$comment){*/
            $comment = $this->find($id);
            $comment['content']=parse_comment_content($comment['content']);
            $comment['user'] = query_user( array('uid', 'nickname', 'avatar32', 'avatar64', 'avatar128', 'avatar256', 'avatar512', 'space_url', 'icons_html', 'rank_link', 'score', 'title', 'weibocount', 'fans', 'following'), $comment['uid']);
/*            S('weibo_comment_'.$id,$comment);
        }*/
        $comment['can_delete']  = is_administrator(is_login()) || $comment['uid'] == is_login();
      return $comment;

    }



    public function getAllComment($weibo_id){

        $order = modC('COMMENT_ORDER',0,'WEIBO') == 1?'create_time asc':'create_time desc';
        $comment = $this->where(array('weibo_id'=>$weibo_id,'status'=>1))->order($order)->field('id')->select();
        $ids = getSubByKey($comment,'id');
        $list = array();
        foreach($ids as $v){
            $list[$v] = $this->getComment($v);
        }
        return $list;

    }


    public function getCommentList($weibo_id,$page=1,$show_more =0){

        $order = modC('COMMENT_ORDER',0,'WEIBO') == 1?'create_time asc':'create_time desc';
        $comment = $this->where(array('weibo_id'=>$weibo_id,'status'=>1))->order($order)->page($page,10)->field('id')->select();
/*        if($page == 1){
            $t = array_chunk($comment,5);
            if($show_more ){
                $comment = $t[1];
            }else{
                $comment = $t[0];
            }
        }*/
        $ids = getSubByKey($comment,'id');
        $list = array();
        foreach($ids as $v){
            $list[$v] = $this->getComment($v);
        }
        return $list;

    }

    public function addIssueComment($uid, $aIssueId, $aContent, $comment_id = 0)
    {

        //写入数据库
        $data = array('uid' => $uid, 'content' => $aContent, 'weibo_id' => $aIssueId, 'comment_id' => $comment_id);
        $data = $this->create($data);
        if (!$data) return false;
        $comment_id = $this->add($data);

        //增加微博评论数量
        M('Weibo')->where(array('id' => $aIssueId))->setInc('comment_count');

       // S('weibo_' . $aIssueId, null);
        //返回评论编号
        return $comment_id;
    }
}