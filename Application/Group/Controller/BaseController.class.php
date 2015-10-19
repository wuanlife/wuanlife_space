<?php
/**
 * 所属项目 阿里研究院.
 * 开发者: 陈一枭
 * 创建日期: 7/29/14
 * 创建时间: 3:44 PM
 * 版权所有 想天软件工作室(www.ourstu.com)
 */

namespace Group\Controller;

use Think\Controller;
use Think\Hook;

class BaseController extends Controller
{
    public function _initialize()
    {


        if (is_login()) {
            $sub_menu['left'][] = array('tab' => 'my', 'title' => "我的" . $this->MODULE_ALIAS, 'href' => is_login() ? U('group/index/my') : "javascript:toast.error('登录后才能操作')");
        } else {
            $sub_menu = array('left'=>array());
        }
        $sub_menu['left'] =array_merge($sub_menu['left'],
            array(
                array('tab' => 'discover', 'href' => U('group/index/discover'), 'title' => '发现'),
                array('tab' => 'select', 'title' => '精选', 'href' => U('group/index/select')),
                array('tab' => 'groups', 'title' => "全部" . $this->MODULE_ALIAS, 'href' => U('group/index/groups')),
            ));
        $sub_menu['right'] =
            array(
                array('tab' => 'create', 'title' => '创建' . $this->MODULE_ALIAS, 'href' => check_auth('Group/Index/addGroup',-1) ? U('group/index/create') : "javascript:toast.error('您无添加群组权限')"),
            );

        $this->assign('sub_menu', $sub_menu);
        $this->assign('current', 'home');
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

    }

    public function index()
    {
        redirect(is_login() ? U('mygroup') : U('groups'));
    }

    protected function parseSearchKey($key = null)
    {
        $action = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
        $post = I('post.');
        if (empty($post)) {
            $keywords = cookie($action);
        } else {
            $keywords = $post;
            cookie($action, $post);
            $_GET['page'] = 1;
        }

        if (!$_GET['page']) {
            cookie($action, null);
            $keywords = null;
        }
        return $key ? $keywords[$key] : $keywords;
    }

    protected function assignGroupTypes()
    {
        $groupType = D('GroupType')->getGroupTypes();
        $this->assign($groupType);
        return $groupType;
    }

    protected function assignGroup($id)
    {
        $group = D('Group/Group')->getGroup($id);
        $this->assign('group', $group);
        return $group;
    }

    protected function assignNotice($group_id)
    {
        $notice = D('GroupNotice')->getNotice($group_id);
        $this->assign('notice', $notice);
        return $notice;
    }

    protected function assignPostCategory($group_id = 0)
    {
        $map['status'] = 1;
        $group_id && $map['group_id'] = $group_id;
        $cate = D('GroupPostCategory')->getList(array('where' => $map, 'order' => 'sort asc'));
        foreach ($cate as &$v) {
            $v = D('GroupPostCategory')->getPostCategory($v);
        }
        $this->assign('post_cate', $cate);
        return $cate;
    }


    protected function assignGroupAllType()
    {
        $groupType = $this->assignGroupTypes();
        foreach ($groupType['parent'] as $k => $v) {
            $child = $groupType['child'][$v['id']];
            //获取数组中第一父级的位置
            $key_name = array_search($v, $groupType['parent']);
            foreach ($child as $key => $val) {
                $val['title'] = '------' . $val['title'];
                //在父级后面添加数组
                array_splice($groupType['parent'], $key_name + 1, 0, array($val));
            }
        }
        $this->assign('groupTypeAll', $groupType['parent']);

    }

    protected function getGroupIdByPost($post_id)
    {
        $post = D('GroupPost')->getPost($post_id);
        return $post['group_id'];

    }


    protected function requireLogin()
    {
        if (!is_login()) {
            $this->error('需要登录才能操作');
        }
    }

    protected function requireGroupExists($group_id)
    {
        if (!group_is_exist($group_id)) {
            $this->error('群组不存在');
        }
    }

    protected function requirePostExists($post_id)
    {
        if (!post_is_exist($post_id)) {
            $this->error('帖子不存在');
        }
    }

} 