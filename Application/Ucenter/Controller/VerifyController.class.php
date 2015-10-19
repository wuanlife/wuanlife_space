<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM3:40
 */

namespace Ucenter\Controller;

use Think\Controller;

class VerifyController extends Controller
{


    /**
     * sendVerify 发送验证码
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function sendVerify()
    {
        $aAccount = $cUsername = I('post.account', '', 'op_t');
        $aType = I('post.type', '', 'op_t');
        $aType = $aType == 'mobile' ? 'mobile' : 'email';
        $aAction = I('post.action', 'config', 'op_t');
        if (!check_reg_type($aType)) {
            $str = $aType == 'mobile' ? '手机' : '邮箱';
            $this->error($str . '选项已关闭！');
        }


        if (empty($aAccount)) {
            $this->error('帐号不能为空');
        }
        check_username($cUsername, $cEmail, $cMobile);
        $time = time();
        if($aType == 'mobile'){
            $resend_time =  modC('SMS_RESEND','60','USERCONFIG');
            if($time <= session('verify_time')+$resend_time ){
                $this->error('请'.($resend_time-($time-session('verify_time'))).'秒后再发');
            }
        }


        if ($aType == 'email' && empty($cEmail)) {
            $this->error('请验证邮箱格式是否正确');
        }
        if ($aType == 'mobile' && empty($cMobile)) {
            $this->error('请验证手机格式是否正确');
        }

        $checkIsExist = UCenterMember()->where(array($aType => $aAccount))->find();
        if ($checkIsExist) {
            $str = $aType == 'mobile' ? '手机' : '邮箱';
            $this->error('该' . $str . '已被其他用户使用！');
        }

        $verify = D('Verify')->addVerify($aAccount, $aType);
        if (!$verify) {
            $this->error('发送失败！');
        }

        $res =  A(ucfirst($aAction))->doSendVerify($aAccount, $verify, $aType);
        if ($res === true) {
            if($aType == 'mobile'){
                session('verify_time',$time);
            }
            $this->success('发送成功，请查收');
        } else {
            $this->error($res);
        }

    }


}