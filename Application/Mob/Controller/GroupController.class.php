<?php


namespace Mob\Controller;

use Think\Controller;
use Common\Model\ContentHandlerModel;

class GroupController extends BaseController
{
    public function _initialize()
    {
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'home', 'href' => U('Mob/Group/index')),
                array('type' => 'message'),
            ),
            'center' => array('title' => '群组')
        );
        if (is_login()) {
            $this->_top_menu_list['right'][] = array('type' => 'edit', 'href' => U('Mob/Group/addGroup'));
        } else {
            $this->_top_menu_list['right'][] = array('type' => 'edit', 'info' => '登录后才能操作！');
        }
        //dump($this->_top_menu_list);exit;
        $this->setMobTitle('群组');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }

    /**
     * 群组首页
     */
    public function index($mark = 'myGroup', $typeId = 0)
    {

        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');

        switch ($mark) {
            case 'myGroup':
                $this->requireLogin();
                $this->setTopTitle('我的群组话题');
                $group_ids = D('GroupMember')->getGroupIds(array('where' => array('uid' => is_login(), 'status' => 1)));
                $myattend = D('Group')->getList(array('where' => array('id' => array('in', $group_ids), 'status' => 1), 'page' => $aPage, 'count' => $aCount, 'order' => 'uid = ' . is_login() . ' desc ,uid asc'));
                foreach ($myattend as &$v) {
                    $v = D('Group')->getGroup($v);
                }
                unset($v);
                $posts = D('GroupPost')->where(array('group_id' => array('in', $group_ids), 'status' => 1))->page($aPage, $aCount)->order('create_time desc')->select();
                $totalCount = D('GroupPost')->where(array('group_id' => array('in', $group_ids), 'status' => 1))->count();
                $supportModel = D('Common/Support');
                foreach ($posts as &$v) {
                    $v['group'] = D('Group')->getGroup($v['group_id']);
                    $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
                    $v['summary']=parse_expression($v['summary']);
                }
               // dump($posts);exit;
                $this->assign('posts', $posts);
                break;
            case 'discover':
                $groupModel = D('Group');
                $group_ids = $groupModel->where(array('status' => 1))->field('id')->select();
                $group_ids = getSubByKey($group_ids, 'id');
                $posts = D('GroupPost')->where(array('status' => 1, 'group_id' => array('in', $group_ids)))->page($aPage, $aCount)->order('create_time desc')->select();
                $totalCount = D('GroupPost')->where(array('status' => 1, 'group_id' => array('in', $group_ids)))->count();
                $supportModel = D('Common/Support');
                foreach ($posts as &$v) {
                    $v['group'] = $groupModel->getGroup($v['group_id']);
                    $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
                    $v['summary']=parse_expression($v['summary']);
                }
                unset($v);
                $this->assign('posts', $posts);

                break;
            case 'select':
                $posts = D('GroupPost')->where(array('is_top' => 1, 'status' => 1))->page($aPage, $aCount)->order('create_time desc')->select();
                $totalCount = D('GroupPost')->where(array('is_top' => 1, 'status' => 1))->count();
                $supportModel = D('Common/Support');
                foreach ($posts as &$v) {
                    $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
                    $v['summary']=parse_expression($v['summary']);
                }
                unset($v);
                $this->assign('posts', $posts);
                break;
            case 'allGroup':
                $group = D('Group')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->order('create_time desc')->select();
                $totalCount = D('Group')->where(array('status' => 1))->count();
                foreach ($group as &$v) {
                    $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
                    $v['logo'] = getThumbImageById($v['logo'], 200, 200);
                    if (is_login() == $v['uid'] || is_administrator(get_uid())) {
                        $v['is_login'] = 1;
                    } else {
                        $v['is_login'] = 0;
                    }
                }
                $this->assign('group', $group);
                break;
            case 'parentType':
                $this->assign('typeId', $typeId);
                $typeName['parent'] = D('GroupType')->where(array('id' => $typeId))->find();

                $typeName['child'] = D('GroupType')->where(array('pid' => $typeName['parent']['id']))->select();
                if (is_null($typeName['child'])) {
                    $this->setTopTitle($typeName['parent']['title']);
                    $Group = D('Group')->where(array('type_id' => $typeId, 'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                    $totalCount = D('Group')->where(array('type_id' => $typeId, 'status' => 1))->count();
                } else {
                    $typeId = array_column($typeName['child'], 'id');

                    $typeId = array_merge($typeId, array($typeName['parent']['id']));
                    $this->setTopTitle($typeName['parent']['title']);
                    $map['type_id'] = array('in', $typeId);
                    $Group = D('Group')->where($map, array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();

                    $totalCount = D('Group')->where($map, array('status' => 1))->count();
                }
                foreach ($Group as &$v) {
                    $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
                    $v['logo'] = getThumbImageById($v['logo'], 200, 200);
                    if (is_login() == $v['uid'] || is_administrator(get_uid())) {
                        $v['is_login'] = 1;
                    } else {
                        $v['is_login'] = 0;
                    }
                }
                $this->assign('group', $Group);
                break;
            case 'childType':
                $this->assign('typeId', $typeId);
                $typeName = D('GroupType')->where(array('id' => $typeId))->find();
                $this->setTopTitle($typeName['title']);
                $Group = D('Group')->where(array('type_id' => $typeId, 'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                $totalCount = D('Group')->where(array('type_id' => $typeId, 'status' => 1))->count();
                foreach ($Group as &$v) {
                    $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
                    $v['logo'] = getThumbImageById($v['logo'], 200, 200);
                    if (is_login() == $v['uid'] || is_administrator(get_uid())) {
                        $v['is_login'] = 1;
                    } else {
                        $v['is_login'] = 0;
                    }
                }
                $this->assign('group', $Group);
                break;
        }


        $parent = D('GroupType')->where(array('status' => 1, 'pid' => 0))->order('sort asc')->select();
        foreach ($parent as &$v) {
            $v['child'] = D('GroupType')->where(array('status' => 1, 'pid' => $v['id']))->order('sort asc')->select();
        }
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }

         //    dump($pid);exit;
        $this->assign('pid', $pid);
        $this->assign('mark', $mark);
        $this->assign('parent', $parent);
        $this->display();
    }


    /**
     *查看更多帖子或群组
     */
public function addMoreIndex(){
    $aPage = I('post.page', 1, 'op_t');
    $aCount = I('post.count',10, 'op_t');
    $aMark= I('post.mark', 0, 'op_t');
    $aTypeId= I('post.$typeId', '', 'op_t');

    switch ($aMark) {
        case 'myGroup':
            $this->requireLogin();
            $this->setTopTitle('我的群组话题');
            $group_ids = D('GroupMember')->getGroupIds(array('where' => array('uid' => is_login(), 'status' => 1)));
            $myattend = D('Group')->getList(array('where' => array('id' => array('in', $group_ids), 'status' => 1), 'page' => $aPage, 'count' => $aCount, 'order' => 'uid = ' . is_login() . ' desc ,uid asc'));
            foreach ($myattend as &$v) {
                $v = D('Group')->getGroup($v);
            }
            unset($v);
            $posts = D('GroupPost')->where(array('group_id' => array('in', $group_ids), 'status' => 1))->page($aPage, $aCount)->order('create_time desc')->select();
            $supportModel = D('Common/Support');
            foreach ($posts as &$v) {
                $v['group'] = D('Group')->getGroup($v['group_id']);
                $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
            }
            break;
        case 'allGroup':
            $group = D('Group')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->order('create_time desc')->select();
            foreach ($group as &$v) {
                $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
                $v['logo'] = getThumbImageById($v['logo'], 200, 200);
                if (is_login() == $v['uid'] || is_administrator(get_uid())) {
                    $v['is_login'] = 1;
                } else {
                    $v['is_login'] = 0;
                }
            }
            break;
        case 'discover':
            $groupModel = D('Group');
            $group_ids = $groupModel->where(array('status' => 1))->field('id')->select();
            $group_ids = getSubByKey($group_ids, 'id');
            $posts = D('GroupPost')->where(array('status' => 1, 'group_id' => array('in', $group_ids)))->page($aPage, $aCount)->order('create_time desc')->select();

            $supportModel = D('Common/Support');
            foreach ($posts as &$v) {
                $v['group'] = $groupModel->getGroup($v['group_id']);
                $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
            }
            unset($v);
            break;
        case 'select':
            $posts = D('GroupPost')->where(array('is_top' => 1, 'status' => 1))->order('create_time desc')->select();
            $supportModel = D('Common/Support');
            foreach ($posts as &$v) {

                $v['support_count'] = $supportModel->getSupportCount('Group', 'post', $v['id']);
            }
            unset($v);
            break;
        case 'parentType':
            $this->assign('typeId', $aTypeId);
            $typeName['parent'] = D('GroupType')->where(array('id' => $aTypeId))->find();

            $typeName['child'] = D('GroupType')->where(array('pid' => $typeName['parent']['id']))->select();
            if (is_null($typeName['child'])) {
                $this->setTopTitle($typeName['parent']['title']);
                $Group = D('Group')->where(array('type_id' => $aTypeId, 'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
            } else {
                $typeId = array_column($typeName['child'], 'id');

                $typeId = array_merge($typeId, array($typeName['parent']['id']));
                $this->setTopTitle($typeName['parent']['title']);
                $map['type_id'] = array('in', $typeId);
                $Group = D('Group')->where($map, array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
            }
            foreach ($Group as &$v) {
                $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
                $v['logo'] = getThumbImageById($v['logo'], 200, 200);
                if (is_login() == $v['uid'] || is_administrator(get_uid())) {
                    $v['is_login'] = 1;
                } else {
                    $v['is_login'] = 0;
                }
            }
            $this->assign('group', $Group);
            break;
        case 'childType':
            $this->assign('typeId', $aTypeId);
            $typeName = D('GroupType')->where(array('id' => $aTypeId))->find();
            $this->setTopTitle($typeName['title']);
            $Group = D('Group')->where(array('type_id' => $aTypeId, 'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
            foreach ($Group as &$v) {
                $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
                $v['logo'] = getThumbImageById($v['logo'], 200, 200);
                if (is_login() == $v['uid'] || is_administrator(get_uid())) {
                    $v['is_login'] = 1;
                } else {
                    $v['is_login'] = 0;
                }
            }
            $this->assign('group', $Group);
            break;
    }
    if(($aMark=='myGroup')||($aMark=='discover')||($aMark=='select')){
        foreach ($posts as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','uid','space_mob_url'), $v['uid']);
            $v['cover_url'] = getThumbImageById($v['cover'],119,89);
            $v['count']=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$v['id']))->order('create_time desc')->count();
        }
        if ($posts) {
            $data['html'] = "";
            foreach ($posts as $val) {
                $this->assign("vo", $val);
                $data['html'] .= $this->fetch("_postlist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }

    }else{
        foreach ($group as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','uid','space_mob_url'), $v['uid']);
            $v['cover_url'] = getThumbImageById($v['cover'],200,200);
            $v['count']=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$v['id']))->order('create_time desc')->count();
        }
        if ($group) {
            $data['html'] = "";
            foreach ($group as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_grouplist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
    }
    $this->ajaxReturn($data);

}

    /**
     * @param $id
     * 群组内的帖子
     */
    public function detail($id)
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count',10, 'op_t');
        $aLzlPage = I('post.lzlpage', 1, 'op_t');
        $aLzlCount = I('post.lzlcount', 3, 'op_t');
           //增加浏览次数
        D('GroupPost')->where(array('id' => $id))->setInc('view_count');
        //获取群组信息

        $group = D('GroupPost')->where(array('id' => $id))->find();
        $group = D('Group')->where(array('id' => $group['group_id']))->find();
        $group['logo'] = getThumbImageById($group['logo'], 100, 100);
        $group['member_count'] = D('GroupMember')->where(array('group_id' => $group['id']))->count();
        if ($group['type'] == 0) {
            $group['type'] = '公共群组';
        }
        if ($group['type'] == 1) {
            $group['type'] = '私有群组';
        }
        $group['GroupType'] = D('GroupType')->where(array('id' => $group['type_id']))->find();
        $group['notice'] = D('GroupNotice')->where(array('group_id' => $id))->find();

        //获取帖子信息
        $post = D('GroupPost')->where(array('id' => $id))->find();
        $post['collect']=D('GroupBookmark')->where(array('post_id'=>$post['id'],'uid'=>is_login()))->find();
        if($post['collect']){
            $post['collect']=1;
        }else{
            $post['collect']=0;
        }
        $post['countSupport']=D('Support')->where(array('appname'=>'Group','row'=>$id))->count();
        $post['isSupport']=D('Support')->where(array('appname'=>'Group','row'=>$id,'uid'=>is_login()))->find();
        if( $post['isSupport']){
            $post['isSupport']=1;
        }else{
            $post['isSupport']=0;
        }
        $post['user'] = query_user(array('nickname', 'avatar64', 'space_mob_url'), $post['uid']);
        //获取帖子评论信息
        $postComment = D('GroupPostReply')->where(array('post_id' => $id,'status'=>1))->page($aPage, $aCount)->select();
        $totalCount= D('GroupPostReply')->where(array('post_id' => $id,'status'=>1))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        foreach ($postComment as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','space_mob_url'), $v['uid']);
            $v['content'] = parse_expression($v['content']);
            if ($postComment['uid'] == $v['uid']) {
                $v['floormaster'] = "楼主";
            }

            //楼中楼内容
            $v['lzllist'] = $list = D('GroupLzlReply')->getLZLReplyList($v['id'],'create_time asc',$aLzlPage,$aLzlCount);
            $v['lzltotalCount'] = $totalCountLzl = D('GroupLzlReply')->where('status=1 and to_f_reply_id=' . $v['id'])->count();
            if ($totalCountLzl <= $aLzlPage * $aLzlCount) {
                $v['lzlcount'] = 0;
            } else {
                $v['lzlcount'] = 1;
            }

            $data['to_f_reply_id'] = $v['id'];
            $pageCount = ceil($totalCountLzl / $aLzlCount);
            $v['lzlhtml'] = $html = getPageHtml('changePage', $pageCount, $data, $aLzlPage);
            //楼中楼内容结束
        }
//dump($postComment[0]['lzllist']);exit;
   // dump($id);exit;
        $this->assign('pid', $pid);
        $this->assign('group_id', $id);
        $this->assign('postComment', $postComment);
        $this->assign('group', $group);
        $this->assign('post', $post);
        $this->display();
    }

    public function addMoreReply(){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count',10, 'op_t');
        $aLzlPage = I('post.lzlpage', 1, 'op_t');
        $aLzlCount = I('post.lzlcount', 3, 'op_t');
        $aId= I('post.group_id',10, 'op_t');
        //获取帖子评论信息
        $postComment = D('GroupPostReply')->where(array('post_id' => $aId,'status'=>1))->page($aPage, $aCount)->select();
        foreach ($postComment as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','space_mob_url'), $v['uid']);
            $v['content'] = parse_expression($v['content']);
            if ($postComment['uid'] == $v['uid']) {
                $v['floormaster'] = "楼主";
            }
            //楼中楼内容
            $v['lzllist'] = $list = D('GroupLzlReply')->getLZLReplyList($v['id'],'create_time asc',$aLzlPage,$aLzlCount);
            $v['lzltotalCount'] = $totalCountLzl = D('GroupLzlReply')->where('status=1 and to_f_reply_id=' . $v['id'])->count();
            if ($totalCountLzl <= $aLzlPage * $aLzlCount) {
                $v['lzlcount'] = 0;
            } else {
                $v['lzlcount'] = 1;
            }
            $data['to_f_reply_id'] = $v['id'];
            $pageCount = ceil($totalCountLzl / $aLzlCount);
            $v['lzlhtml'] = $html = getPageHtml('changePage', $pageCount, $data, $aLzlPage);
            //楼中楼内容结束
        }
        if ($postComment) {
            $data['html'] = "";
            foreach ($postComment as $key=>$val ) {
                $this->assign("vl", $val);
                $this->assign("k", ($aPage-1)*$aCount+$key+1);

                $data['html'] .= $this->fetch("_postcomment");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }


    public function addMoreLzlreply(){
        $aLzlPage = I('post.lzlpage', 1, 'op_t');
        $aLzlCount = I('post.lzlcount', 3, 'op_t');
        $aId=I('post.id', '', 'op_t');
        $map['id'] = array('eq', $aId);

        $forum_detail = D('GroupPost')->where($map)->find();

        $forum_detail['user']=query_user(array('nickname', 'avatar128','space_mob_url'), $forum_detail['uid']);
        //楼中楼内容
        //   dump($v['id']);exit;
        $post_detail['lzllist']= $list = D('Mob/GroupLzlReply')->getLZLReplyList($aId,'create_time asc',$aLzlPage,$aLzlCount);
       // dump( $post_detail);exit;

        if ($post_detail) {
            $data['html'] = "";
            $this->assign("vl", $post_detail);
            $data['html'] .= $this->fetch("_lzlreply");
            if($data['html']){
                $data['status'] = 1;
            }else{
                $data['stutus'] = 0;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }

    /**
     * @param $id
     * 群组内的动态内容与信息
     */

    public function group($id)
    {
        //获取群组信息
        $group = D('Group')->where(array('id' => $id))->find();
        $group['logo'] = getThumbImageById($group['logo'], 100, 100);
        $group['member_count'] = D('GroupMember')->where(array('group_id' => $group['id']))->count();
        if ($group['type'] == 0) {
            $group['type'] = '公共群组';
        }
        if ($group['type'] == 1) {
            $group['type'] = '私有群组';
        }
        $group['GroupType'] = D('GroupType')->where(array('id' => $group['type_id']))->find();
        $group['notice'] = D('GroupNotice')->where(array('group_id' => $id))->find();
        $map['position'] = array('in',array(2,3));//去除已关注的人

        $group ['canAdmin']=D('GroupMember')->where(array('group_id' => $id,$map))->field('uid')->select();
        $group ['canAdmin']=array_column($group ['canAdmin'],'uid');


        //群组动态
        $list = D('GroupDynamic')->where(array('group_id' => $group['id']))->order('create_time desc')->select();
        $list = array_column($list, 'id');

        //获取帖子信息

        $post = D('GroupPost')->where(array('group_id' => $id))->order('create_time desc')->select();
        foreach ($post as & $v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'space_mob_url'), $v['uid']);
        }
        //成员
        $people['admin'] = D('GroupMember')->where(array('group_id' => $id, 'position' => 3))->order('create_time desc')->find();
        $people['admin']['user'] = query_user(array('nickname', 'avatar64', 'space_mob_url'), $people['admin']['uid']);
        $people['people'] = D('GroupMember')->where(array('group_id' => $id, 'position' => 1))->order('create_time desc')->select();
        // dump($people);exit;
        foreach ($people['people'] as & $v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'space_mob_url'), $v['uid']);
        }
       //  dump($group);exit;
        $this->assign('group_id', $id);
        $this->assign('dynamicList', $list);            //群组的动态
        $this->assign('group', $group);             //群组的信息
        //  dump($people);exit;
        $this->assign('post', $post);               //帖子的信息
        $this->assign('people', $people);            //成员
        $this->display();
    }

    /**
     * @param $id
     * 群组ID
     *渲染  编辑  创建群组
     */

    public function addGroup()
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
                D('Message')->sendMessage(1, '群组创建审核', get_nickname(is_login()) . "创建了群组【{$aTitle}】，快去审核吧。", 'admin/group/unverify');
                $data['status'] = 1;
                $data['info'] = $message;
                $this->ajaxReturn($data);
            }

            // 发送微博
            if (D('Module')->checkInstalled('Weibo')) {

                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('group', array('id' => $group_id));
                if ($isEdit && check_is_in_config('edit_group', modC('GROUP_SEND_WEIBO', 'add_group,edit_group', 'GROUP'))) {
                    D('Weibo')->addWeibo(is_login(), "我修改了群组【" . $aTitle . "】：" . $postUrl);
                }
                if (!$isEdit && check_is_in_config('add_group', modC('GROUP_SEND_WEIBO', 'add_group,edit_group', 'GROUP'))) {
                    D('Weibo')->addWeibo(is_login(), "我创建了一个新的群组【" . $aTitle . "】：" . $postUrl);
                }

            }

            //显示成功消息
            $message = $isEdit ? '编辑成功。' : '发表成功。';
            $url = $isEdit ? 'refresh' : U('group/index/group', array('id' => $group_id));
            $this->success($message, $url);
        }
        $groupType = $this->assignGroupTypes();//分类信息内容
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
        $this->setMobTitle('创建群组');
        $this->setTopTitle('创建群组');
        $this->assign('groupTypeAll', $groupType['parent']);
        $this->display();
    }

    private function requireLimit()
    {
        $model = D('Group');
        $limit = modC('GROUP_LIMIT', 5, 'GROUP');
        $count = $model->getUserGroupCount(is_login());
        if ($count >= $limit) {
            $this->error('您创建的群组数已达上限：' . $limit);
        }
    }

    /**
     * @param string $tab
     * @param int $id
     * 编辑群组内容渲染
     */
    public function admin($tab = 'edit', $id = 0)
    {
        switch ($tab) {
            case 'edit':
                $editGroup = D('Group')->where(array('status' => 1, 'id' => $id))->find();
                $editGroup['logo_id'] = getThumbImageById($editGroup['logo']);
                $editGroup['background_id'] = getThumbImageById($editGroup['background']);

                $groupType = $this->assignGroupTypes();//分类信息内容
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
                $this->setMobTitle('编辑群组');
                $this->settopTitle('基本信息');
                $this->assign('groupTypeAll', $groupType['parent']);
                $this->assign('editGroup', $editGroup);
                break;
            case 'posttype':
                $postType = D('GroupPostCategory')->where(array('group_id' => $id, 'status' => 1))->select();
                $this->setMobTitle('帖子分类');
                $this->settopTitle('帖子分类管理');
                $this->assign('postType', $postType);
                $this->assign('group_id', $id);
                break;
            case 'member':
                $member = D('GroupMember')->where(array('group_id' => $id, 'status' => 1))->select();
                foreach ($member as &$v) {
                    $v['user'] = query_user(array('nickname', 'avatar64', 'space_mob_url'), $v['uid']);
                }
                $notAuditMember=D('GroupMember')->where(array('group_id' => $id, 'status' => 0))->select();
                foreach ($notAuditMember as &$a) {
                    $a['user'] = query_user(array('nickname', 'avatar64', 'space_mob_url'), $a['uid']);
                }

                $this->setMobTitle('成员');
                $this->settopTitle('成员管理');
                $this->assign('group_id', $id);
                $this->assign('member', $member);
                $this->assign('memberCount', count($member));
                $this->assign('notAuditMember', $notAuditMember);
                $this->assign('notAuditMemberCount', count($notAuditMember));
                break;
            case 'notic':
                $this->setMobTitle('公告');
                $this->settopTitle('公告管理');
                $notic = $this->assignNotice($id);
                $this->assign('group_id', $id);
                $this->assign('notic', $notic);
                break;


        }
        $this->assign('id', $id);
        $this->display();
    }

    protected function assignGroupTypes()
    {
        $groupType = D('Group/GroupType')->getGroupTypes();
        $this->assign($groupType);
        return $groupType;
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

    /**
     * 加入群组
     */
    public function attend()
    {
        $aGroupId = I('post.group_id', 0, 'op_t');
        $this->requireGroupExists($aGroupId);
        $this->requireLogin();
        // $this->checkAuth();

        //判断是否已经加入
        if (is_joined($aGroupId) == 1) {
            $this->error('已经加入了该群组');
        }
        // 已经加入但还未审核
        if (is_joined($aGroupId) == 2) {
            $this->error('请耐心等待管理员审核');
        }
        $uid = is_login();
        $group = D('Mob/Group')->getGroup($aGroupId);

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
            D('Message')->sendMessage($group['uid'], '加入群组审核', get_nickname($uid) . "请求加入群组【{$group['title']}】", 'group/Manage/member', array('group_id' => $aGroupId, 'status' => 0), $uid);
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
            D('Group')->where(array('id' => $aGroupId))->setInc('member_count');
            S('group_is_join_' . $aGroupId . '_' . $uid, null);
            S('group_member_count_' . $group['id'], null);
            $this->success('加入成功' . $info, 'refresh');
        } else {
            $this->error('加入失败');
        }

    }

    /**
     *退出群组
     */
    public function quit()
    {
        $aGroupId = I('group_id', 0, 'intval');
        $this->requireLogin();
        $this->requireGroupExists($aGroupId);
        // $this->checkAuth();
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

            D('Group')->where(array('id' => $aGroupId))->setDec('member_count');

            S('group_is_join_' . $aGroupId . '_' . $uid, null);
            S('group_member_count_' . $group['id'], null);
            $this->success('退出成功', 'refresh');
        } else {
            $this->error('退出失败');
        }
    }

    /**
     * 邀请好友内容渲染，与功能实现。
     */
    public function invitationFriend()
    {
        $aGroupId = I('group_id', 0, 'intval');
        if (IS_POST) {
            $uids = I('post.uids');
            if (empty($uids)) {
                $result = array('status' => 0, 'info' => '请选择好友');
                $this->ajaxReturn($result);
            }
            $group = D('Group')->getGroup($aGroupId);
            foreach ($uids as $uid) {
                D('Message')->sendMessage($uid, '邀请加入群组', get_nickname(is_login()) . "邀请您加入群组【{$group['title']}】  <a class='ajax-post' href='" . U('group/index/attend', array('group_id' => $aGroupId)) . "'>接受邀请</a>", 'group/index/group', array('id' => $aGroupId), is_login());
            }
            $result = array('status' => 1, 'info' => '邀请成功');
            $this->ajaxReturn($result);
        } else {
            $friendList = D('Follow')->getAllFriends(is_login());
            $friendIds = getSubByKey($friendList, 'follow_who');
            $friends = array();
            foreach ($friendIds as $v) {
                $friends[$v] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_mob_url'), $v);
            }
            $this->assign('group_id', $aGroupId);
            $this->assign('friends', $friends);
            $this->display();
        }


    }

    /**
     * 帖子分类管理
     */
    public function editCate()
    {
        $aCateId = I('post.cate_id', 0, 'intval');          //根据是否有分类的ID判断是否是编辑或新增分类
        $aTitle = I('post.title', '', 'text');
        $aGroupId = I('post.group_id', 0, 'intval');
        if (empty($aTitle)) {
            $this->error('分类名不能为空');
        }
        if ($aCateId == 0) {
            $res = D('GroupPostCategory')->add(array('group_id' => $aGroupId, 'title' => $aTitle, 'create_time' => time(), 'status' => 1, 'sort' => 0));
            if (!$res) {
                $this->error('添加分类失败');
            }
            $this->success('添加分类成功', 'refresh');
        } else {
            $res = D('GroupPostCategory')->where(array('id' => $aCateId))->save(array('title' => $aTitle));
            if (!$res) {
                $this->error('编辑分类失败');
            }
            $this->success('编辑分类成功', 'refresh');
        }
    }

    /**
     * 删除分类；
     */
    public function delCate()
    {
        $aCateId = I('post.cate_id', 0, 'intval');
        $res = D('GroupPostCategory')->where(array('id' => $aCateId))->setField('status', 0);
        if (!$res) {
            $this->error('删除分类失败');
        }
        $this->success('删除分类成功', 'refresh');
    }

    /**
     * 公告
     */
    private function assignNotice($group_id)
    {
        $notice = D('GroupNotice')->getNotice($group_id);
        $this->assign('notice', $notice);
        return $notice;
    }

    /**
     * 公告编辑
     */
    public function notice()
    {
        $aNotice = I('post.content', '', 'text');
        $aGroupId = I('post.group_id', '', 'intval');
        $data['group_id'] = $aGroupId;
        $data['content'] = $aNotice;
        $res = D('GroupNotice')->addNotice($data);
        if ($res) {
            $this->success('添加成功', 'refresh');
        } else {
            $this->error('添加失败');
        }

    }
    /**
     * 成员管理
     */
    public function removeGroupMember()
    {
        $aUid = I('post.uid', 0, 'intval');
        $aGroupId = I('post.group_id', '', 'intval');
        $res = D('GroupMember')->where(array('uid' => $aUid, 'group_id' => $aGroupId))->delete();
        $dynamic['group_id'] =$aGroupId;
        $dynamic['uid'] = $aUid;
        $dynamic['type'] = 'remove';
        $dynamic['create_time'] = time();
        $group = D('Group')->getGroup($aGroupId);
        if ($group['uid'] == $aUid) {
            $this->error('创建者无法移出群组');
        }
        D('GroupDynamic')->add($dynamic);
        if ($res) {
            $group = D('Group')->getGroup($this->groupId);
            D('Message')->sendMessage($aUid, '移出群组', get_nickname(is_login()) . "将您移出了群组【{$group['title']}】", 'group/index/group', array('id' => $this->groupId), is_login());
            S('group_member_count_' . $group['id'],null);
            S('group_is_join_' . $group['id'] . '_' . $aUid, null);
            $this->success('删除成功', 'refresh');
        } else {
            $this->error('删除失败');
        }
    }
    /**
     * 审核通过
     */
    public function receiveMember()
    {
        $aUid = I('post.uid', 0, 'intval');
        $aGroupId = I('post.group_id', '', 'intval');
        $res = D('GroupMember')->setStatus($aUid, $aGroupId, 1);
        $dynamic['group_id'] = $aGroupId;
        $dynamic['uid'] = $aUid;
        $dynamic['type'] = 'attend';
        $dynamic['create_time'] = time();
        D('GroupDynamic')->add($dynamic);
        if ($res) {
            $group = D('Group')->getGroup($aGroupId);
            D('Message')->sendMessage($aUid,'群组审核通过', get_nickname(is_login()) . "通过了您加入群组【{$group['title']}】的请求",  'group/index/group', array('id' => $aGroupId), is_login());
            S('group_member_count_' . $group['id'],null);
            S('group_is_join_' . $group['id'] . '_' . $aUid, null);
            $this->success('审核成功', 'refresh');
        } else {
            $this->error('审核失败');
        }
    }

    /**
     * 解散群组
     */
    public function dismiss()
    {
        $aGroup = I('post.group_id', 0, 'op_t');
        $this->checkAuth('Group/Manager/dismiss',get_group_creator($aGroup),'您没有解散群组的权限。');
        $res = D('Mob/Group')->delGroup($aGroup);
        if ($res) {
            $this->success('解散成功', U('group/index/index'));
        } else {
            $this->error('解散失败');
        }
    }


    //以下为群组帖子内容与功能


    public function addComment($id=0,$is_edit=0,$reply_id=0){
        if($is_edit==1){
            $reply_id = intval($reply_id);
            $this->checkActionLimit('edit_group_reply', 'GroupPostReply', $reply_id, is_login(), true);
            if ($reply_id) {
                $reply = D('GroupPostReply')->where(array('id' => $reply_id, 'status' => 1))->find();
                $reply['is_edit']=1;
            } else {
                $this->error('参数出错！');
            }
            $this->setMobTitle('编辑评论 —— 论坛');
            //显示页面
            $this->assign("reply_id", $reply['id']);
            $this->assign("is_edit", 1);
            $this->assign("editreply", $reply);
            $this->display();
        }else{
            $this->assign("post_id", $id);
            $this->display();
        }
    }
    /**
     * 帖子回复
     */
    public function doReply()
    {
        $aPostId = I('post.post_id', 0, 'op_t');
        $attach_ids=I('post.attach_ids','','text');
        if($attach_ids){
            $aContent = I('post.content', '', 'filter_content');
            $img_ids = explode(',',  $attach_ids);              //把图片和内容结合
            //    dump($img_ids);
            foreach($img_ids as &$v){
                $v = M('Picture')->where(array('status' => 1))->getById($v);
                if(!is_bool(strpos( $v['path'],'http://'))){
                    $v = $v['path'];
                }else{
                    $v =getRootUrl(). substr( $v['path'],1);
                }
                $v='<p><img src="'. $v.'" style=""/></p><br>';
            };
            $img_ids = implode('', $img_ids);
            //  dump($img_ids);
            $aContent=$img_ids.$aContent;
            $contentHandler=new ContentHandlerModel();
            $aContent=$contentHandler->filterHtmlContent($aContent);    //把图片和内容结合END
        }else{
            $aContent = I('post.content', '', 'filter_content');
        }

        $isEdit = I('post.is_edit', 0, 'op_t');
        if($isEdit==1){
            $aReplyId= I('post.reply_id', 0, 'op_t');
            $this->checkActionLimit('edit_group_reply', 'GroupPostReply', $aReplyId, is_login(), true);
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
        }else{
            // 获取群组ID
            $group_id = $this->getGroupIdByPost($aPostId);
            $this->requireLogin();
            $this->checkActionLimit('add_group_reply', 'GroupPostReply', 0, is_login(), true);


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


    }
    private  function getGroupIdByPost($post_id)
    {
        $post = D('GroupPost')->getPost($post_id);
        return $post['group_id'];

    }
//以下为楼中楼内容

    /**
     * @param int $uid
     * @param null $username
     * @param int $id
     * @param int $post_id
     * @param int $to_f_reply_id
     * 楼中楼回复时数据渲染、
     */
    public function atComment($uid=0,$username=null,$id=0,$post_id=0,$to_f_reply_id=0){

        $this->assign("uid", $uid);             //to_uid
        $this->assign("atusername", $username);
        $this->assign("id", $id);               //楼层id
        $this->assign("post_id", $post_id);        //帖子ID
        $this->assign("to_f_reply_id", $to_f_reply_id);
        $this->display();
    }
    /**
     * 群组帖子楼中楼
     */
    public function doSendLzlReply()
    {
        $aToFReplyId = I('post.to_f_reply_id', 0, 'intval');
        $aToReplyId = I('post.to_reply_id', 0, 'intval');

        $aContent = I('post.content', '', 'text');
        $model = D('Mob/GroupLzlReply');

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

    /**
     * 删除帖子回复
     */
    public function delPostReply()
    {
        $ReplyId = I('post.replyId', 0, 'intval');
        $this->requireLogin();
        $this->checkAuth(null, get_reply_admin($ReplyId), '您无删除权限');

        $res = D('GroupPostReply')->delPostReply($ReplyId);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }
    /**
     * 删除楼中楼回复
     */
    public function delLzlReply()
    {

        $aId = I('post.lzlreplyId', 0, 'intval');
        $this->requireLogin();

        $this->checkAuth(null, get_lzl_admin($aId));

        $res = D('GroupLzlReply')->delLzlReply($aId);

        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * @param int $group_id
     * 帖子分类内容渲染
     */

    public function addPost($group_id = 0,$isEdit=0,$post_id=0)
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'back','need_confirm'=>1,'confirm_info'=>'确定要返回？','a_class'=>'','span_class'=>''),
            ),
        );
        // dump($this->_top_menu_list);exit;
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('发表新帖');
        if($isEdit!=0){
        $post=D('GroupPost')->where(array('id'=>$post_id))->find();
            $this->setTopTitle('编辑帖子');
            $this->assign('post',$post);
        }
       // dump($group_id);exit;
        $map['status'] = 1;
        $group_id && $map['group_id'] = $group_id;
        $cate = D('GroupPostCategory')->getList(array('where' => $map, 'order' => 'sort asc'));
        foreach ($cate as &$v) {
            $v = D('GroupPostCategory')->getPostCategory($v);
        }
        $this->assign('group_id',$group_id);
        $this->assign('post_cate', $cate);
       $this->display();
    }

    /**
     * 帖子发布跟编辑。
     */
    public function doEdit()
    {
        $aPostId = I('post.post_id', 0, 'intval');
        $aGroupId = I('post.group_id', 0, 'intval');
        $aTitle = I('post.title', '', 'text');
        $aCategory = I('post.category', 0, 'intval');

        $attach_ids=I('post.attach_ids','','text');
        if($attach_ids){
            $aContent = I('post.content', '', 'filter_content');
            $img_ids = explode(',',  $attach_ids);              //把图片和内容结合
            //    dump($img_ids);
            foreach($img_ids as &$v){
                $v = M('Picture')->where(array('status' => 1))->getById($v);
                if(!is_bool(strpos( $v['path'],'http://'))){
                    $v = $v['path'];
                }else{
                    $v =getRootUrl(). substr( $v['path'],1);
                }
                $v='<p><img src="'. $v.'" style=""/></p><br>';
            };
            $img_ids = implode('', $img_ids);
            $aContent=$img_ids.$aContent;
            $contentHandler=new ContentHandlerModel();
            $aContent=$contentHandler->filterHtmlContent($aContent);    //把图片和内容结合END
        }else{
            $aContent = I('post.content', '', 'filter_content');
        }


        if (is_joined($aGroupId) != 1) {
            $this->error('您无发布帖子权限');
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
                    D('Weibo')->addWeibo(is_login(), "我在群组【{$group['title']}】里更新了帖子【" . $post['title'] . "】：" . $postUrl, $type, $feed_data);
                }
                if (!$isEdit && check_is_in_config('add_group_post', modC('GROUP_POST_SEND_WEIBO', 'add_group_post,edit_group_post', 'GROUP'))) {
                    D('Weibo')->addWeibo(is_login(), "我在群组【{$group['title']}】里发表了一个新的帖子【" . $post['title'] . "】：" . $postUrl, $type, $feed_data);
                }
            }
        }
    }

    protected function requirePostExists($post_id)
    {
        if (!post_is_exist($post_id)) {
            $this->error('帖子不存在');
        }
    }

    /**
     * 删除帖子
     */
    public function deletePost()
    {
        $aPostId=I('post.post_id',0,'intval');
       // dump($aPostId);exit;
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

    /**
     * 收藏帖子
     */
    public function doBookmark()
    {

        $aPostId = I('post.post_id', 0, 'intval');
        $aFlag = I('post.flag', 0, 'intval');            //未收藏时传1过来，已收藏时不传数据。
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

    public function support($id=0, $uid=0){
        //$id是发帖人的微博ID
        //$uid是发帖人的ID
        if (!is_login()) {
            $this->error('请登陆后再进行操作');
        }
        $row = $id;
        $message_uid = $uid;
        $support['appname'] = 'Group';
        $support['table'] = 'post';
        $support['row'] = $row;
        $support['uid'] = is_login();

        if (D('Support')->where($support)->count()) {
            $return['status'] = '0';
            $return['info'] = '亲，您已经赞过我了！';
        } else {
            $support['create_time'] = time();
            if (D('Support')->where($support)->add($support)) {

                $this->clearCache($support);

                $user = query_user(array('username', 'uid','space_mob_url'));

                D('Message')->sendMessage($message_uid, $user['username'] . '给您点了个赞。', $title = $user['username'] . '赞了您。', is_login());

                $return['status'] = '1';
            } else {
                $return['status'] = ' 0';
                $return['info'] = '亲，您已经赞过我了！';
            }


        }
        $this->ajaxReturn($return);
    }
    private function clearCache($support)
    {
        unset($support['uid']);
        unset($support['create_time']);
        $cache_key = "support_count_" . implode('_', $support);
        S($cache_key, null);
    }
}