<?php

namespace Admin\Controller;

use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminConfigBuilder;

class WeiboController extends AdminController
{

    public function config()
    {
        $builder = new AdminConfigBuilder();
        $data = $builder->callback('configCallback')->handleConfig();

        $data['SHOW_TITLE'] = $data['SHOW_TITLE'] == null ? 1 : $data['SHOW_TITLE'];
        $data['HIGH_LIGHT_AT'] = $data['HIGH_LIGHT_AT'] == null ? 1 : $data['HIGH_LIGHT_AT'];
        $data['HIGH_LIGHT_TOPIC'] = $data['HIGH_LIGHT_TOPIC'] == null ? 1 : $data['HIGH_LIGHT_TOPIC'];
        $data['CAN_IMAGE'] = $data['CAN_IMAGE'] == null ? 1 : $data['CAN_IMAGE'];
        $data['CAN_TOPIC'] = $data['CAN_TOPIC'] == null ? 1 : $data['CAN_TOPIC'];
        $data['WEIBO_INFO'] = $data['WEIBO_INFO'] ? $data['WEIBO_INFO'] : '有什么新鲜事想告诉大家?';
        $data['WEIBO_NUM'] = $data['WEIBO_NUM'] ? $data['WEIBO_NUM'] : 140;
        $data['SHOW_COMMENT'] = $data['SHOW_COMMENT'] == null ? 1 : $data['SHOW_COMMENT'];
        $data['ACTIVE_USER'] = $data['ACTIVE_USER'] == null ? 1 : $data['ACTIVE_USER'];
        $data['ACTIVE_USER_COUNT'] = $data['ACTIVE_USER_COUNT'] ? $data['ACTIVE_USER_COUNT'] : 6;
        $data['NEWEST_USER'] = $data['NEWEST_USER'] == null ? 1 : $data['NEWEST_USER'];
        $data['NEWEST_USER_COUNT'] = $data['NEWEST_USER_COUNT'] ? $data['NEWEST_USER_COUNT'] : 6;

        $tab = array(
            array('data-id' => 'all', 'title' => '全站动态'),
            array('data-id' => 'concerned', 'title' => '我的关注'),
            array('data-id' => 'hot', 'title' => '热门微博'),
        );
        $default = array(array('data-id' => 'enable', 'title' => '启用', 'items' => $tab), array('data-id' => 'disable', 'title' => '禁用', 'items' => array()));

        $data['WEIBO_DEFAULT_TAB'] = $builder->parseKanbanArray($data['WEIBO_DEFAULT_TAB'], $tab, $default);

        $scoreTypes = D('Ucenter/Score')->getTypeList(array('status' => 1));
        foreach ($scoreTypes as $val) {
            $types[$val['id']] = $val['title'];
        }


        $data['WEIBO_SHOW_TITLE1'] = $data['WEIBO_SHOW_TITLE1'] ? $data['WEIBO_SHOW_TITLE1'] : '最新微博';
        $data['WEIBO_SHOW_COUNT1'] = $data['WEIBO_SHOW_COUNT1'] ? $data['WEIBO_SHOW_COUNT1'] : 5;
        $data['WEIBO_SHOW_ORDER_FIELD1'] = $data['WEIBO_SHOW_ORDER_FIELD1'] ? $data['WEIBO_SHOW_ORDER_FIELD1'] : 'create_time';
        $data['WEIBO_SHOW_ORDER_TYPE1'] = $data['WEIBO_SHOW_ORDER_TYPE1'] ? $data['WEIBO_SHOW_ORDER_TYPE1'] : 'desc';
        $data['WEIBO_SHOW_CACHE_TIME1'] = $data['WEIBO_SHOW_CACHE_TIME1'] ? $data['WEIBO_SHOW_CACHE_TIME1'] : '600';


        $data['WEIBO_SHOW_TITLE2'] = $data['WEIBO_SHOW_TITLE2'] ? $data['WEIBO_SHOW_TITLE2'] : '热门微博';
        $data['WEIBO_SHOW_COUNT2'] = $data['WEIBO_SHOW_COUNT2'] ? $data['WEIBO_SHOW_COUNT2'] : 5;
        $data['WEIBO_SHOW_ORDER_FIELD2'] = $data['WEIBO_SHOW_ORDER_FIELD2'] ? $data['WEIBO_SHOW_ORDER_FIELD2'] : 'comment_count';
        $data['WEIBO_SHOW_ORDER_TYPE2'] = $data['WEIBO_SHOW_ORDER_TYPE2'] ? $data['WEIBO_SHOW_ORDER_TYPE2'] : 'desc';
        $data['WEIBO_SHOW_CACHE_TIME2'] = $data['WEIBO_SHOW_CACHE_TIME2'] ? $data['WEIBO_SHOW_CACHE_TIME2'] : '600';
        $order = array('create_time' => '发布时间', 'comment_count' => '评论数');

        $builder->keyText('WEIBO_SHOW_TITLE1', '标题名称', '在首页展示块的标题');
        $builder->keyText('WEIBO_SHOW_COUNT1', '显示微博数', '');
        $builder->keyRadio('WEIBO_SHOW_ORDER_FIELD1', '排序值', '展示模块的数据排序方式', $order);
        $builder->keyRadio('WEIBO_SHOW_ORDER_TYPE1', '排序方式', '展示模块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'));
        $builder->keyText('WEIBO_SHOW_CACHE_TIME1', '缓存时间', '默认600秒，以秒为单位');

