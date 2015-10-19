<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminSortBuilder;
use Think\Model;
use Weibo\Api\WeiboApi;

class GroupController extends AdminController
{

    function _initialize()
    {
        parent::_initialize();
    }


    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        if (IS_POST) {
            S('GROUP_SHOW_DATA', null);
            S('GROUP_POST_SHOW_DATA', null);
        }
        $data = $admin_config->handleConfig();
        $admin_config->title('群组基本设置')
            ->keyBool('GROUP_NEED_VERIFY', '创建群组是否需要审核', '默认无需审核')
            ->keyText('GROUP_POST_IMG_COUNT', '帖子显示图片的数量限制', '默认为10')

            ->keyCheckBox('GROUP_SEND_WEIBO', '创建/修改群组发送微博开关', '', array('add_group' => '创建群组', 'edit_group' => '编辑群组'))
            ->keyBool('GROUP_AUDIT_SEND_WEIBO', '审核群组是否发送微博', '')
            ->keyText('GROUP_LIMIT', '每个人允许创建的群组个数', '默认为5')
            ->keyCheckBox('GROUP_POST_SEND_WEIBO', '创建/修改帖子发送微博开关', '', array('add_group_post' => '新增帖子', 'edit_group_post' => '编辑帖子'))
            ->keyRadio('GROUP_LZL_REPLY_ORDER', '楼中楼排序', '', array(0 => '时间降序', 1 => '时间升序'))
            ->keyText('GROUP_LZL_SHOW_COUNT', '楼中楼显示数量')

            ->buttonSubmit('', '保存')->data($data)

            ->keyDefault('GROUP_LIMIT', 5)
            ->keyDefault('GROUP_NEED_VERIFY', 0)
            ->keyDefault('GROUP_POST_IMG_COUNT', 10)
            ->keyDefault('GROUP_SEND_WEIBO', 'add_group,edit_group')
            ->keyDefault('GROUP_POST_SEND_WEIBO', 'add_group_post,edit_group_post')
            ->keyDefault('GROUP_LZL_REPLY_ORDER', 1)
            ->keyDefault('GROUP_LZL_SHOW_COUNT', 5)
            ->group('群组设置', 'GROUP_NEED_VERIFY,GROUP_SEND_WEIBO,GROUP_AUDIT_SEND_WEIBO,GROUP_LIMIT')
            ->group('帖子设置', 'GROUP_POST_IMG_COUNT,GROUP_POST_SEND_WEIBO')
            // ->group('回复设置','')
            ->group('楼中楼设置', 'GROUP_LZL_REPLY_ORDER,GROUP_LZL_SHOW_COUNT')

            ->keyText('GROUP_SHOW_TITLE', '标题名称', '在首页展示块的标题')->keyDefault('GROUP_SHOW_TITLE', '推荐群组')
            ->keyText('GROUP_SHOW', '显示板块', '竖线|分割，填板块ID，如1|2|3|4|5')
            ->keyText('GROUP_SHOW_CACHE_TIME', '缓存时间', '默认600秒，以秒为单位')->keyDefault('GROUP_SHOW_CACHE_TIME', '600')

            ->keyText('GROUP_POST_SHOW_TITLE', '标题名称', '在首页展示块的标题')->keyDefault('GROUP_POST_SHOW_TITLE', '热门群组话题')
            ->keyText('GROUP_POST_SHOW_NUM', '贴子显示数量')->keyDefault('GROUP_POST_SHOW_NUM', 5)
            ->keyRadio('GROUP_POST_ORDER', '贴子排序字段', '', array('create_time' => '创建时间', 'update_time' => '更新时间', 'last_reply_time' => '最后回复时间', 'view_count' => '阅读量', 'reply_count' => '回复数'))->keyDefault('GROUP_POST_ORDER', 'last_reply_time')
            ->keyRadio('GROUP_POST_TYPE', '贴子排序方式', '', array('asc' => '升序', 'desc' => '降序'))->keyDefault('GROUP_POST_TYPE', 'desc')
            ->keyText('GROUP_POST_CACHE_TIME', '缓存时间', '默认600秒，以秒为单位')->keyDefault('GROUP_POST_CACHE_TIME', '600')

