<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController
{

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {

        if (UID) {

            if(IS_POST){
                $count_day=I('post.count_day', C('COUNT_DAY'),'intval');
                if(M('Config')->where(array('name'=>'COUNT_DAY'))->setField('value',$count_day)===false){
                    $this->error('设置失败。');
                }else{
                   S('DB_CONFIG_DATA',null);
                    $this->success('设置成功。','refresh');
                }

            }else{
                $this->meta_title = '管理首页';
                $today = date('Y-m-d', time());
                $today = strtotime($today);
                $count_day = C('COUNT_DAY',null,7);
                $count['count_day']=$count_day;
                for ($i = $count_day; $i--; $i >= 0) {
                    $day = $today - $i * 86400;
                    $day_after = $today - ($i - 1) * 86400;
                    $week_map=array('Mon'=>'周一','Tue'=>'周二','Wed'=>'周三','Thu'=>'周四','Fri'=>'周五','Sat'=>'<strong>周六</strong>','Sun'=>'<strong>周日</strong>');
                    $week[] = date('m月d日 ', $day). $week_map[date('D',$day)];
                    $user = UCenterMember()->where('status=1 and reg_time >=' . $day . ' and reg_time < ' . $day_after)->count() * 1;
                    $registeredMemeberCount[] = $user;
                    if ($i == 0) {
                        $count['today_user'] = $user;
                    }
                }
                $week = json_encode($week);
                $this->assign('week', $week);
                $count['total_user'] = $userCount = UCenterMember()->where(array('status' => 1))->count();
                $count['today_action_log'] = M('ActionLog')->where('status=1 and create_time>=' . $today)->count();
                $count['last_day']['days'] = $week;
                $count['last_day']['data'] = json_encode($registeredMemeberCount);
                // dump($count);exit;

                $this->assign('count', $count);
                $this->display();
            }


        } else {
            $this->redirect('Public/login');
        }
    }

}