        $builder->keyText('WEIBO_SHOW_TITLE2', '标题名称', '在首页展示块的标题');
        $builder->keyText('WEIBO_SHOW_COUNT2', '显示微博数', '');
        $builder->keyRadio('WEIBO_SHOW_ORDER_FIELD2', '排序值', '展示模块的数据排序方式', $order);
        $builder->keyRadio('WEIBO_SHOW_ORDER_TYPE2', '排序方式', '展示模块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'));
        $builder->keyText('WEIBO_SHOW_CACHE_TIME2', '缓存时间', '默认600秒，以秒为单位');


        $builder->title('微博基本设置')
            ->data($data)
            ->keySwitch('SHOW_TITLE', '是否在微博左侧显示等级')
            ->keySwitch('HIGH_LIGHT_AT', '高亮AT某人')
            ->keySwitch('HIGH_LIGHT_TOPIC', '高亮微博话题')
            ->keyText('WEIBO_INFO', '微博发布框左上内容')
            ->keyText('WEIBO_NUM', '微博字数限制')
            ->keyText('HOT_LEFT', '热门微博取多少天以内的，以那天零点之后为准')->keyDefault('HOT_LEFT',3)
            ->keySwitch('CAN_IMAGE', '是否开启插入图片类型')
            ->keySwitch('CAN_TOPIC', '是否开启插入话题类型')
            ->keyRadio('COMMENT_ORDER', '微博评论列表顺序', '', array(0 => '时间倒序', 1 => '时间正序'))
            ->keyRadio('SHOW_COMMENT', '微博评论列表默认显示或隐藏', '', array(0 => '隐藏', 1 => '显示'))
            //->keySelect('WEIBO_DEFAULT_TAB', '微博默认显示标签', '', array('all'=>'全站微博','concerned'=>'我的关注','hot'=>'热门微博'))
            ->keyKanban('WEIBO_DEFAULT_TAB', '微博默认显示标签')
            ->keySwitch('ACTIVE_USER', '活跃用户开关')
            ->keySelect('ACTIVE_USER_ORDER', '活跃用户排序', '', $types)
            ->keyText('ACTIVE_USER_COUNT', '活跃用户显示数量', '')
            ->keyText('USE_TOPIC', '常用话题', '显示在微博发布按钮左边，用‘,’分隔')
            ->keySwitch('NEWEST_USER', '最新用户开关')
            ->keyText('NEWEST_USER_COUNT', '最新用户显示数量', '')
            ->group('基本设置', 'SHOW_TITLE,WEIBO_NUM,WEIBO_DEFAULT_TAB,HIGH_LIGHT_AT,HIGH_LIGHT_TOPIC,WEIBO_INFO,HOT_LEFT')
            ->group('微博类型设置', 'CAN_IMAGE,CAN_TOPIC')
            ->group('微博评论设置', 'COMMENT_ORDER,SHOW_COMMENT')
            ->group('微博右侧设置', 'ACTIVE_USER,ACTIVE_USER_ORDER,ACTIVE_USER_COUNT,NEWEST_USER,NEWEST_USER_COUNT')
            ->group('话题设置', 'USE_TOPIC')
            ->group('首页展示左侧栏', 'WEIBO_SHOW_TITLE1,WEIBO_SHOW_COUNT1,WEIBO_SHOW_ORDER_FIELD1,WEIBO_SHOW_ORDER_TYPE1,WEIBO_SHOW_CACHE_TIME1')
            ->group('首页展示右侧栏', 'WEIBO_SHOW_TITLE2,WEIBO_SHOW_COUNT2,WEIBO_SHOW_ORDER_FIELD2,WEIBO_SHOW_ORDER_TYPE2,WEIBO_SHOW_CACHE_TIME2')
            ->buttonSubmit('', '保存');


        $builder->display();
    }

    public function configCallback()
    {
        S('weibo_latest_user_top', null);
        S('weibo_latest_user_new', null);
    }


    public function weibo()
    {
        $aPage = I('page', 1, 'intval');
        $aContent = I('content', '', 'op_t');
        $r = 20;
        $map = array('status' => array('EGT', 0));
        $model = M('Weibo');
        $aContent && $map['content'] = array('like', '%' . $aContent . '%');

        $list = $model->where($map)->order('create_time desc')->page($aPage, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $attr1 = $attr;
        $attr1['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 1));
        $attr0 = $attr;
        $attr0['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 0));

        $builder->title('微博管理')
            ->setStatusUrl(U('setWeiboStatus'))->buttonEnable()->buttonDisable()->buttonDelete()->button('置顶', $attr1)->button('取消置顶', $attr0)
            ->keyId()->keyLink('content', '内容', 'comment?weibo_id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->keyDoActionEdit('editWeibo?id=###')->keyMap('is_top', '置顶', array(0 => '不置顶', 1 => '置顶'))
            ->search('内容', 'content')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }


    public function setWeiboTop($ids, $top)
    {
        foreach ($ids as $id) {
            D('Weibo')->where(array('id' => $id))->setField('is_top', $top);
            S('weibo_' . $id, null);
        }

        $this->success('设置成功', $_SERVER['HTTP_REFERER']);
    }

    public function weiboTrash()
    {
        $aPage = I('page', 1, 'intval');
        $r = 20;
        $builder = new AdminListBuilder();
        $builder->clearTrash('Weibo');
        //读取微博列表
        $map = array('status' => -1);
        $model = M('Weibo');
        $list = $model->where($map)->order('id desc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder->title('微博回收站')
            ->setStatusUrl(U('setWeiboStatus'))->buttonRestore()->buttonClear('Weibo')
            ->keyId()->keyLink('content', '内容', 'comment?weibo_id=###')->keyUid()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setWeiboStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Weibo', $ids, $status);
    }

    public function editWeibo()
    {
        $aId = I('id', 0, 'intval');
        $aContent = I('post.content', '', 'op_t');
        $aCreateTime = I('post.create_time', time(), 'intval');
        $aStatus = I('post.status', 1, 'intval');

        $model = M('Weibo');
        if (IS_POST) {
            //写入数据库
            $data = array('content' => $aContent, 'create_time' => $aCreateTime, 'status' => $aStatus);

            $result = $model->where(array('id' => $aId))->save($data);
            S('weibo_' . $aId, null);
            if (!$result) {
                $this->error('编辑失败');
            }

            //返回成功信息
            $this->success('编辑成功', U('weibo'));
        } else {
            //读取微博内容
            $weibo = $model->where(array('id' => $aId))->find();

            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title('编辑微博')
                ->keyId()->keyTextArea('content', '内容')->keyCreateTime()->keyStatus()
                ->buttonSubmit(U('editWeibo'))->buttonBack()
                ->data($weibo)
                ->display();
        }
    }


    public function comment()
    {
        $aWeiboId = I('weibo_id', 0, 'intval');
        $aPage = I('page', 1, 'intval');
        $r = 20;
        //读取评论列表
        $map = array('status' => array('EGT', 0));
        if ($aWeiboId) $map['weibo_id'] = $aWeiboId;
        $model = M('WeiboComment');
        $list = $model->where($map)->order('id desc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('回复管理')
            ->setStatusUrl(U('setCommentStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyText('content', '内容')->keyUid()->keyCreateTime()->keyStatus()->keyDoActionEdit('editComment?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function commentTrash()
    {
        $aPage = I('page', 1, 'intval');
        $r = 20;
        $builder = new AdminListBuilder();
        $builder->clearTrash('WeiboComment');
        //读取评论列表
        $map = array('status' => -1);
        $model = M('WeiboComment');
        $list = $model->where($map)->order('id desc')->page($aPage, $r)->select();
        $totalCount = $model->where($map)->count();
        //显示页面
        $builder->title('回复回收站')
            ->setStatusUrl(U('setCommentStatus'))->buttonRestore()->buttonClear('WeiboComment')
            ->keyId()->keyText('content', '内容')->keyUid()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setCommentStatus($ids, $status)
    {
        foreach($ids as $id){
            $comemnt = D('Weibo/WeiboComment')->getComment($id);
            if($status == 1){
                D('Weibo/Weibo')->where(array('id' => $comemnt['weibo_id']))->setInc('comment_count');
            }else{
                D('Weibo/Weibo')->where(array('id' => $comemnt['weibo_id']))->setDec('comment_count');
            }
            S('weibo_' . $comemnt['weibo_id'], null);
        }



        $builder = new AdminListBuilder();
        $builder->doSetStatus('WeiboComment', $ids, $status);
    }

    public function editComment()
    {
        $aId = I('id', 0, 'intval');

        $aContent = I('post.content', '', 'op_t');
        $aCreateTime = I('post.create_time', time(), 'intval');
        $aStatus = I('post.status', 1, 'intval');

        $model = M('WeiboComment');
        if (IS_POST) {
            //写入数据库
            $data = array('content' => $aContent, 'create_time' => $aCreateTime, 'status' => $aStatus);
            $result = $model->where(array('id' => $aId))->save($data);
            S('weibo_comment_' . $aId);
            if (!$result) {
                $this->error('编辑出错');
            }
            //显示成功消息
            $this->success('编辑成功', U('comment'));
        } else {
            //读取评论内容
            $comment = $model->where(array('id' => $aId))->find();
            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title('编辑评论')
                ->keyId()->keyTextArea('content', '内容')->keyCreateTime()->keyStatus()
                ->data($comment)
                ->buttonSubmit(U('editComment'))->buttonBack()
                ->display();
        }
    }


    public function topic()
    {
        $aPage = I('page', 1, 'intval');
        $aName = I('name', '', 'op_t');
        $r = 20;
        $model = M('WeiboTopic');
        $aName && $map['name'] = array('like', '%' . $aName . '%');

        $list = $model->where($map)->order('id asc')->page($aPage, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $attr1 = $attr;
        $attr1['url'] = $builder->addUrlParam(U('setTopicTop'), array('top' => 1));
        $attr0 = $attr;
        $attr0['url'] = $builder->addUrlParam(U('setTopicTop'), array('top' => 0));

        $attr_del = $attr;
        $attr_del['url'] = $builder->addUrlParam(U('delTopic'), array());

        $builder->title('话题管理')
            ->button('推荐', $attr1)->button('取消推荐', $attr0)
            ->button('删除', $attr_del)
            ->keyId()
            ->keyLink('name', '内容', 'weibo?content=%23{$name}%23')
            ->keyUid()
            ->keyText('logo', 'Logo')
            ->keyText('intro', '导语')
            ->keyText('qrcode', '二维码')
            ->keyText('uadmin', '话题管理员')
            ->keyText('read_count', '阅读量')
            ->keyMap('is_top', '是否推荐', array(0 => '不推荐', 1 => '推荐'))
            ->search('名称', 'name')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setTopicTop($ids, $top)
    {
        M('WeiboTopic')->where(array('id' => array('in', $ids)))->setField('is_top', $top);
        S('topic_rank', null, 60);
        $this->success('设置成功', $_SERVER['HTTP_REFERER']);
    }

    public function delTopic($ids)
    {

        M('WeiboTopic')->where(array('id' => array('in', $ids)))->delete();
        S('topic_rank', null, 60);
        $this->success('删除成功', $_SERVER['HTTP_REFERER']);
    }


}
