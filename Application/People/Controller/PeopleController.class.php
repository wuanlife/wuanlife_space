<?php


namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;

class PeopleController extends AdminController
{
    public function config()
    {
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();
        $builder->title('基本设置');
        $data['MAX_SHOW_HEIGHT'] = $data['MAX_SHOW_HEIGHT'] ? $data['MAX_SHOW_HEIGHT'] :160;
        $builder->keyInteger('MAX_SHOW_HEIGHT', '标签面板初始最大展示高度')->keyDefault('MAX_SHOW_HEIGHT',160);

        $role_list=M('Role')->where(array('status'=>1))->field('id,title')->select();
        foreach($role_list as &$val){
            $val=array('data-id' => $val['id'], 'title' => $val['title']);
        }
        unset($val);
        $default = array(array('data-id' => 'disable', 'title' => '禁用', 'items' => $role_list), array('data-id' => 'enable', 'title' => '启用', 'items' => array()));
        $builder->keyKanban('SHOW_ROLE_TAB', '找人界面展示身份tab','拖拽到右侧以展示这些身份tab，操作的项目对应系统身份');
        $data['SHOW_ROLE_TAB'] = $builder->parseKanbanArray($data['SHOW_ROLE_TAB'], $role_list, $default);
        $builder->group('基本设置', 'MAX_SHOW_HEIGHT,SHOW_ROLE_TAB');

        $data['USER_SHOW_TITLE1'] = $data['USER_SHOW_TITLE1'] ? $data['USER_SHOW_TITLE1'] : '活跃会员';
        $data['USER_SHOW_COUNT1'] = $data['USER_SHOW_COUNT1'] ? $data['USER_SHOW_COUNT1'] : 5;
        $data['USER_SHOW_ORDER_FIELD1'] = $data['USER_SHOW_ORDER_FIELD1'] ? $data['USER_SHOW_ORDER_FIELD1'] : 'score1';
        $data['USER_SHOW_ORDER_TYPE1'] = $data['USER_SHOW_ORDER_TYPE1'] ? $data['USER_SHOW_ORDER_TYPE1'] : 'desc';
        $data['USER_SHOW_CACHE_TIME1'] = $data['USER_SHOW_CACHE_TIME1'] ? $data['USER_SHOW_CACHE_TIME1'] : '600';


        $data['USER_SHOW_TITLE2'] = $data['USER_SHOW_TITLE2'] ? $data['USER_SHOW_TITLE2'] : '最新会员';
        $data['USER_SHOW_COUNT2'] = $data['USER_SHOW_COUNT2'] ? $data['USER_SHOW_COUNT2'] : 5;
        $data['USER_SHOW_ORDER_FIELD2'] = $data['USER_SHOW_ORDER_FIELD2'] ? $data['USER_SHOW_ORDER_FIELD2'] : 'reg_time';
        $data['USER_SHOW_ORDER_TYPE2'] = $data['USER_SHOW_ORDER_TYPE2'] ? $data['USER_SHOW_ORDER_TYPE2'] : 'desc';
        $data['USER_SHOW_CACHE_TIME2'] = $data['USER_SHOW_CACHE_TIME2'] ? $data['USER_SHOW_CACHE_TIME2'] : '600';


        $score=D("Ucenter/Score")->getTypeList(array('status'=>1));
        $order['reg_time']='注册时间';
        $order['last_login_time']='最后登录时间';


        foreach ($score as $s) {
            $order['score'.$s['id']]='【'.$s['title'].'】';
        }

        $builder->keyText('USER_SHOW_TITLE1', '标题名称', '在首页展示块的标题');
        $builder->keyText('USER_SHOW_COUNT1', '显示人数', '只有在网站首页模块中启用了专辑块之后才会显示');
        $builder->keyRadio('USER_SHOW_ORDER_FIELD1', '排序值', '展示模块的数据排序方式', $order);
        $builder->keyRadio('USER_SHOW_ORDER_TYPE1', '排序方式', '展示模块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'));
        $builder->keyText('USER_SHOW_CACHE_TIME1', '缓存时间', '默认600秒，以秒为单位');

        $builder->keyText('USER_SHOW_TITLE2', '标题名称', '在首页展示块的标题');
        $builder->keyText('USER_SHOW_COUNT2', '显示人数', '只有在网站首页模块中启用了专辑块之后才会显示');
        $builder->keyRadio('USER_SHOW_ORDER_FIELD2', '排序值', '展示模块的数据排序方式', $order);
        $builder->keyRadio('USER_SHOW_ORDER_TYPE2', '排序方式', '展示模块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'));
        $builder->keyText('USER_SHOW_CACHE_TIME2', '缓存时间', '默认600秒，以秒为单位');



        $builder->group('首页展示左侧栏', 'USER_SHOW_TITLE1,USER_SHOW_COUNT1,USER_SHOW_ORDER_FIELD1,USER_SHOW_ORDER_TYPE1,USER_SHOW_CACHE_TIME1');
        $builder->group('首页展示右侧栏', 'USER_SHOW_TITLE2,USER_SHOW_COUNT2,USER_SHOW_ORDER_FIELD2,USER_SHOW_ORDER_TYPE2,USER_SHOW_CACHE_TIME2');
        $builder->data($data);
        $builder->buttonSubmit();
        $builder->display();
    }

}