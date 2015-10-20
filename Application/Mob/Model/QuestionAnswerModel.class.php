<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-6
 * Time: 下午1:33
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Mob\Model;

use Common\Model\ContentHandlerModel;
use Think\Model;

class QuestionAnswerModel extends Model
{

    public function editData($data)
    {
        $contentHandler = new ContentHandlerModel();
        if (isset($data['content'])){
            $data['content'] = $contentHandler->filterHtmlContent($data['content']);
        }

        if ($data['id']) {
            $data['update_time'] = time();
            $res = $this->save($data);
            if ($res) {
                action_log('edit_answer', 'question_answer', $data['id'], get_uid());
            }
        } else {
            $data['support'] = $data['oppose'] = 0;
            $data['status'] = 1;
            $data['uid'] = get_uid();
            $data['create_time'] = $data['update_time'] = time();
            $res = $this->add($data);
            if ($res) {
                D('Question/Question')->where(array('id' => $data['question_id']))->setInc('answer_num');
                action_log('add_answer', 'question_answer', $res, get_uid());
            }
        }
        return $res;
    }

    public function changeNum($id, $type = 1)
    {
        if ($type) {
            $field = 'support';
        } else {
            $field = 'oppose';
        }
        $res = $this->where(array('id' => $id))->setInc($field);
        return $res;
    }

    public function getData($map, $order = null)
    {
        if ($order == null) {
            $data = $this->where($map)->find();
        } else {
            $data = $this->where($map)->order($order)->limit(1)->select();
            $data = $data[0];
        }
        if ($data) {
            $contentHandler = new ContentHandlerModel();
            $data['content'] = $contentHandler->displayHtmlContent($data['content']);

            $questionSupportModel = D('Question/QuestionSupport');
            $data['user'] = query_user(array('uid', 'space_url', 'nickname'), $data['uid']);
            $support = $questionSupportModel->getData(array('uid' => get_uid(), 'tablename' => 'QuestionAnswer', 'row' => $data['id']));
            if ($support) {
                if ($support['type']) {
                    $data['is_support'] = 1;
                } else {
                    $data['is_oppose'] = 1;
                }
            }
            $data['support_user'] = $questionSupportModel->getSupportUsers(array('tablename' => 'QuestionAnswer', 'row' => $data['id'], 'type' => 1));
        }
        return $data;
    }

    /**
     * 获取问题回答列表，用于问题详情页
     * @param $map
     * @param int $page
     * @param string $order
     * @param int $r
     * @param string $field
     * @return array
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getListByMapPage($map, $page = 1, $order = 'support desc,create_time desc', $r = 10, $field = '*')
    {
        $questionSupportModel = D('Question/QuestionSupport');
        $totalCount = $this->where($map)->count();
        if ($totalCount) {
            $list = $this->where($map)->page($page, $r)->order($order)->field($field)->select();
            $contentHandler = new ContentHandlerModel();
            foreach ($list as &$val) {
                $val['content'] = $contentHandler->displayHtmlContent($val['content']);
                $val['user'] = query_user(array('uid', 'space_url', 'nickname'), $val['uid']);
                $support = $questionSupportModel->getData(array('uid' => get_uid(), 'tablename' => 'QuestionAnswer', 'row' => $val['id']));
                if ($support) {
                    if ($support['type']) {
                        $val['is_support'] = 1;
                    } else {
                        $val['is_oppose'] = 1;
                    }
                }
                $val['support_user'] = $questionSupportModel->getSupportUsers(array('tablename' => 'QuestionAnswer', 'row' => $val['id'], 'type' => 1));
            }
        }
        return array($list, $totalCount);
    }

    /**
     * 获取我的回答列表
     * @param int $uid
     * @param int $page
     * @param string $order
     * @param int $r
     * @param string $field
     * @return array
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getMyListPage($uid = 0, $page = 1, $order = 'support desc,create_time desc', $r = 10, $field = '*')
    {
        !$uid && $uid = is_login();
        $questionModel = D('Question/Question');
        $map['uid'] = $uid;
        $map['status'] = 1;
        $totalCount = $this->where($map)->count();
        if ($totalCount) {
            $list = $this->where($map)->page($page, $r)->order($order)->field($field)->select();
            $contentHandler = new ContentHandlerModel();
            foreach ($list as &$val) {
                $val['content'] = $contentHandler->displayHtmlContent($val['content']);
                $val['question'] = $questionModel->getData($val['question_id']);
                $val['question']['info'] = msubstr(op_t($val['question']['description']), 0, 200);
                $val['question']['img'] = get_pic($val['question']['description']);
            }
        }
        return array($list, $totalCount);
    }

    public function getSimpleListPage($map, $page = 1, $order = 'create_time desc', $r = 20, $field = '*')
    {
        $totalCount = $this->where($map)->count();
        if ($totalCount) {
            $list = $this->where($map)->page($page, $r)->order($order)->field($field)->select();
            $contentHandler = new ContentHandlerModel();
            foreach ($list as &$val) {
                $val['content'] = $contentHandler->displayHtmlContent($val['content']);
            }
        }
        return array($list, $totalCount);
    }
} 