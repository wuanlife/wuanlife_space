<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-4
 * Time: 下午6:57
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Mob\Widget;

use Think\Action;

class LoginWidget extends Action
{
    public function login($type = "quickLogin")
    {
        if ($type != "quickLogin") {
            if (is_login()) {
                redirect(U('Home/Index/index'));
            }
        }
        $this->assign('login_type', $type);
        $ph = array();
        check_reg_type('username') && $ph[] = '用户名';
        check_reg_type('email') && $ph[] = '邮箱';
        check_reg_type('mobile') && $ph[] = '手机号';
        $this->assign('ph', implode('/', $ph));
        $this->display('Widget/Login/login');
    }

    public function doLogin()
    {
        $aUsername = $username = I('post.username', '', 'op_t');
        $aPassword = I('post.password', '', 'op_t');
        $aVerify = I('post.verify', '', 'op_t');
        $aRemember = I('post.remember', 0, 'intval');


      //  dump($aRemember);exit;
        /* 检测验证码 */
        if (check_verify_open('login')) {
            if (!check_verify($aVerify)) {
                $res['info']="验证码输入错误。";
                return $res;
            }
        }

        /* 调用UC登录接口登录 */
        check_username($aUsername, $email, $mobile, $aUnType);

        if (!check_reg_type($aUnType)) {
            $res['info']="该类型未开放登录。";
        }

        $uid = UCenterMember()->login($username, $aPassword, $aUnType);

        if (0 < $uid) { //UC登录成功
            /* 登录用户 */
            $Member = D('Mob/Member');
            $args['uid'] = $uid;
            $args = array('uid'=>$uid,'nickname'=>$username);
            check_and_add($args);

            if ($Member->mobileLogin($uid, $aRemember == 1)) { //登录用户
                //TODO:跳转到登录前页面


                if (UC_SYNC && $uid != 1) {
                    //同步登录到UC
                    $ref = M('ucenter_user_link')->where(array('uid' => $uid))->find();
                    $html = '';
                    $html = uc_user_synlogin($ref['uc_uid']);
                }

                $oc_config =  include_once './OcApi/oc_config.php';
                if ($oc_config['SSO_SWITCH']) {
                    include_once  './OcApi/OCenter/OCenter.php';
                    $OCApi = new \OCApi();
                    $html = $OCApi->ocSynLogin($uid);
                }

                $res['status']=1;
                $res['info']=$html;
                //$this->success($html, get_nav_url(C('AFTER_LOGIN_JUMP_URL')));
            } else {
                $res['info']=$Member->getError();
            }

        } else { //登录失败
            switch ($uid) {
                case -1:
                    $res['info']= '用户不存在或被禁用！';
                    break; //系统级别禁用
                case -2:
                    $res['info']= '密码错误！';
                    break;
                default:
                    $res['info']= $uid;
                    break; // 0-接口参数错误（调试阶段使用）
            }
        }
        return $res;
    }
} 