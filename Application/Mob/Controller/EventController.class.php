<?php


namespace Mob\Controller;

use Think\Controller;

class EventController extends BaseController
{

    protected $eventModel = '';
    protected $eventAttendModel = '';

    public function _initialize()
    {
        $this->eventModel = D('Event/Event');
        $this->eventAttendModel = D('Event/EventAttend');
        $this->eventTypeModel = D('Event/EventType');
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'home','href'=>U('Mob/event/index')),
                array('type'=>'message'),
            ),
            'center'=>array('title'=>'活动')
        );
        $this->setMobTitle('活动');
        if(is_login()){
            if(check_auth('Event/Index/add')){
                $this->_top_menu_list['right'][]=array('type'=>'edit','href'=>U('Mob/event/add'));
            }else{
                $this->_top_menu_list['right'][]=array('type'=>'edit','info'=>'您无活动发布权限！');
            }
        }else{
            $this->_top_menu_list['right'][]=array('type'=>'edit','info'=>'登录后才能操作！');
        }

        $tree = D('EventType')->where(array('status' => 1))->select();
        $this->assign('type_tree', $tree);
        $this->assign('top_menu_list', $this->_top_menu_list);

    }


    public function index($type_id = 0, $norh = 'new')
    {
        $aPage = I('page', 1, 'intval');

        $type_id = intval($type_id);
        if ($type_id != 0) {
            $map['type_id'] = $type_id;
        }
        $map['status'] = 1;
        $order = 'create_time desc';
        $norh == 'hot' && $order = 'signCount desc';
        $content = $this->eventModel->where($map)->order($order)->page($aPage, 10)->select();

        $totalCount = $this->eventModel->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->getType($v['type_id']);
            $v['check_isSign'] = $this->eventAttendModel->where(array('uid' => is_login(), 'event_id' => $v['id']))->select();
        }
        unset($v);
        $this->assign('type_id', $type_id);
        $this->assign('contents', $content);
        $this->assign('norh', $norh);
        $this->assign('totalPageCount', $totalCount);

        if (IS_AJAX) {
            $this->ajaxReturn($this->fetch('_eventlist'));
        } else {
            $this->display();
        }
    }


    private function getType($type_id)
    {
        $type = $this->eventTypeModel->where('id=' . $type_id)->find();
        return $type;
    }


    /**
     * 我的活动页面
     * @param int $page
     * @param int $type_id
     * @param string $norh
     * autor:xjw129xjt
     */
    public function myevent($page = 1, $type_id = 0, $lora = '')
    {
        $this->setMobTitle('我的活动');
        if (!is_login()) {
            $this->error('请登录后再查看');
        }

        $type_id = intval($type_id);
        if ($type_id != 0) {
            $map['type_id'] = $type_id;
        }

        $map['status'] = 1;
        $order = 'create_time desc';
        if ($lora == 'attend') {
            $attend = $this->eventAttendModel->where(array('uid' => is_login()))->select();
            $enentids = getSubByKey($attend, 'event_id');
            $map['id'] = array('in', $enentids);
        } else {
            $map['uid'] = is_login();
        }
        $content = $this->eventModel->where($map)->order($order)->page($page, 10)->select();

        $totalCount = $this->eventModel->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->getType($v['type_id']);
            $v['check_isSign'] = $this->eventAttendModel->where(array('uid' => is_login(), 'event_id' => $v['id']))->select();
        }
        unset($v);
        $this->assign('type_id', $type_id);
        $this->assign('contents', $content);
        $this->assign('lora', $lora);
        $this->assign('totalPageCount', $totalCount);

        $this->assign('current', 'myevent');


        if (IS_AJAX) {
            $this->ajaxReturn($this->fetch('_eventlist'));
        } else {
            $this->display();
        }

    }


    public function detail($id = 0)
    {
        $this->setMobTitle('活动详情');
        $check_isSign = $this->eventAttendModel->where(array('uid' => is_login(), 'event_id' => $id))->select();

        $this->assign('check_isSign', $check_isSign);

        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $id))->find();
        if (!$event_content) {
            $this->error('404 not found');
        }
        $this->eventModel->where(array('id' => $id))->setInc('view_count');

        $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
        $event_content['type'] = $this->getType($event_content['type_id']);


        $menber = $this->eventAttendModel->where(array('event_id' => $id, 'status' => 1))->select();
        foreach ($menber as $k => $v) {
            $event_content['member'][$k] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $v['uid']);

        }

        $this->assign('content', $event_content);
        //$this->setTitle('{$content.title|op_t}' . '——活动');
        // $this->setKeywords('{$content.title|op_t}' . ',活动');
        // $this->getRecommend();
        $this->display();
    }


    public function add()
    {
        $this->checkAuth('Event/Index/add', -1, '您无活动发布权限。');
        $this->setTitle('添加活动' . '——活动');
        $this->setKeywords('添加' . ',活动');
        $this->display();
    }


    /**
     * 编辑活动
     * @param $id
     * autor:xjw129xjt
     */
    public function edit($id)
    {
        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $id))->find();
        if (!$event_content) {
            $this->error('404 not found');
        }
        $this->checkAuth('Event/Index/edit', $event_content['uid'], '您无该活动编辑权限。');
        $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
        $this->assign('event', $event_content);
        $this->setMobTitle('编辑活动' . '——活动');
        $this->setKeywords('编辑' . ',活动');
        $this->display('add');
    }


    /**
     * 发布活动
     * @param int $id
     * @param int $cover_id
     * @param string $title
     * @param string $explain
     * @param string $sTime
     * @param string $eTime
     * @param string $address
     * @param int $limitCount
     * @param string $deadline
     * autor:xjw129xjt
     */
    public function doPost($id = 0, $cover_id = 0, $title = '', $explain = '', $sTime = '', $eTime = '', $address = '', $limitCount = 0, $deadline = '', $type_id = 0)
    {

        if (!is_login()) {
            $this->error('请登陆后再投稿。');
        }
        if (!$cover_id) {
            $this->error('请上传封面。');
        }
        if (trim(op_t($title)) == '') {
            $this->error('请输入标题。');
        }
        if ($type_id == 0) {
            $this->error('请选择分类。');
        }
        if (trim(op_h($explain)) == '') {
            $this->error('请输入内容。');
        }
        if (trim(op_h($address)) == '') {
            $this->error('请输入地点。');
        }
        if ($eTime < $deadline) {
            $this->error('报名截止不能大于活动结束时间');
        }
        if ($deadline == '') {
            $this->error('请输入截止日期');
        }
        if ($sTime > $eTime) {
            $this->error('活动开始时间不能大于活动结束时间');
        }
        $content = $this->eventModel->create();
        $content['explain'] = filter_content($content['explain']);
        $content['title'] = op_t($content['title']);
        $content['sTime'] = strtotime($content['sTime']);
        $content['eTime'] = strtotime($content['eTime']);
        $content['deadline'] = strtotime($content['deadline']);
        $content['type_id'] = intval($type_id);
        if ($id) {
            $content_temp = $this->eventModel->find($id);
            $this->checkAuth('Event/Index/edit', $content_temp['uid'], '您无该活动编辑权限。');
            $this->checkActionLimit('add_event', 'event', $id, is_login(), true);
            $content['uid'] = $content_temp['uid']; //权限矫正，防止被改为管理员
            $rs = $this->eventModel->save($content);
            if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('detail', array('id' => $id));
                D('Weibo')->addWeibo(is_login(), "我修改了活动【" . $title . "】：" . $postUrl);
            }
            if ($rs) {
                action_log('add_event', 'event', $id, is_login());
                $this->success('编辑成功。', U('detail', array('id' => $content['id'])));
            } else {
                $this->success('编辑失败。', '');
            }
        } else {
            $this->checkAuth('Event/Index/add', -1, '您无活动发布权限。');
            $this->checkActionLimit('add_event', 'event', 0, is_login(), true);
            if (modC('NEED_VERIFY', 0) && !is_administrator()) //需要审核且不是管理员
            {
                $content['status'] = 0;
                $tip = '但需管理员审核通过后才会显示在列表中，请耐心等待。';
                $user = query_user(array('username', 'nickname'), is_login());
                D('Common/Message')->sendMessage(C('USER_ADMINISTRATOR'), $title = '活动发布提醒', "{$user['nickname']}发布了一个活动，请到后台审核。", 'Admin/Event/verify', array(), is_login(), 2);
            }
            $aIsAttend = I('post.isAttend', 0, 'intval');
            if ($aIsAttend) {
                $content['attentionCount'] = 1;
                $content['signCount'] = 1;
            }
            $rs = $this->eventModel->add($content);

            if ($aIsAttend) {
                $data['uid'] = is_login();
                $data['event_id'] = $rs;
                $data['name'] = '活动发布者';
                $data['create_time'] = time();
                $data['status'] = 1;
                $this->eventAttendModel->add($data);
            }


            if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                //同步到微博
                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Event/Index/detail', array('id' => $rs));
                D('Weibo')->addWeibo(is_login(), "我发布了一个新的活动【" . $title . "】：" . $postUrl);
            }

            if ($rs) {
                action_log('add_event', 'event', $rs, is_login());
                $this->success('发布成功。' . $tip, U('index'));
            } else {
                $this->success('发布失败。', '');
            }
        }

    }


    /**
     * 活动成员
     * @param int $id
     * @param string $tip
     * autor:xjw129xjt
     */
    public function member($id = 0, $tip = 'all')
    {
        if ($tip == 'sign') {
            $map['status'] = 0;
        }
        if ($tip == 'attend') {
            $map['status'] = 1;
        }

        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $id))->find();
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        $map['event_id'] = $id;
        $member = $this->eventAttendModel->where($map)->select();
        foreach ($member as &$v) {
            $v['user_info'] = query_user(array('uid', 'nickname', 'space_url', 'avatar32', 'avatar64'), $v['uid']);
            if ($v['image']) {
                $v['image_info'] = '<div class="popup-gallery"><a class="popup" href="' . get_cover($v['image'], 'path') . '"><img src="' . getThumbImageById($v['image'], 50, 50) . '"/></a></div>';
            }
            $v['attach_info'] = D('File')->find($v['attach']);
            $v['attach_info']['link'] = get_pic_src($v['attach_info']['savepath'] . $v['attach_info']['savename']);
            if ($v['status'] == 0) {
                $v['status_info'] = '待审核';
            }
            if ($v['status'] == 1) {
                $v['status_info'] = '已审核';
            }
        }
        unset($v);

        $this->assign('all_count', $this->eventAttendModel->where(array('event_id' => $id))->count());
        $this->assign('sign_count', $this->eventAttendModel->where(array('event_id' => $id, 'status' => 0))->count());
        $this->assign('attend_count', $this->eventAttendModel->where(array('event_id' => $id, 'status' => 1))->count());

        $this->assign('event_member', $member);
        $this->assign('event_content', $event_content);
        $this->assign('tip', $tip);
        $this->setTitle($event_content['title'] . '——活动');
        $this->setKeywords($event_content['title'] . ',活动');


        $tmp = 'attend';
        $this->display($tmp);
    }


    public function sign()
    {
        $aId = I('get.id', 0, 'intval');
        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $aId))->find();
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        if (!is_login()) {
            $this->error('请登录');
        }
        $this->assign('event', $event_content);
        $this->display();

    }


    /**
     * 报名参加活动
     * @param $event_id
     * @param $name
     * @param $phone
     * autor:xjw129xjt
     */
    public function doSign($event_id, $name, $phone)
    {

        if (!is_login()) {
            $this->error('请登陆后再报名。');
        }
        if (!$event_id) {
            $this->error('参数错误');
        }
        if (trim(op_t($name)) == '') {
            $this->error('请输入姓名。');
        }
        if (trim($phone) == '') {
            $this->error('请输入手机号码。');
        }
        $check = $this->eventAttendModel->where(array('uid' => is_login(), 'event_id' => $event_id))->select();
        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $event_id))->find();
        $this->checkAuth('Event/Index/doSign', $event_content['uid'], '你没有报名参加活动的权限！');
        $this->checkActionLimit('event_do_sign', 'event', $event_id, is_login());
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        /*      if ($event_content['attentionCount'] + 1 > $event_content['limitCount']) {
                  $this->error('超过限制人数，报名失败');
              }*/
        if (time() > $event_content['deadline']) {
            $this->error('报名已经截止');
        }
        if (!$check) {
            $data['uid'] = is_login();
            $data['event_id'] = $event_id;
            $data['name'] = $name;
            $data['phone'] = $phone;
            $data['create_time'] = time();
            $res = $this->eventAttendModel->add($data);
            if ($res) {
                D('Message')->sendMessageWithoutCheckSelf($event_content['uid'], '报名通知', query_user('nickname', is_login()) . '报名参加了活动]' . $event_content['title'] . ']，请速去审核！', 'Event/Index/member', array('id' => $event_id));

                $this->eventModel->where(array('id' => $event_id))->setInc('signCount');
                action_log('event_do_sign', 'event', $event_id, is_login());
                $this->success('报名成功。', 'refresh');
            } else {
                $this->error('报名失败。', '');
            }
        } else {
            $this->error('您已经报过名了。', '');
        }
    }


    /**
     * ajax删除活动
     * @param $event_id
     * autor:xjw129xjt
     */
    public function doDelEvent($event_id)
    {

        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        $this->checkAuth('Event/Index/doDelEvent', $event_content['uid'], '你没有删除活动的权限！');
        $res = $this->eventModel->where(array('status' => 1, 'id' => $event_id))->setField('status', 0);
        if ($res) {
            $this->success('删除成功！');
        } else {
            $this->error('操作失败！');
        }
    }

    /**
     * ajax提前结束活动
     * @param $event_id
     * autor:xjw129xjt
     */
    public function doEndEvent($event_id)
    {

        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        $this->checkAuth('Event/Index/doEndEvent', $event_content['uid'], '你没有结束活动的权限！');
        $data['eTime'] = time();
        $data['deadline'] = time();
        $res = $this->eventModel->where(array('status' => 1, 'id' => $event_id))->setField($data);
        if ($res) {
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }

    }


    /**
     * 审核
     * @param $uid
     * @param $event_id
     * @param $tip
     * autor:xjw129xjt
     */
    public function shenhe($uid, $event_id, $tip)
    {
        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content || $event_content['eTime'] < time()) {
            $this->error('活动不存在或活动已结束！');
        }
        $this->checkAuth('Event/Index/shenhe', $event_content['uid'], '你没有审核的权限！');
        $res = $this->eventAttendModel->where(array('uid' => $uid, 'event_id' => $event_id))->setField('status', $tip);
        if ($tip) {
            $this->eventModel->where(array('id' => $event_id))->setInc('attentionCount');
            D('Message')->sendMessageWithoutCheckSelf($uid, '审核通知', query_user('nickname', is_login()) . '已经通过了您对活动[' . $event_content['title'] . ']的报名请求', 'Event/Index/detail', array('id' => $event_id));
        } else {
            $this->eventModel->where(array('id' => $event_id))->setDec('attentionCount');
            D('Message')->sendMessageWithoutCheckSelf($uid, '取消审核通知', query_user('nickname', is_login()) . '取消了您对活动[' . $event_content['title'] . ']的报名请求', 'Event/Index/member', array('id' => $event_id));
        }
        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
        }
    }


    /**
     * 取消报名
     * @param $event_id
     * autor:xjw129xjt
     */
    public function unSign($event_id)
    {

        $event_content = $this->eventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content || $event_content['eTime'] < time()) {
            $this->error('活动不存在或活动已结束！');
        }


        $check = $this->eventAttendModel->where(array('uid' => is_login(), 'event_id' => $event_id))->find();

        $res = $this->eventAttendModel->where(array('uid' => is_login(), 'event_id' => $event_id))->delete();
        if ($res) {
            if ($check['status']) {
                $this->eventModel->where(array('id' => $event_id))->setDec('attentionCount');
            }
            $this->eventModel->where(array('id' => $event_id))->setDec('signCount');
            if ($event_content['sign_type']) {
                $this->eventModel->where(array('id' => $event_id))->setDec('sign_user_num', $check['count']);
            }

            D('Message')->sendMessageWithoutCheckSelf($event_content['uid'], '取消报名通知', query_user('nickname', is_login()) . '取消了对活动[' . $event_content['title'] . ']的报名', 'Event/Index/detail', array('id' => $event_id));

            $this->success('取消报名成功');
        } else {
            $this->error('操作失败');
        }
    }


}