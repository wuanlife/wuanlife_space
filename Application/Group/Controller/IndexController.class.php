<?php
namespace Group\Controller;

use Think\Controller;

class IndexController extends BaseController
{


    public function _initialize()
    {
        parent::_initialize();
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), is_login());
        $this->assign('myInfo', $myInfo);
    }

    public function index()
    {
        if (is_login()) {
            redirect(U('my'));
        } else {
            redirect(U('discover'));
        }
    }


    public function groups()
    {
        $aKeyword = $this->parseSearchKey('keyword');
        $aPage = I('get.page', 1, 'intval');
        $r = 20;
        $aOrder = I('get.order', 'create_time', 'text');
        $aReverse = I('get.reverse', 'desc', 'text');
        $aCate = I('get.cate', 0, 'intval');
        $aUid = I('get.uid', 0, 'intval');

        if ($aOrder == 'activity') {
            $this->assign('order', '按活跃度排序');
        } elseif ($aOrder == 'member') {
            //todo 根据成员数排序
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $count = $Model->query("SELECT group_id,count(group_id) as count from opensns_group_member group by group_id order by count desc");
            $ids = getSubByKey($count, 'group_id');
            $ids = implode(',', $ids);
            $aOrder = "find_in_set( id ,'" . $ids . "') ";
            $this->assign('order', '按成员数排序');
        } else {
            $aOrder = 'create_time';
            $this->assign('order', '按最新创建排序');
        }
        if (!empty($aCate)) {
            $gid = D('GroupType')->where('pid=' . $aCate)->field('id')->select();
            $gids = getSubByKey($gid, 'id');
            $gids[] = $aCate;
            $param['where']['type_id'] = array('in', $gids);

            $this->assign('name', get_type_name($aCate));

            $this->assign('group_cate', $aCate);
            $this->assign('keyword', array(0 => 'cate', 1 => $aCate));
            $this->setTitle('{$name}');
        }
        if (!empty($aKeyword)) {
            $param['where']['title'] = array('like', '%' . $aKeyword . '%');
            $this->assign('name', $aKeyword);
            $this->assign('keyword', array(0 => 'keywords', 1 => $aKeyword));
        }
        if ($aUid != 0) {
            $param['where']['uid'] = $aUid;
            $this->assign('name', get_nickname($aUid) . '的群组');
            $this->assign('keyword', array(0 => 'uid', 1 => $aUid));
        }

        $param['where']['status'] = 1;
        $param['page'] = $aPage;
        $param['count'] = $r;
        $param['order'] = $aOrder . ' ' . $aReverse;
        $param['field'] = 'id';
        $group_list = D('Group/Group')->getList($param);
        //获取总数
        $totalCount = D('Group/Group')->where($param['where'])->count();
        $this->assign('totalCount', $totalCount);
        $this->assign('r', $r);
        $this->assign('group_list', $group_list);
        $this->assignGroupTypes();
        $this->setTitle('群组首页');
        $this->assign('current', 'groups');
        $this->display();
    }

    public function group()
    {
        $aId = I('get.id', 0, 'intval');
        $aPage = I('get.page', 1, 'intval');
        $aOrder = I('get.order', '', 'text');
        $aType = I('get.type', 'post', 'text');
        $aTitle = I('post.title', '', 'text');
        $aCate = I('get.cate', 0, 'intval');
        $r = 20;

        $this->requireGroupExists($aId);

        D('GroupMember')->setLastView($aId);
        $this->assignNotice($aId);
        if (!empty($aOrder)) {
            $aType = 'post';
            if ($aOrder == 'ctime') {
                $this->assign('order', 0);
                $aOrder = 'create_time desc';
            } else if ($aOrder == 'reply') {
                $aOrder = 'last_reply_time desc';
                $this->assign('order', 1);
            }
        }
        // 按名称查询帖子
        if (!empty($aTitle)) {
            $aType = 'post';
            $map['title'] = array('like', "%{$aTitle}%");
            $this->assign('search_key', $aTitle);
        }
        //按分类查询帖子
        if (!empty($aCate)) {
            $aType = 'post';
            $map['cate_id'] = $aCate;
        }
        //读取置顶列表
        $list_top = D('GroupPost')->getList(array('where' => 'status=1 AND is_top=1 and group_id=' . $aId, 'order' => $aOrder));
        foreach ($list_top as &$v) {
            $v = D('GroupPost')->getPost($v);
            $v['group'] = D('Group')->getGroup($v['group_id']);
        }
        unset($v);
        //帖子页面显示
        if ($aType == 'post') {
            $r = 10;
            //读取帖子列表
            $map['status'] = 1;
            $map['group_id'] = $aId;
            empty($aOrder) && $aOrder = 'create_time desc';
            $list = D('GroupPost')->getList(array('where' => $map, 'order' => $aOrder, 'page' => $aPage, 'count' => $r));
            $totalCount = D('GroupPost')->where($map)->count();
        }
        if ($aType == 'new') {
            $map = array('group_id' => $aId);
            $list = D('GroupDynamic')->getList(array('where' => $map, 'order' => 'create_time desc', 'page' => $aPage, 'count' => $r));
            $totalCount = D('GroupDynamic')->where($map)->count();
        }

        //member页面显示
        if ($aType == 'member') {
            $map = array('group_id' => $aId, 'status' => 1);
            $list = D('GroupMember')->where($map)->order('position desc , create_time asc')->page($aPage, 30)->cache(true, 60)->select();
            foreach ($list as &$user) {
                $user['user'] = query_user(array('avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['uid']);
            }
            $totalCount = D('GroupMember')->where($map)->count();
        }
        $this->assign('list', $list);
        $this->assign('totalCount', $totalCount);
        //显示页面
        $this->assign('group_id', $aId);
        if ($aId != 0) {
            $this->assignGroup($aId);
            $this->setTitle('{$group.title}');

        } else {
            $this->setTitle('群组');
            $this->assign('group', array('title' => '群组 Group'));
        }
        $this->assign('list_top', $list_top);
        $this->assignPostCategory($aId);
        $this->assign('r', $r);
        $this->assign('type', $aType);
        $this->display();
    }

    public function select()
    {

        $posts = D('GroupPost')->where(array('is_top' => 1, 'status' => 1))->findPage();

        $supportModel = D('Common/Support');
        foreach ($posts['data'] as &$v) {

            $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
        }
        unset($v);
        $this->assign('posts', $posts);
        $groupModel = D('Group');
        $groups = $groupModel->field('id')->order('rand()')->where(array('status' => 1))->limit(15)->select();
        foreach ($groups as &$v) {
            $v = $groupModel->getGroup($v['id']);
        }
        $this->assign('groups', $groups);


        $this->assign('current', 'select');
        $this->display();
    }

    public function discover()
    {
        $groupModel = D('Group');
        $group_ids = $groupModel->where(array('status' => 1))->field('id')->select();
        $group_ids = getSubByKey($group_ids, 'id');
        $posts = D('GroupPost')->where(array('status' => 1, 'group_id' => array('in', $group_ids)))->order('create_time desc')->findPage(15);

        $supportModel = D('Common/Support');
        foreach ($posts['data'] as &$v) {
            $v['group'] = $groupModel->getGroup($v['group_id']);
            $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
        }
        unset($v);
        $this->assign('posts', $posts);

        $groups = $groupModel->field('id')->order('rand()')->where(array('status' => 1))->limit(15)->select();
        foreach ($groups as &$v) {
            $v = $groupModel->getGroup($v['id']);
        }
        $this->assign('groups', $groups);


        $this->assign('current', 'discover');
        $this->display();
    }

    public function create()
    {
        if (IS_POST) {
            $aGroupId = I('post.group_id', 0, 'intval');
            $aGroupType = I('post.group_type', 0, 'intval');
            $aTitle = I('post.title', '', 'text');
            $aDetail = I('post.detail', '', 'text');
            $aLogo = I('post.logo', 0, 'intval');
            $aType = I('post.type', 0, 'intval');
            $aBackground = I('post.background', 0, 'intval');
            $aMemberAlias = I('post.member_alias', '成员', 'text');
            if (empty($aTitle)) {
                $this->error('请填写群组名称');
            }
            if (utf8_strlen($aTitle) > 20) {
                $this->error('群组名称最多20个字');
            }
            if ($aGroupType == -1) {
                $this->error('请选择群组分类');
            }
            if (empty($aDetail)) {
                $this->error('请填写群组介绍');
            }
            $model = D('Group');
            $isEdit = $aGroupId ? true : false;
            if ($isEdit) {
                $this->requireLogin();
                $this->requireGroupExists($aGroupId);
                $this->checkActionLimit('edit_group', 'Group', $aGroupId, is_login(), true);
                $this->checkAuth('Group/Index/editGroup', get_group_admin($aGroupId), '您无编辑群组权限');
            } else {

                $this->requireLimit();

                $this->checkActionLimit('add_group', 'Group', 0, is_login(), true);
                $this->checkAuth('Group/Index/addGroup', -1, '您无添加群组权限');
            }
            $need_verify = modC('GROUP_NEED_VERIFY', 0, 'GROUP');

            if ($isEdit) {
                $data = array('id' => $aGroupId, 'type_id' => $aGroupType, 'title' => $aTitle, 'detail' => $aDetail, 'logo' => $aLogo, 'type' => $aType, 'background' => $aBackground, 'member_alias' => $aMemberAlias);
                $data['status'] = $need_verify ? 0 : 1;
                $result = $model->editGroup($data);
                $group_id = $aGroupId;
            } else {
                $data = array('type_id' => $aGroupType, 'title' => $aTitle, 'detail' => $aDetail, 'logo' => $aLogo, 'type' => $aType, 'uid' => is_login(), 'background' => $aBackground, 'member_alias' => $aMemberAlias);
                $data['status'] = $need_verify ? 0 : 1;
                $result = $model->createGroup($data);
                if (!$result) {
                    $this->error('创建群组失败：' . $model->getError());
                }
                $group_id = $result;
                //向GroupMember表添加创建者成员
                D('GroupMember')->addMember(array('uid' => is_login(), 'group_id' => $group_id, 'status' => 1, 'position' => 3));
            }
            if ($need_verify) {
                $message = '创建成功，请耐心等候管理员审核。';
                // 发送消息
                D('Message')->sendMessage(1,'群组创建审核', get_nickname(is_login()) . "创建了群组【{$aTitle}】，快去审核吧。",  'admin/group/unverify');
                $this->success($message, U('group/index/index'));
            }

            // 发送微博
            if (D('Module')->checkInstalled('Weibo')) {
                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Group/Index/group', array('id' => $group_id));
                if ($isEdit && check_is_in_config('edit_group', modC('GROUP_SEND_WEIBO', 'add_group,edit_group', 'GROUP'))) {
                    D('Weibo/Weibo')->addWeibo(is_login(), "我修改了群组【" . $aTitle . "】：" . $postUrl);
                }
                if (!$isEdit && check_is_in_config('add_group', modC('GROUP_SEND_WEIBO', 'add_group,edit_group', 'GROUP'))) {
                    D('Weibo/Weibo')->addWeibo(is_login(), "我创建了一个新的群组【" . $aTitle . "】：" . $postUrl);
                }

            }

            //显示成功消息
            $message = $isEdit ? '编辑成功。' : '发表成功。';
            $url = $isEdit ? 'refresh' : U('group/index/group', array('id' => $group_id));
            $this->success($message, $url);
        } else {

            $this->requireLimit();

            $this->requireLogin();
            $this->assignGroupAllType();
            $this->setTitle('创建群组');
            $this->display();
        }
    }

    private function requireLimit(){
        $model = D('Group');
        $limit = modC('GROUP_LIMIT', 5, 'GROUP');
        $count = $model->getUserGroupCount(is_login());
        if($count >= $limit){
            $this->error('您创建的群组数已达上限：'.$limit);
        }
    }
    public function my()
    {
        $aPage = I('get.page', 1, 'intval');
        $r = 20;
        $this->requireLogin();
        $groupModel = D('Group');
        $group_ids = D('GroupMember')->getGroupIds(array('where' => array('uid' => is_login(), 'status' => 1)));
        $myattend = $groupModel->getList(array('where' => array('id' => array('in', $group_ids), 'status' => 1), 'page' => $aPage, 'count' => $r, 'order' => 'uid = ' . is_login() . ' desc ,uid asc'));
        foreach ($myattend as &$v) {
            $v = $groupModel->getGroup($v);
        }
        unset($v);
        $posts = D('GroupPost')->where(array('group_id' => array('in', $group_ids), 'status' => 1))->order('create_time desc')->findPage(10);

        $supportModel = D('Common/Support');
        foreach ($posts['data'] as &$v) {
            $v['group'] = $groupModel->getGroup($v['group_id']);
            $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
        }

        $this->assign('posts', $posts);


        $this->assign('r', $r);
        $this->assign('groups', $myattend);
        $this->assign('current', 'my');
        $this->setTitle('我的群组');
        $this->display();
    }

    public function mygroup()
    {
        $aPage = I('get.page', 1, 'intval');
        $r = 20;
        $this->requireLogin();
        $group_ids = D('GroupMember')->getGroupIds(array('where' => array('uid' => is_login(), 'status' => 1)));
        $myattend = D('Group')->getList(array('where' => array('id' => array('in', $group_ids), 'status' => 1), 'page' => $aPage, 'count' => $r, 'order' => 'uid = ' . is_login() . ' desc ,uid asc'));


        $totalCount = D('Group')->where(array('id' => array('in', $group_ids), 'status' => 1))->count();
        $this->assign('totalCount', $totalCount);
        $this->assign('r', $r);
        $this->assign('mygroup', $myattend);
        $this->assign('current', 'my');
        $this->setTitle('我的群组');
        $this->display();
    }


    public function detail()
    {
        $aId = I('get.id', 0, 'intval');
        $aPage = I('get.page', 1, 'intval');
        $r = 10;
        $post = D('GroupPost')->getPost($aId);

        $post['group'] = D('Group')->getGroup($post['group_id']);
        $post['content'] = D('ContentHandler')->displayHtmlContent($post['content']);
        $post['content'] = limit_picture_count($post['content']);

        $this->assignNotice($post['group_id']);
        //检测群组、帖子是否存在
        if (!$post || !group_is_exist($post['group_id'])) {
            $this->error('找不到该帖子');
        }
        //增加浏览次数
        D('GroupPost')->where(array('id' => $aId))->setInc('view_count');
        //读取回复列表
        $map = array('post_id' => $aId, 'status' => 1);
        $replyList = D('GroupPostReply')->getList(array('where' => $map, 'order' => 'create_time asc', 'page' => $aPage, 'count' => $r));
        $replyTotalCount = D('GroupPostReply')->where($map)->count();
        //显示页面
        $this->assign('group_id', $post['group_id']);
        $this->assign('post', $post);
        $this->setTitle('{$post.title|op_t} —— 贴吧');
        $this->assign('page', $aPage);
        $this->assign('r', $r);
        $this->assign('replyList', $replyList);
        $this->assign('replyTotalCount', $replyTotalCount);
        $this->assignGroup($post['group_id']);
        $this->display();
    }


    public function edit()
    {

        $aGroupId = I('get.group_id', 0, 'intval');
        $aPostId = I('get.post_id', 0, 'intval');
        //判断是不是为编辑模式
        $isEdit = $aPostId ? true : false;
        //如果是编辑模式的话，读取帖子，并判断是否有权限编辑


        if ($isEdit) {
            $this->requireLogin();
            $this->requirePostExists($aPostId);
            $this->checkAuth('Group/Index/edit', get_post_admin($aPostId), '您无编辑帖子权限');
            $post = D('GroupPost')->getPost($aPostId);
        } else {
            if (is_joined($aGroupId) != 1) {
                $this->error('您还未加入群组无法发帖。');
            }
            $this->checkAuth('Group/Index/addPost', -1, '您无添加帖子权限');
            $post = array('group_id' => $aGroupId);
        }
        //获取群组id
        $aGroupId = $aGroupId ? intval($aGroupId) : $post['group_id'];
        $this->assignPostCategory($aGroupId);
        $this->assignGroup($aGroupId);
        $this->assign('group_id', $aGroupId);
        $this->setTitle('{$title}');
        $this->assign('title', $isEdit ? '编辑帖子' : '发表新帖');
        $this->assign('post', $post);
        $this->assign('isEdit', $isEdit);
        $this->display();
    }


    public function doEdit()
    {
        $aPostId = I('post.post_id', 0, 'intval');
        $aGroupId = I('post.group_id', 0, 'intval');
        $aTitle = I('post.title', '', 'text');


        $aContent = I('post.content', '', 'filter_content');
        $aCategory = I('post.category', 0, 'intval');

        if (is_joined($aGroupId) != 1) {
            $this->error('您无编辑帖子权限');
        }

        //判断是不是编辑模式
        $isEdit = $aPostId ? true : false;
        //如果是编辑模式，确认当前用户能编辑帖子
        $this->requireLogin();
        $this->requireGroupExists($aGroupId);

        if ($isEdit) {
            $this->requirePostExists($aPostId);
            $this->checkActionLimit('edit_group_post', 'GroupPost', $aPostId, is_login(), true);
            $this->checkAuth('Group/Index/edit', get_post_admin($aPostId), '您无编辑帖子权限');

        } else {
            $this->checkActionLimit('add_group_post', 'GroupPost', 0, is_login(), true);
            $this->checkAuth('Group/Index/addPost', -1, '您无添加帖子权限');
        }


        if (empty($aGroupId)) {
            $this->error('请选择帖子所在的群组');
        }
        if (empty($aTitle)) {
            $this->error('请填写帖子标题');
        }
        if (empty($aContent)) {
            $this->error('请填写帖子内容');
        }


        $model = D('GroupPost');
        $cover = get_pic($aContent);
        $cover = $cover == null ? '' : $cover;
        $len = modC('SUMMARY_LENGTH', 50);
        if ($isEdit) {
            $data = array('id' => $aPostId, 'title' => $aTitle, 'summary' => mb_substr(text($aContent), 0, $len, 'utf-8'), 'cover' => $cover, 'content' => $aContent, 'parse' => 0, 'group_id' => $aGroupId, 'cate_id' => $aCategory);
            $result = $model->editPost($data);
            //添加到最新动态
            $dynamic['group_id'] = $aGroupId;
            $dynamic['uid'] = is_login();
            $dynamic['type'] = 'update_post';
            $dynamic['row_id'] = $aPostId;
            D('GroupDynamic')->addDynamic($dynamic);
            if (!$result) {
                $this->error('编辑失败：' . $model->getError());
            }
        } else {
            $data = array('uid' => is_login(), 'title' => $aTitle, 'summary' => mb_substr(text($aContent), 0, $len, 'utf-8'), 'cover' => $cover, 'content' => $aContent, 'parse' => 0, 'group_id' => $aGroupId, 'cate_id' => $aCategory);
            $result = $model->createPost($data);
            if (!$result) {
                $this->error('发表失败。');
            }
            $aPostId = $result;
            //添加到最新动态
            $dynamic['group_id'] = $aGroupId;
            $dynamic['uid'] = is_login();
            $dynamic['type'] = 'post';
            $dynamic['row_id'] = $aPostId;
            D('GroupDynamic')->addDynamic($dynamic);
            //增加活跃度
            D('Group')->where(array('id' => $aGroupId))->setInc('activity');
            D('GroupMember')->where(array('group_id' => $aGroupId, 'uid' => is_login()))->setInc('activity');
        }

        //实现发布帖子发布图片微博(公共内容)
        $group = D('Group')->getGroup($aGroupId);
        $this->sendWeibo($aPostId, $isEdit, $group);
        //显示成功消息
        $message = $isEdit ? '编辑成功。' : '发表成功。' . cookie('score_tip');
        $this->success($message, U('Group/Index/detail', array('id' => $aPostId)));
    }


    protected function sendWeibo($aPostId, $isEdit, $group)
    {
        if (D('Module')->checkInstalled('Weibo')) {
            $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Group/Index/detail', array('id' => $aPostId));

            $post = D('GroupPost')->getPost($aPostId);

            $type = 'feed';
            $feed_data = array();
            //解析并成立图片数据
            $arr = array();
            preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/", $post['content'], $arr); //匹配所有的图片
            if (!empty($arr[0])) {
                $feed_data['attach_ids'] = array();
                $dm = __ROOT__; //前缀图片多余截取

                $max = count($arr[1]) > 9 ? 9 : count($arr[1]);
                for ($i = 0; $i < $max; $i++) {
                    $tmparray = strpos($arr[1][$i], $dm);
                    $is_local = !is_bool($tmparray);
                    if ($is_local) {
                        $path = cut_str($dm, $arr[1][$i], 'l');
                        $result_id = D('Home/Picture')->where(array('path' => $path))->getField('id');
                    } else {
                        $path = $arr[1][$i];
                        $result_id = D('Home/Picture')->where(array('path' => $path))->getField('id');
                    }

                    if (!$result_id) {
                        $dr = '';
                        if (is_bool(strpos($path, 'http://'))) {
                            $dr = 'local';
                        }
                        $result_id = D('Home/Picture')->add(array('type' => $dr, 'path' => $path, 'url' => $path, 'status' => 1, 'create_time' => time()));
                    }

                    $feed_data['attach_ids'][] = $result_id;
                }
                $feed_data['attach_ids'] = implode(',', $feed_data['attach_ids']);
            }

            $feed_data['attach_ids'] != false && $type = "image";
            if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                if ($isEdit && check_is_in_config('edit_group_post', modC('GROUP_POST_SEND_WEIBO', 'add_group_post,edit_group_post', 'GROUP'))) {
                    D('Weibo/Weibo')->addWeibo(is_login(), "我在群组【{$group['title']}】里更新了帖子【" . $post['title'] . "】：" . $postUrl, $type, $feed_data);
                }
                if (!$isEdit && check_is_in_config('add_group_post', modC('GROUP_POST_SEND_WEIBO', 'add_group_post,edit_group_post', 'GROUP'))) {
                    D('Weibo/Weibo')->addWeibo(is_login(), "我在群组【{$group['title']}】里发表了一个新的帖子【" . $post['title'] . "】：" . $postUrl, $type, $feed_data);
                }
            }
        }
    }

    public function doBookmark()
    {

        $aPostId = I('get.post_id', 0, 'intval');
        $aFlag = I('get.flag', 0, 'intval');
        //确认用户已经登录
        $this->requireLogin();

        $this->requirePostExists($aPostId);
        $this->checkAuth(null, -1, '您无收藏权限');


        //写入数据库
        if ($aFlag) {
            $result = D('GroupBookmark')->addBookmark(is_login(), $aPostId);
            if (!$result) {
                $this->error('收藏失败');
            }
        } else {
            $result = D('GroupBookmark')->removeBookmark(is_login(), $aPostId);
            if (!$result) {
                $this->error('取消失败');
            }
        }
        //返回成功消息
        if ($aFlag) {
            $this->success('收藏成功');
        } else {
            $this->success('取消成功');
        }
    }

    public function recommend()
    {
        $aPostId = I('get.post_id', 0, 'intval');
        $aTop = I('get.top', 1, 'intval');
        $aTop && $aTop = 1;

        $group_id = $this->getGroupIdByPost($aPostId);
        $this->requirePostExists($aPostId);
        $this->checkAuth(null, get_group_admin($group_id), '您无置顶权限');

        $res = D('GroupPost')->where(array('id' => $aPostId, 'status' => 1))->setField('is_top', $aTop);

        if ($res) {
            S('group_post_' . $aPostId, null);
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }


    public function delPostReply()
    {
        $ReplyId = I('post.reply_id', 0, 'intval');
        $this->requireLogin();
        $this->checkAuth(null, get_reply_admin($ReplyId), '您无删除权限');

        $res = D('GroupPostReply')->delPostReply($ReplyId);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }


    public function doSendLzlReply()
    {

        $aToFReplyId = I('post.to_f_reply_id', 0, 'intval');
        $aToReplyId = I('post.to_reply_id', 0, 'intval');
        $aContent = I('post.content', '', 'text');
        $model = D('GroupLzlReply');
        $reply = D('GroupPostReply')->getReply($aToFReplyId);
        $lzl = $model->getLzlReply($aToReplyId);
        //确认用户已经登录
        $this->requireLogin();
        $this->checkActionLimit('add_group_lzl_reply', 'GroupLzlReply', 0, is_login(), true);
        $this->checkAuth(null, -1);


        if (empty($aContent)) {
            $this->error('内容不能为空');
        }


        //写入数据库
        $data['post_id'] = $reply['post_id'];
        $data['to_f_reply_id'] = $aToFReplyId;
        $data['to_reply_id'] = $aToReplyId;
        $data['content'] = $aContent;
        $data['uid'] = is_login();
        $data['to_uid'] = $lzl['uid'] ? $lzl['uid'] : $reply['uid'];
        $result = $model->addLzlReply($data);
        M('GroupPost')->where(array('id' => $data['post_id']))->setInc('reply_count');
        //增加活跃度
        $group_id = $this->getGroupIdByPost($reply['post_id']);
        D('Group')->where(array('id' => $group_id))->setInc('activity');
        D('GroupMember')->where(array('group_id' => $group_id, 'uid' => is_login()))->setInc('activity');

        if (!$result) {
            $this->error('发布失败：' . $model->getError());
        }

        //发送评论
        $res['data'] = $result;
        $res['html'] = R('Detail/lzlReplyHtml', array('lzl_id' => $res['data']), 'Widget');
        $res['status'] = 1;
        $res['info'] = '回复成功！' . cookie('score_tip');
        $this->ajaxReturn($res);
    }

    public function lzlList()
    {


        $aToFReplyId = I('post.reply_id', 0, 'intval');
        $aPage = I('post.page', 1, 'intval');
        $r = modC('GROUP_LZL_SHOW_COUNT', 5, 'GROUP');

        $order = modC('GROUP_LZL_REPLY_ORDER', 0, 'GROUP') == 1 ? 'create_time asc' : 'create_time desc';

        $lzlModel = D('GroupLzlReply');
        $map['to_f_reply_id'] = $aToFReplyId;
        $map['status'] = 1;
        $list = $lzlModel->getList(array('where' => $map, 'order' => $order, 'page' => $aPage, 'count' => $r));

        $totalCount = $lzlModel->where($map)->count();
        $this->assign('lzl_list', $list);

        $data['to_f_reply_id'] = $aToFReplyId;
        $pageCount = ceil($totalCount / $r);
        $html = getPageHtml('group_lzl_page', $pageCount, $data, $aPage);
        $this->assign('html', $html);

        $resutl = $this->fetch('lzllist');
        $this->ajaxReturn($resutl);
    }


    public function loadLzl()
    {
        $aReplyId = I('post.reply_id', 0, 'intval');
        $html = R('LzlReply/lzlHtml', array('reply_id' => $aReplyId), 'Widget');
        $this->ajaxReturn($html);
    }


    public function delLzlReply()
    {

        $aId = I('post.id', 0, 'intval');
        $this->requireLogin();

        $this->checkAuth(null, get_lzl_admin($aId));

        $res = D('GroupLzlReply')->delLzlReply($aId);

        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    // todo 帖子列表，用于搜索等
    public function posts()
    {
        $aPostCate = I('get.post_cate', 0, 'intval');
        if (!empty($aPostCate)) {
            $where['cate_id'] = $aPostCate;
            $this->assign('name', get_post_category($aPostCate));
            $this->assign('postCate', $aPostCate);
        }
        $where['status'] = 1;
        $post_list = D('Group/GroupPost')->getList(array('where' => $where));
        $this->assign('list', $post_list);
        $this->assignPostCategory();
        $this->display();
    }


    public function editReply()
    {
        $aReplyId = I('reply_id', 0, 'intval');
        $this->requireLogin();

        $this->checkAuth(null, get_reply_admin($aReplyId));
        if (IS_POST) {
            $this->checkActionLimit('edit_group_reply', 'GroupPostReply', $aReplyId, is_login(), true);
            $aContent = I('post.content', '', 'filter_content');
            $groipReplyModel = D('GroupPostReply');
            $post = $groipReplyModel->getReply($aReplyId);

            $data['id'] = $aReplyId;
            $data['content'] = $aContent;
            $data['update_time'] = time();


            $res = $groipReplyModel->editReply($data);
            if ($res) {

                $this->success('编辑回复成功', U('Group/Index/detail', array('id' => $post['post_id'])));
            } else {
                $this->error("编辑回复失败");
            }
        } else {
            if ($aReplyId) {
                $reply = D('GroupPostReply')->getReply($aReplyId);
            } else {
                $this->error('参数出错！');
            }
            $this->setTitle('编辑回复 —— 群组');
            //显示页面
            $this->assign('reply', $reply);
            $this->display();
        }

    }


    public function doReply()
    {
        $aPostId = I('get.post_id', 0, 'intval');
        $aContent = I('post.content', '', 'filter_content');

        // 获取群组ID
        $group_id = $this->getGroupIdByPost($aPostId);
        $this->requireLogin();
        $this->checkActionLimit('add_group_reply', 'GroupPostReply', 0, is_login(), true);
        $this->checkAuth();

        //添加到数据库
        $model = D('GroupPostReply');
        $data['post_id'] = $aPostId;
        $data['content'] = $aContent;
        $data['uid'] = is_login();
        $result = $model->addReply($data);

        //添加到最新动态
        $dynamic['group_id'] = $group_id;
        $dynamic['uid'] = is_login();
        $dynamic['type'] = 'reply';
        $dynamic['row_id'] = $result;
        D('GroupDynamic')->addDynamic($dynamic);
        //增加活跃度

        M('Group')->where(array('id' => $group_id))->setInc('activity');
        M('GroupPost')->where(array('id' => $data['post_id']))->setInc('reply_count');
        M('GroupMember')->where(array('group_id' => $group_id, 'uid' => is_login()))->setInc('activity');
        if (!$result) {
            $this->error('回复失败：' . $model->getError());
        }
        //显示成功消息
        $this->success('回复成功。' . cookie('score_tip'), 'refresh');

    }


    public function attend()
    {
        $aGroupId = I('group_id', 0, 'intval');
        $this->requireGroupExists($aGroupId);
        $this->requireLogin();
        $this->checkAuth();

        //判断是否已经加入
        if (is_joined($aGroupId) == 1) {
            $this->error('已经加入了该群组');
        }
        // 已经加入但还未审核
        if (is_joined($aGroupId) == 2) {
            $this->error('请耐心等待管理员审核');
        }
        $uid = is_login();
        $group = D('Group')->getGroup($aGroupId);

        //要存入数据库的数据
        $data['group_id'] = $aGroupId;
        $data['uid'] = $uid;
        $data['position'] = 1;
        $info = '';
        if ($group['type'] == 1) {
            // 群组为私有的。
            $data['status'] = 0;
            $res = D('GroupMember')->addMember($data);
            $info = '，等待群组管理员审核！';
            // 发送消息
            D('Message')->sendMessage($group['uid'], '加入群组审核',get_nickname($uid) . "请求加入群组【{$group['title']}】", 'group/Manage/member', array('group_id' => $aGroupId, 'status' => 0), $uid);
        } else {
            // 群组为公共的
            $data['status'] = 1;
            $res = D('GroupMember')->addMember($data);
            //添加到最新动态
            $dynamic['group_id'] = $aGroupId;
            $dynamic['uid'] = $uid;
            $dynamic['type'] = 'attend';
            D('GroupDynamic')->addDynamic($dynamic);
        }
        if ($res) {
            D('Group')->where(array('id'=>$aGroupId))->setInc('member_count');
            S('group_is_join_' . $aGroupId . '_' . $uid, null);
            S('group_member_count_' . $group['id'], null);
            $this->success('加入成功' . $info, 'refresh');
        } else {
            $this->error('加入失败');
        }

    }


    public function quit()
    {
        $aGroupId = I('group_id', 0, 'intval');
        $this->requireLogin();
        $this->requireGroupExists($aGroupId);
        $this->checkAuth();
        $uid = is_login();

        // 判断是否是创建者，创建者无法退出
        $group = D('Group')->getGroup($aGroupId);
        if ($group['uid'] == $uid) {
            $this->error('创建者无法退出群组');
        }
        // 判断是否在该群组内
        if (is_joined($aGroupId) == 0) {
            $this->error('你不在该群组中');
        }

        $res = D('GroupMember')->delMember(array('group_id' => $aGroupId, 'uid' => $uid));
        if ($res) {
            //添加到最新动态
            $dynamic['group_id'] = $aGroupId;
            $dynamic['uid'] = $uid;
            $dynamic['type'] = 'quit';
            D('GroupDynamic')->addDynamic($dynamic);

            D('Group')->where(array('id'=>$aGroupId))->setDec('member_count');

            S('group_is_join_' . $aGroupId . '_' . $uid, null);
            S('group_member_count_' . $group['id'], null);
            $this->success('退出成功', 'refresh');
        } else {
            $this->error('退出失败');
        }
    }

    public function groupInvite()
    {
        $aGroupId = I('group_id', 0, 'intval');
        $this->checkAuth();
        if (IS_POST) {

            $uids = I('post.uids');
            $group = D('Group')->getGroup($aGroupId);
            foreach ($uids as $uid) {
                D('Message')->sendMessage($uid, '邀请加入群组', get_nickname(is_login()) . "邀请您加入群组【{$group['title']}】  <a class='ajax-post' href='" . U('group/index/attend', array('group_id' => $aGroupId)) . "'>接受邀请</a>",  'group/index/group', array('id' => $aGroupId), is_login());
            }

            $result = array('status' => 1, 'info' => '邀请成功');
            $this->ajaxReturn($result);
        } else {
            $friendList = D('Follow')->getAllFriends(is_login());
            $friendIds = getSubByKey($friendList, 'follow_who');
            $friends = array();
            foreach ($friendIds as $v) {
                $friends[$v] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), $v);
            }
            $this->assign('friends', $friends);
            $this->assign('group_id', $aGroupId);
            $this->display();
        }
    }

    ////////////////////////////---------------------------------------华丽的分割线-----------------------------------/////////////////////////////


    public function search($page = 1, $uid = 0)
    {
        $_REQUEST['keywords'] = op_t($_REQUEST['keywords']);

        if ($uid != 0) {
            $where['uid'] = $uid;
            $this->assign('tip', $uid);
        } else {
            //读取帖子列表
            $map['title'] = array('like', "%{$_REQUEST['keywords']}%");
            $map['content'] = array('like', "%{$_REQUEST['keywords']}%");
            $map['_logic'] = 'OR';
            $where['_complex'] = $map;
            $where['status'] = 1;
        }
        $list = D('GroupPost')->where($where)->order('last_reply_time desc')->page($page, 10)->select();
        $totalCount = D('GroupPost')->where($where)->count();
        foreach ($list as &$post) {
            $post['colored_title'] = str_replace('"', '', str_replace($_REQUEST['keywords'], '<span style="color:red">' . $_REQUEST['keywords'] . '</span>', op_t(strip_tags($post['title']))));
            $post['colored_content'] = str_replace('"', '', str_replace($_REQUEST['keywords'], '<span style="color:red">' . $_REQUEST['keywords'] . '</span>', op_t(strip_tags($post['content']))));
            $post['group'] = $this->getGroup($post['group_id']);

        }
        unset($post);

        $_GET['keywords'] = $_REQUEST['keywords'];
        //显示页面
        $this->assign('list', $list);
        $this->assign('totalCount', $totalCount);
        $this->display();
    }

    public function deletePost()
    {
        $aPostId=I('id',0,'intval');
        $postModel=D('GroupPost');
        $map=array('id'=>$aPostId,'status'=>1);
        $post=$postModel->where($map)->find();
        if(!$post){
            $this->ajaxReturn(array('status'=>0,'info'=>'不存在该帖子！','url'=>U('Group/Index/groups')));
        }
        $this->checkAuth('Group/Index/deletePost',get_admin_ids($aPostId,3,0),'你没有删除贴子的权限！');
        $res=$postModel->where($map)->setField('status',-1);
        if($res){
            $this->ajaxReturn(array('status'=>1,'info'=>'删除成功！','url'=>U('Group/Index/group',array('id'=>$post['group_id']))));
        }else{
            $this->ajaxReturn(array('status'=>0,'info'=>'删除失败！'.$postModel->getError()));
        }
    }

}