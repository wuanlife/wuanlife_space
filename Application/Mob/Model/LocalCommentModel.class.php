<?php


namespace Mob\Model;

use Think\Model;

class LocalCommentModel extends Model
{
    public function addIssueComment($uid, $aIssueId, $aContent, $comment_id = 0)
    {
        $issue="Issue";
        $mod="issueContent";
        $time=time();
        $status="1";
        if(empty($aContent)){
            return false;
        }

        //写入数据库
        $data = array('uid' => $uid,'app'=>$issue, 'mod'=>$mod,'content' => $aContent,'create_time'=>$time, 'row_id' => $aIssueId, 'comment_id' => $comment_id,'status'=>$status);
        $data = $this->create($data);
        if (!$data) return false;
        $comment_id = $this->add($data);


        //增加微博评论数量
        M('IssueContent')->where(array('id' => $aIssueId))->setInc('reply_count');

        S('IssueContent_' . $aIssueId, null);
        //返回评论编号
        return $comment_id;
    }

    public function deleteComment($comment_id)
    {
        //获取微博编号
        $issueCommentModel=D('LocalComment');
        $comment = $issueCommentModel->find($comment_id);
        if ($comment['status'] == -1) {
            return false;
        }
        $issue_id = $comment['row_id'];

        //将评论标记为已经删除

        $issueCommentModel->where(array('id' => $comment_id))->setField('status', -1);

        //减少专辑的评论数量
        D('IssueContent')->where(array('id' => $issue_id))->setDec('reply_count');
        S('IssueContent' . $issue_id, null);
        //返回成功结果
        return true;
    }

    public function addBlogComment($uid, $aBlogId, $aContent, $comment_id = 0)
    {
        $issue="News";
        $mod="index";
        $time=time();
        $status="1";
        if(empty($aContent)){
            return false;
        }

        //写入数据库
        $data = array('uid' => $uid,'app'=>$issue, 'mod'=>$mod,'content' => $aContent,'create_time'=>$time, 'row_id' => $aBlogId, 'comment_id' => $comment_id,'status'=>$status);
        $data = $this->create($data);
        if (!$data) return false;
        $comment_id = $this->add($data);

        //增加微博评论数量
        M('IssueContent')->where(array('id' => $aBlogId))->setInc('reply_count');

        S('IssueContent_' . $aBlogId, null);
        //返回评论编号
        return $comment_id;
    }

    public function deleteBlogComment($comment_id)
    {
        //获取微博编号
        $issueCommentModel=D('LocalComment');
        $comment = $issueCommentModel->find($comment_id);
        if ($comment['status'] == -1) {
            return false;
        }

        //将评论标记为已经删除

        $issueCommentModel->where(array('id' => $comment_id))->setField('status', -1);


        return true;
    }
}