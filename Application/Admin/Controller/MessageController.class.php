<?php
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

/**
 * Class MessageController  消息控制器
 * @package Admin\Controller
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
class MessageController extends AdminController
{


    public function userList($page=1,$r=20)
    {

        $aUserGroup = I('get.user_group', 0, 'intval');
        $aRole = I('get.role', 0, 'intval');
        $map = array();

        if (!empty($aRole) || !empty($aUserGroup)) {
            $uids = $this->getUids($aUserGroup, $aRole);
            $map['uid'] = array('in', $uids);
        }

        $user = D('member')->where($map)->page($page,$r)->field('uid,nickname')->select();
        foreach ($user as &$v) {
            $v['id'] = $v['uid'];
        }
        unset($v);
        $totalCount = D('member')->where($map)->count();
        $r = 20;

        $role = D('Role')->selectByMap(array('status' => 1));
        $user_role = array(array('id' => 0, 'value' => '全部'));
        foreach ($role as $key => $v) {
            array_push($user_role, array('id' => $v['id'], 'value' => $v['title']));
        }

        $group = D('AuthGroup')->getGroups();

        $user_group = array(array('id' => 0, 'value' => '全部'));
        foreach ($group as $key => $v) {
            array_push($user_group, array('id' => $v['id'], 'value' => $v['title']));
        }

        $builder = new AdminListBuilder();
        $builder->title("群发用户列表");
        $builder->meta_title = '群发用户列表';

        $builder->setSelectPostUrl(U('Message/userList'))
            ->select('用户组：', 'user_group', 'select', '根据用户组筛选', '', '', $user_group)
            ->select('身份：', 'role', 'select', '根据用户身份筛选', '', '', $user_role);
        $builder->buttonModalPopup(U('Message/sendMessage'), array('user_group' => $aUserGroup, 'role' => $aRole), '发送消息', array('data-title' => '群发消息', 'target-form' => 'ids', 'can_null' => 'true'));
        $builder->keyText('uid', '用户ID')->keyText('nickname', "昵称");

        $builder->data($user);
        $builder->pagination($totalCount, $r);
        $builder->display();


    }

    private function getUids($user_group = 0, $role = 0)
    {
        $uids = array();
        if (!empty($user_group)) {
            $users = D('auth_group_access')->where(array('group_id' => $user_group))->field('uid')->select();
            $group_uids = getSubByKey($users, 'uid');
            if ($group_uids) {
                $uids = $group_uids;
            }
        }
        if (!empty($role)) {
            $users = D('user_role')->where(array('role_id' => $role))->field('uid')->select();
            $role_uids = getSubByKey($users, 'uid');
            if ($role_uids) {
                $uids = $role_uids;
            }
        }
        if (!empty($role) && !empty($user_group)) {
            $uids = array_intersect($group_uids, $role_uids);
        }
        return $uids;


    }

    public function sendMessage()
    {

        if (IS_POST) {
            $aUids = I('post.uids');
            $aUserGroup = I('post.user_group');
            $aUserRole = I('post.user_role');
            $aTitle = I('post.title', '', 'text');
            $aContent = I('post.content', '', 'html');
            $aUrl = I('post.url', '', 'text');
            $aArgs = I('post.args', '', 'text');
            $args = array();
            // 转换成数组
            if ($aArgs) {
                $array = explode('/', $aArgs);
                while (count($array) > 0) {
                    $args[array_shift($array)] = array_shift($array);
                }
            }

            if (empty($aTitle)) {
                $this->error('请输入消息标题');
            }
            if (empty($aContent)) {
                $this->error('请输入消息内容');
            }
            // 以用户组或身份发送消息
            if(empty($aUids)){
                if (empty($aUserGroup) && empty($aUserRole)) {
                    $this->error('请选择用户组或身份组或者用户');
                }

                $role_count = D('Role')->where(array('status' => 1))->count();
                $group_count = D('AuthGroup')->where(array('status' => 1))->count();
                if ($role_count == count($aUserRole)) {
                    $aUserRole = 0;
                }
                if ($group_count == count($aUserGroup)) {
                    $aUserGroup = 0;
                }
                if (!empty($aUserRole)) {
                    $uids = D('user_role')->where(array('role_id' => array('in', $aUserRole)))->field('uid')->select();
                }
                if (!empty($aUserGroup)) {
                    $uids = D('auth_group_access')->where(array('group_id' => array('in', $aUserGroup)))->field('uid')->select();
                }
                if (empty($aUserRole) && empty($aUserGroup)) {
                    $uids = D('Member')->where(array('status' => 1))->field('uid')->select();
                }
                $to_uids = getSubByKey($uids, 'uid');
            }else{
                // 用uid发送消息
                $to_uids = explode(',',$aUids);
            }
            D('Message')->sendMessageWithoutCheckSelf($to_uids, $aTitle, $aContent, $aUrl, $args);
            $result['status'] = 1;
            $result['info'] = '发送成功';
            $this->ajaxReturn($result);
        } else {
            $aUids = I('get.ids');
            $aUserGroup = I('get.user_group', 0, 'intval');
            $aRole = I('get.role', 0, 'intval');
            if (empty($aUids)) {
                $role = D('Role')->selectByMap(array('status' => 1));
                $roles = array();
                foreach ($role as $key => $v) {
                    array_push($roles, array('id' => $v['id'], 'value' => $v['title']));
                }
                $group = D('AuthGroup')->getGroups();
                $groups = array();
                foreach ($group as $key => $v) {
                    array_push($groups, array('id' => $v['id'], 'value' => $v['title']));
                }
                $this->assign('groups', $groups);
                $this->assign('roles', $roles);
                $this->assign('aUserGroup', $aUserGroup);
                $this->assign('aRole', $aRole);
            } else {
                $uids = implode(',',$aUids);
                $users = D('Member')->where(array('uid'=>array('in',$aUids)))->field('uid,nickname')->select();
                $this->assign('users', $users);
                $this->assign('uids', $uids);
            }
            $this->display('sendmessage');
        }
    }

}