            ->group('首页展示板块设置', 'GROUP_SHOW_TITLE,GROUP_SHOW,GROUP_SHOW_CACHE_TIME')
            ->group('首页展示贴子设置', 'GROUP_POST_SHOW_TITLE,GROUP_POST_SHOW_NUM,GROUP_POST_ORDER,GROUP_POST_TYPE,NEWS_SHOW_CACHE_TIME');
        $admin_config->display();
    }

    public function index()
    {
        redirect(U('group'));
    }

    public function group()
    {

        $aPage = I('get.page', 1, 'intval');
        $aTypeId = I('get.type_id', 0, 'intval');
        $r = 20;

        //读取数据
        $map = array('status' => 1);
        if ($aTypeId != 0) {
            $map['type_id'] = $aTypeId;
        }
        $model = M('Group');
        $list = $model->where($map)->page($aPage, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('群组管理')
            ->buttonNew(U('Group/editGroup'))
            ->setStatusUrl(U('Group/setGroupStatus'))->buttonDisable('', '审核不通过')->buttonDelete()
            ->buttonSort(U('Group/sortGroup'))
            ->keyId()->keyLink('title', '标题', 'Group/post?group_id=###')
            ->keyCreateTime()->keyText('post_count', '文章数量')->keyStatus()->keyDoActionEdit('editGroup?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }


    public function unverify()
    {
        $aPage = I('get.page', 1, 'intval');
        $aTypeId = I('get.type_id', 0, 'intval');
        $r = 20;
        //读取数据
        $map = array('status' => 0);
        if ($aTypeId != 0) {
            $map['type_id'] = $aTypeId;
        }
        $model = M('Group');
        $list = $model->where($map)->page($aPage, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('群组管理')
            ->setStatusUrl(U('Group/setGroupStatus'))->buttonEnable(U('Group/setGroupStatus', array('tip' => 'verify')), '审核通过')->buttonDelete()
            ->buttonSort(U('Group/sortGroup'))
            ->keyId()->keyLink('title', '标题', 'Group/post?group_id=###')
            ->keyCreateTime()->keyText('post_count', '文章数量')->keyStatus()->keyDoActionEdit('editGroup?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function groupType()
    {
        //读取数据
        $map = array('status' => array('GT', -1), 'pid' => 0);

        $model = M('GroupType');
        $list = $model->where($map)->order('sort asc')->select();

        foreach ($list as $k => $v) {
            $child = $model->where(array('pid' => $v['id'], 'status' => 1))->order('sort asc')->select();
            //获取数组中第一父级的位置
            $key_name = array_search($v, $list);
            foreach ($child as $key => $val) {
                $val['title'] = '------' . $val['title'];
                //在父级后面添加数组
                array_splice($list, $key_name + 1, 0, array($val));
            }
        }

        foreach ($list as &$type) {
            $type['group_count'] = D('Group')->where(array('type_id' => $type['id']))->count();
        }
        unset($type);
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('分类管理')
            ->buttonNew(U('Group/editGroupType'))
            ->setStatusUrl(U('Group/setGroupTypeStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->buttonSort(U('Group/sortGroupType'))
            ->keyId()->keyLink('title', '标题', 'Group/group?type_id=###')
            ->keyCreateTime()->keyText('group_count', '群组数量')->keyStatus()->keyDoActionEdit('editGroupType?id=###')
            ->data($list)
            ->display();
    }

    public function setGroupTypeStatus($ids, $status)
    {
        $builder = new AdminListBuilder();

        $builder->doSetStatus('GroupType', $ids, $status);

    }


    public function editGroupType()
    {
        $aId = I('id', 0, 'intval');
        if (IS_POST) {
            if ($aId != 0) {
                $data = D('GroupType')->create();
                $res = D('GroupType')->save($data);
            } else {
                $data = D('GroupType')->create();
                $res = D('GroupType')->add($data);
            }
            if ($res) {
                $this->success(($aId == 0 ? '添加' : '编辑') . '成功');
            } else {
                $this->error(($aId == 0 ? '添加' : '编辑') . '失败');
            }

        } else {
            $builder = new AdminConfigBuilder();

            $types = M('GroupType')->where(array('pid' => 0))->select();
            $opt = array();
            foreach ($types as $type) {
                $opt[$type['id']] = $type['title'];
            }


            if ($aId != 0) {
                $wordCate1 = D('GroupType')->find($aId);
            } else {
                $wordCate1 = array('status' => 1, 'sort' => 0);
            }
            $builder->title('新增分类')->keyId()->keyText('title', '标题')->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt)
                ->keyStatus()->keyCreateTime()->keyText('sort', '排序')
                ->data($wordCate1)
                ->buttonSubmit(U('Group/editGroupType'))->buttonBack()->display();
        }
    }


    public function sortGroupType($ids = null)
    {
        if (IS_POST) {
            $builder = new AdminSortBuilder();
            $builder->doSort('GroupType', $ids);
        } else {
            $map['status'] = array('egt', 0);
            $list = D('GroupType')->where($map)->order("sort asc")->select();
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $builder = new AdminSortBuilder();
            $builder->meta_title = '分组排序';
            $builder->data($list);
            $builder->buttonSubmit(U('sortGroupType'))->buttonBack();
            $builder->display();
        }
    }

    public function groupTrash()
    {
        $aPage = I('get.page', 1, 'intval');
        $r = 20;
        //读取回收站中的数据
        $map = array('status' => '-1');
        $model = M('Group');
        $list = $model->where($map)->page($aPage, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('群组回收站')->buttonDeleteTrue(U('groupClear'))
            ->setStatusUrl(U('Group/setGroupStatus'))->buttonRestore()
            ->keyId()->keyLink('title', '标题', 'Group/post?group_id=###')
            ->keyCreateTime()->keyText('post_count', '文章数量')
            ->data($list)
            ->pagination($totalCount, $r)

            ->display();
    }

    public function groupClear($ids)
    {
        $builder = new AdminListBuilder();
        $builder->doDeleteTrue('Group', $ids);
    }

    /**
     * sortGroup 群组排序页面
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function sortGroup($ids)
    {
        if (IS_POST) {
            $builder = new AdminSortBuilder();
            $builder->doSort('Group', $ids);
        } else {
            //读取群组列表
            $list = M('Group')->where(array('status' => array('EGT', 0)))->order('sort asc')->select();

            //显示页面
            $builder = new AdminSortBuilder();
            $builder->title('群组排序')
                ->data($list)
                ->buttonSubmit(U('sortGroup'))->buttonBack()
                ->display();
        }

    }


    /**
     * setGroupStatus  设置群组状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setGroupStatus($ids, $status)
    {
        $ids = is_array($ids) ? $ids : array($ids);

        foreach ($ids as $v) {
            $title = D('Group/Group')->where(array('id' => $v))->field('title,uid')->find();
            if (I('get.tip') == 'verify') {
                if (modC('GROUP_AUDIT_SEND_WEIBO', 1, 'Group')) {
                    $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Group/Index/group', array('id' => $v));
                    if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                        D('Weibo/weibo')->addWeibo(is_login(), "管理员通过了群组【" . $title['title'] . "】的审核：" . $postUrl);
                    }
                    // 发送消息
                }
                D('Message')->sendMessageWithoutCheckSelf($title['uid'], '群组审核通知', "管理员通过了您创建的群组【" . $title['title'] . "】的审核", 'Group/Index/group', array('id' => $v));
            }
            if($status == 0){
                D('Message')->sendMessageWithoutCheckSelf($title['uid'], '群组审核通知', "管理员取消了您创建的群组【" . $title['title'] . "】的审核，您的群组现在为未审核状态，有问题请及时联系管理员。");
            }

        }


        foreach ($ids as $v) {
            S('group_' . $v, null);
        }
        S('group_post_exist_ids', null);
        S('group_exist_ids', null);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Group', $ids, $status);
    }


    public function editGroup($id = 0)
    {
        if (IS_POST) {
            $aId = I('post.id', 0, 'intval');
            $aTitle = I('post.title', '', 'text');
            $aCreateTime = I('post.create_time', 0, 'intval');
            $aStatus = I('post.status', 0, 'intval');
            $aAllowUserGroup = I('post.allow_user_group', 0, 'intval');
            $aLogo = I('post.logo', 0, 'intval');
            $aTypeId = I('post.type_id', 0, 'intval');
            $aDetail = I('post.detail', '', 'text');
            $aType = I('post.type', 0, 'intval');
            $aMemberAlias = I('post.member_alias', '', 'text');

            $isEdit = $aId ? true : false;
            //生成数据
            $data = array('title' => $aTitle, 'create_time' => $aCreateTime, 'status' => $aStatus, 'allow_user_group' => $aAllowUserGroup, 'logo' => $aLogo, 'type_id' => $aTypeId, 'detail' => $aDetail, 'type' => $aType, 'member_alias' => $aMemberAlias);
            //写入数据库
            $model = M('Group');
            if ($isEdit) {
                $data['id'] = $aId;
                $data = $model->create($data);
                $result = $model->where(array('id' => $aId))->save($data);

            } else {
                $data = $model->create($data);
                $data['uid'] = 1;
                $result = $model->add($data);
                if (!$result) {
                    $this->error('创建失败');
                }
            }
            S('group_list', null);
            //返回成功信息
            $this->success($isEdit ? '编辑成功' : '保存成功');
        } else {
            $aId = I('get.id', 0, 'intval');
            //判断是否为编辑模式
            $isEdit = $aId ? true : false;
            //如果是编辑模式，读取群组的属性
            if ($isEdit) {
                $group = M('Group')->where(array('id' => $aId))->find();
            } else {
                $group = array('create_time' => time(), 'post_count' => 0, 'status' => 1);
            }
            $groupType = D('GroupType')->where(array('status' => 1, 'pid' => 0))->limit(100)->select();
            foreach ($groupType as $k => $v) {
                $child = D('GroupType')->where(array('pid' => $v['id'], 'status' => 1))->order('sort asc')->select();
                //获取数组中第一父级的位置
                $key_name = array_search($v, $groupType);
                foreach ($child as $key => $val) {
                    $val['title'] = '------' . $val['title'];
                    //在父级后面添加数组
                    array_splice($groupType, $key_name + 1, 0, array($val));
                }
            }
            foreach ($groupType as $type) {
                $opt[$type['id']] = $type['title'];
            }
            //显示页面
            $builder = new AdminConfigBuilder();
            $builder
                ->title($isEdit ? '编辑群组' : '新增群组')
                ->keyId()->keyTitle()->keyTextArea('detail', '群组介绍')
                ->keyRadio('type', '群组类型', '群组的类型', array(0 => '公共群组', 1 => '私有群组'))
                ->keySelect('type_id', '分类', '选择分类', $opt)
                /* ->keyMultiUserGroup('allow_user_group', '允许发帖的用户组')*/
                ->keyStatus()
                ->keySingleImage('logo', '群组logo', '群组logo，300px*300px')->keyText('member_alias', '群成员昵称')->keyCreateTime()
                ->data($group)
                ->buttonSubmit(U('editGroup'))->buttonBack()
                ->display();
        }
    }


    public function post()
    {
        $aPage = I('get.page', 1, 'intval');
        $aGroupId = I('get.group_id', 0, 'intval');
        $aTitle = I('get.title', '', 'text');
        $aContent = I('get.content', '', 'text');
        $r = 20;

        $groups = D('group')->where(array('status' => 1))->field('id')->cache('group_exist_ids', 60 * 5)->select();
        $group_ids = getSubByKey($groups, 'id');

        //读取文章数据
        $map = array('status' => array('EGT', 0), 'group_id' => array('in', $group_ids));
        if ($aTitle != '') {
            $map['title'] = array('like', '%' . $aTitle . '%');
        }
        if ($aContent != '') {
            $map['content'] = array('like', '%' . $aContent . '%');
        }
        if ($aGroupId) $map['group_id'] = $aGroupId;
        $model = M('GroupPost');
        $list = $model->where($map)->order('last_reply_time desc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$v) {
            if ($v['is_top'] == 1) {
                $v['top'] = '版内置顶';
            } else if ($v['is_top'] == 2) {
                $v['top'] = '全局置顶';
            } else {
                $v['top'] = '不置顶';
            }
        }
        //读取群组基本信息
        if ($aGroupId) {
            $group = D('Group/Group')->getGroup($aGroupId);
            $groupTitle = ' - ' . $group['title'];
        } else {
            $groupTitle = '';
        }

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('文章管理' . $groupTitle)
            ->setStatusUrl(U('Group/setPostStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyLink('title', '标题', 'Group/reply?post_id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', '最后回复时间')->keyText('top', '是否置顶')->keyStatus()->keyDoActionEdit('group/index/edit?post_id=###')
            ->setSearchPostUrl(U('post'))->search('标题', 'title')->search('内容', 'content')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function postTrash()
    {
        $aPage = I('get.page', 1, 'intval');
        $r = 20;
        //读取文章数据
        $map = array('status' => -1);
        $model = M('GroupPost');
        $list = $model->where($map)->order('last_reply_time desc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('文章回收站')
            ->setStatusUrl(U('Group/setPostStatus'))->buttonRestore()->buttonDeleteTrue(U('postClear'))
            ->keyId()->keyLink('title', '标题', 'Group/reply?post_id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', '最后回复时间')->keyBool('is_top', '是否置顶')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function postClear($ids)
    {
        $builder = new AdminListBuilder();
        $builder->doDeleteTrue('GroupPost', $ids);
    }

    /**
     * setPostStatus  设置帖子状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setPostStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $ids = is_array($ids) ? $ids : array($ids);
        foreach ($ids as $v) {
            S('group_post_' . $v, null);
        }
        S('group_post_exist_ids', null);
        S('group_exist_ids', null);
        $builder->doSetStatus('GroupPost', $ids, $status);
    }


    public function reply()
    {
        //读取回复列表

        $aPage = I('get.page', 1, 'intval');
        $aPostId = I('get.post_id', 0, 'intval');
        $aUid = I('get.uid', 0, 'intval');
        $aKeyword = I('get.keyword', '', 'text');
        $r = 20;

        $groups = D('group')->where(array('status' => 1))->field('id')->cache('group_exist_ids', 60 * 5)->select();
        $group_ids = getSubByKey($groups, 'id');
        $posts = D('group_post')->where(array('status' => 1, 'group_id' => array('in', $group_ids)))->field('id')->cache('group_post_exist_ids', 60 * 5)->select();
        $post_ids = getSubByKey($posts, 'id');

        $map = array('status' => array('EGT', 0), 'post_id' => array('in', $post_ids));
        $aKeyword != '' && $map['content'] = array('like', '%' . $aKeyword . '%');
        $aUid != 0 && $map['uid'] = $aUid;
        if ($aPostId) $map['post_id'] = $aPostId;
        $model = M('GroupPostReply');
        $list = $model->where($map)->order('create_time asc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);
            $post = D('Group/GroupPost')->getPost($v['post_id']);
            $v['post_title'] = $post['title'];
            $v['show'] = '查看楼中楼回复';
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('回复管理')
            ->setStatusUrl(U('setReplyStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()
            ->keyLinkByFlag('post_title', '帖子标题', 'group/index/detail?id=###&#{$id}', 'post_id')
            ->keyText('uname', '发布者')->keyCreateTime()
            ->keyUpdateTime()->keyStatus()
            ->keyLink('show', '楼中楼', 'Admin/Group/lzlreply?id=###')->keyDoActionEdit('group/index/editreply?reply_id=###')
            ->data($list)
            ->setSearchPostUrl(U('reply'))->search('用户ID', 'uid')->search('关键词', 'keyword')
            ->pagination($totalCount, $r)
            ->display();
    }

    public function replyTrash()
    {

        $aPage = I('get.page', 1, 'intval');
        $r = 20;

        //读取回复列表
        $map = array('status' => -1);
        $model = M('GroupPostReply');
        $list = $model->where($map)->order('create_time asc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);

            $v['show'] = '查看楼中楼回复';
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('回复回收站')
            ->setStatusUrl(U('setReplyStatus'))->buttonRestore()->buttonDeleteTrue(U('postReplyClear'))
            ->keyId()->keyTruncText('content', '内容', 50)->keyText('uname', '发布者')->keyCreateTime()->keyUpdateTime()->keyStatus()->keyLink('show', '楼中楼', 'Admin/Group/lzlreplyTrash?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function postReplyClear($ids)
    {
        $builder = new AdminListBuilder();
        $builder->doDeleteTrue('GroupPostReply', $ids);
    }


    public function setReplyStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('GroupPostReply', $ids, $status);
    }


    public function postType()
    {
        $aPage = I('get.page', 1, 'intval');
        $r = 20;
        //读取数据
        $map = array('status' => array('GT', -1));
        $model = M('GroupPostCategory');
        $list = $model->where($map)->page($aPage, $r)->order('group_id asc, sort asc')->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$cate) {
            $group = D('Group')->where(array('id' => $cate['group_id']))->find();
            $cate['group_name'] = $group['title'];
            $cate['post_count'] = D('GroupPost')->where(array('cate_id' => $cate['id']))->count();
        }
        unset($cate);
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('分类管理')
            ->buttonNew(U('Group/editPostCate'))
            ->setStatusUrl(U('Group/setGroupPostCateStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            /*  ->buttonSort(U('Group/sortPostCate'))*/
            ->keyId()->keyText('group_name', '所属群组')->keyText('title', '标题')
            ->keyCreateTime()->keyText('post_count', '群组数量')->keyStatus()->keyDoActionEdit('editPostCate?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setGroupPostCateStatus($ids, $status)
    {

        $builder = new AdminListBuilder();
        $builder->doSetStatus('GroupPostCategory', $ids, $status);

    }

    public function editPostCate()
    {
        $aId = I('id', 0, 'intval');
        if (IS_POST) {
            $data = D('GroupPostCategory')->create();
            if ($aId != 0) {
                $res = D('GroupPostCategory')->save($data);
            } else {
                $res = D('GroupPostCategory')->add($data);
            }
            if ($res) {
                $this->success(($aId == 0 ? '添加' : '编辑') . '成功');
            } else {
                $this->error(($aId == 0 ? '添加' : '编辑') . '失败');
            }

        } else {
            $builder = new AdminConfigBuilder();

            $groups = D('Group')->where(array('status' => 1))->select();
            foreach ($groups as $group) {
                $opt[$group['id']] = $group['title'];
            }

            if ($aId != 0) {
                $wordCate1 = D('GroupPostCategory')->find($aId);
            } else {
                $wordCate1 = array('status' => 1, 'sort' => 0);
            }
            $builder->title('新增分类')->keyId()->keySelect('group_id', '所属群组', '', $opt)->keyText('title', '标题')
                ->keyStatus()->keyCreateTime()
                ->data($wordCate1)
                ->buttonSubmit(U('Group/editPostCate'))->buttonBack()->display();
        }
    }

    public function sortPostCate($ids = null)
    {
        if (IS_POST) {
            $builder = new AdminSortBuilder();
            $builder->doSort('GroupPostCategory', $ids);
        } else {
            $map['status'] = array('egt', 0);
            $list = D('GroupPostCategory')->where($map)->order("sort asc")->select();
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $builder = new AdminSortBuilder();
            $builder->meta_title = '分组排序';
            $builder->data($list);
            $builder->buttonSubmit(U('sortPostCate'))->buttonBack();
            $builder->display();
        }
    }


    public function lzlreply()
    {
        $aPage = I('get.page', 1, 'intval');
        $aId = I('get.id', 0, 'intval');
        $aUid = I('get.uid', 0, 'intval');
        $aKeyword = I('get.keyword', '', 'text');
        $r = 20;
        //读取回复列表
        $map = array('status' => array('EGT', 0));
        $aKeyword != '' && $map['content'] = array('like', '%' . $aKeyword . '%');
        $aUid != 0 && $map['uid'] = $aUid;
        if ($aId) $map['to_f_reply_id'] = $aId;
        $model = M('GroupLzlReply');
        $list = $model->where($map)->order('create_time asc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('楼中楼回复管理')
            ->setStatusUrl(U('setLzlReplyStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyTruncText('content', '内容', 50)->keyText('uname', '发布者')->keyTime('create_time', '创建时间')->keyStatus()->keyDoActionEdit('editLzlReply?id=###')
            ->data($list)
            ->setSearchPostUrl(U('lzlreply', array('id' => $aId)))->search('用户ID', 'uid')->search('关键词', 'keyword')
            ->pagination($totalCount, $r)
            ->display();
    }

    public function lzlreplyTrash()
    {
        $aPage = I('get.page', 1, 'intval');
        $aId = I('get.id', 0, 'intval');
        $r = 20;

        //读取回复列表
        $map = array('status' => -1);
        if ($aId) $map['to_f_reply_id'] = $aId;
        $model = M('GroupLzlReply');
        $list = $model->where($map)->order('create_time asc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('回复回收站')
            ->setStatusUrl(U('setLzlReplyStatus'))->buttonRestore()->buttonDeleteTrue(U('lzlClear'))
            ->keyId()->keyTruncText('content', '内容', 50)->keyText('uname', '发布者')->keyCreateTime()->keyUpdateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function lzlClear($ids)
    {
        $builder = new AdminListBuilder();
        $builder->doDeleteTrue('GroupLzlReply', $ids);
    }

    public function setLzlReplyStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('GroupLzlReply', $ids, $status);

    }


    public function editLzlReply()
    {
        $aId = I('id', 0, 'intval');
        if (IS_POST) {
            $aContent = I('post.content', '', 'text');
            $aCreateTime = I('post.create_time', 0, 'intval');
            $aStatus = I('post.status', 0, 'intval');
            //判断是否为编辑模式
            $isEdit = $aId ? true : false;

            //写入数据库
            $data = array('content' => $aContent, 'create_time' => $aCreateTime, 'status' => $aStatus);
            $model = M('GroupLzlReply');
            if ($isEdit) {
                $result = $model->where(array('id' => $aId))->save($data);
            } else {
                $result = $model->add($data);
            }

            //如果写入出错，则显示错误消息
            if (!$result) {
                $this->error($isEdit ? '编辑失败' : '创建失败');
            }

            //返回成功消息
            $this->success($isEdit ? '编辑成功' : '创建成功', U('lzlreply'));
        } else {
            //判断是否为编辑模式
            $isEdit = $aId ? true : false;

            //读取回复内容
            if ($isEdit) {
                $model = M('GroupLzlReply');
                $reply = $model->where(array('id' => $aId))->find();
            } else {
                $reply = array('status' => 1);
            }

            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? '编辑回复' : '创建回复')
                ->keyId()->keyTextArea('content', '内容')->keyTime('create_time', '创建时间')->keyStatus()
                ->data($reply)
                ->buttonSubmit(U('editLzlReply'))->buttonBack()
                ->display();
        }

    }


}
