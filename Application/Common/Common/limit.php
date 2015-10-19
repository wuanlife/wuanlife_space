<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-10
 * Time: 下午4:39
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */


class ActionLimit
{

    var $item = array();
    var $state = true;
    var $url;
    var $info = '';
    var $punish = array(
        array('warning','警告并禁止'),
        array('logout_account', '强制退出登陆'),
        array('ban_account', '封停账户'),
        array('ban_ip', '封IP'),
    );

    function __construct()
    {
        $this->url = '';
        $this->info = '';
        $this->state = true;
    }

    function addCheckItem($action = null, $model = null, $record_id = null, $user_id = null, $ip = false)
    {
        $this->item[] = array('action' => $action, 'model' => $model, 'record_id' => $record_id, 'user_id' => $user_id, 'action_ip' => $ip);
        return $this;
    }

    function check()
    {
        $items = $this->item;
        foreach ($items as &$item) {
            $this->checkOne($item);
        }
        unset($item);
    }

    function checkOne($item)
    {
        $item['action_ip'] = $item['action_ip'] ? get_client_ip(1) : null;
        foreach ($item as $k => $v) {
            if (empty($v)) {
                unset($item[$k]);
            }
        }
        unset($k, $v);
        $time = time();
        $map['action_list'] = array(array('like', '%[' . $item['action'] . ']%'), '', 'or');
        $map['status'] = 1;
        $limitList = D('ActionLimit')->getList($map);
        !empty($item['action']) && $item['action_id'] = M('action')->where(array('name' => $item['action']))->getField('id');
        foreach ($limitList as &$val) {
            $ago = get_time_ago($val['time_unit'], $val['time_number'], $time);
            $item['create_time'] = array('egt', $ago);
            $log = M('action_log')->where($item)->order('create_time desc')->select();
            if (count($log) >= $val['frequency']) {
                $punishes = explode(',', $val['punish']);
                foreach ($punishes as $punish) {
                    //执行惩罚
                    if (method_exists($this, $punish)) {
                        $this->$punish($item,$val);
                    }
                }
                unset($punish);
                if ($val['if_message']) {
                    D('Message')->sendMessageWithoutCheckSelf($item['user_id'], '系统消息',$val['message_content'],$_SERVER['HTTP_REFERER']);
                }
            }
        }
        unset($val);
    }

    /**
     * logout_account 注销已登录帐号
     * @param $item
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    function logout_account($item)
    {
        D('Member')->logout();
    }

    /**
     * ban_account  封停帐号
     * @param $item
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    function ban_account($item)
    {
        set_user_status($item['user_id'], 0);
    }

    function ban_ip($item,$val)
    {
       //TODO 进行封停IP的操作
    }

    function warning($item,$val){
        $this->state = false;
        $this->info = '操作频繁，请'.$val['time_number'].get_time_unit($val['time_unit']).'后再试';
        $this->url = U('home/index/index');
    }
}


function check_action_limit($action = null, $model = null, $record_id = null, $user_id = null, $ip = false)
{
    $obj = new ActionLimit();

    $item = array('action' => $action, 'model' => $model, 'record_id' => $record_id, 'user_id' => $user_id, 'action_ip' => $ip);
    if(empty($record_id)){
        unset($item['record_id']);
    }
    $obj->checkOne($item);
    $return = array();
    if (!$obj->state) {
        $return['state'] = $obj->state;
        $return['info'] = $obj->info;
        $return['url'] = $obj->url;
    }else{
        $return['state'] = true;
    }
    return $return;
}

function get_time_ago($type = 'second', $some = 1, $time = null)
{
    $time = empty($time) ? time() : $time;
    switch ($type) {
        case 'second':
            $result = $time - $some;
            break;
        case 'minute':
            $result = $time - $some * 60;
            break;
        case 'hour':
            $result = $time - $some * 60 * 60;
            break;
        case 'day':
            $result = strtotime('-' . $some . ' day', $time);
            break;
        case 'week':
            $result = strtotime('-' . ($some * 7) . ' day', $time);
            break;
        case 'month':
            $result = strtotime('-' . $some . ' month', $time);
            break;
        case 'year':
            $result = strtotime('-' . $some . ' year', $time);
            break;
        default:
            $result = $time - $some;
    }
    return $result;
}

function get_time_unit($key = null){
    $array = array('second' => '秒', 'minute' => '分', 'hour' => '小时', 'day' => '天', 'week' => '周', 'month' => '月', 'year' => '年');
    return empty($key)?$array:$array[$key];
}

/**
 * 单位格式时间转换成时间戳
 * @param string $str 单位格式时间
 * @param string $type +:生成的是之后的时间撮，-:生成的是之前的时间撮
 * @param null $time 基准时间点
 * @return array|int|null
 * @author 郑钟良<zzl@ourstu.com>
 */
function unitTime_to_time($str='1 day',$type='-',$time=null)
{
    $time = empty($time) ? time() : $time;
    $str=explode(' ',$str);
    switch ($str[1]) {
        case 'second':
            if($type=='-'){
                $result=$time-$str[0];
            }else{
                $result=$time+$str[0];
            }
            break;
        case 'minute':
            if($type=='-'){
                $result=$time-$str[0] * 60;
            }else{
                $result=$time+$str[0] * 60;
            }
            break;
        case 'hour':
            if($type=='-'){
                $result=$time-$str[0] * 60 * 60;
            }else{
                $result=$time+$str[0] * 60 * 60;
            }
            break;
        case 'day':
            $result = strtotime($type . $str[0] . ' day', $time);
            break;
        case 'week':
            $result = strtotime($type . ($str[0] * 7) . ' day', $time);
            break;
        case 'month':
            $result = strtotime($type . $str[0] . ' month', $time);
            break;
        case 'year':
            $result = strtotime($type . $str[0] . ' year', $time);
            break;
        default:
            $result = $time - $str[0];
    }
    return $result;
}

/**
 * 30 day -> 30 天
 * 单位格式时间转换成可显示的中文单位格式时间
 * @param string $str
 * @return string
 * @author 郑钟良<zzl@ourstu.com>
 */
function unitTime_to_showUnitTime($str='1 day')
{
    $str=explode(' ',$str);
    $replace=get_time_unit();
    $str[1]=$replace[$str[1]];
    $str=implode(' ',$str);
    return $str;
}


function get_punish_name($key){
    !is_array($key) && $key = explode(',',$key);
    $obj =new \ActionLimit();
    $punish = $obj->punish;
    $return = array();
    foreach($key as $val){
        foreach($punish as $v){
            if($v[0] == $val){
                $return[]= $v[1];
            }
        }
    }
    return implode(',',$return);
}

function get_action_name($key){
    !is_array($key) && $key = explode(',',str_replace(array('[',']'),'',$key));
    $return = array();
    foreach($key as $val){
        $return[] = D('Action')->where(array('name'=>$val))->getField('title');
    }
    return implode(',',$return);
}