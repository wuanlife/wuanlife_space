<?php

namespace Ucenter\Controller;

use Think\Controller;

class ConfigController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        if (!is_login()) {
            $this->error('请登陆后再访问本页面。');
        }
        $this->setTitle('编辑资料');
        $this->_assignSelf();
        $this->_haveOtherRole();
    }

    /**关联自己的信息
     * @auth 陈一枭
     */
    private function _assignSelf()
    {
        $self = query_user(array('avatar128', 'nickname', 'space_url', 'space_link','score','title'));
        $this->assign('self', $self);
    }

    /**
     * 是否拥有其他角色或可拥有角色
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _haveOtherRole()
    {
        $have = 0;

        $roleModel = D('Role');
        $userRoleModel = D('UserRole');

        $register_type = modC('REGISTER_TYPE', 'normal', 'Invite');
        $register_type = explode(',', $register_type);
        if (!in_array('invite', $register_type)) {//开启邀请注册
            $map['status'] = 1;
            $map['invite'] = 0;
            if ($roleModel->where($map)->count() > 1) {
                $have = 1;
            } else {
                $map_user['uid'] = is_login();
                $map_user['role_id'] = array('neq', get_login_role());
                $map_user['status'] = array('egt', 0);
                $role_ids = $userRoleModel->where($map_user)->field('role_id')->select();
                if ($role_ids) {
                    $role_ids = array_column($role_ids, 'role_id');
                    $map_can['status'] = 1;
                    $map_can['id'] = array('in', $role_ids);
                    if ($roleModel->where($map_can)->count()) {
                        $have = 1;
                    }
                }
            }
        } else {
            $map['status'] = 1;
            if ($roleModel->where($map)->count() > 1) {
                $have = 1;
            }
        }
        $this->assign('can_show_role', $have);
    }

    private function _setTab($name)
    {
        $this->assign('tab', $name);
    }

    public function password()
    {

        $this->_setTab('password');
        $this->display();
    }

    public function score()
    {

        $scoreModel = D('Ucenter/Score');
        $scores = $scoreModel->getTypeList();
        foreach ($scores as &$v) {
            $v['value'] = $scoreModel->getUserScore(is_login(), $v['id']);
        }
        unset($v);
        $this->assign('scores', $scores);


        $level = nl2br(modC('LEVEL', '
0:Lv1 实习
50:Lv2 试用
100:Lv3 转正
200:Lv4 助理
400:Lv5 经理
800:Lv6 董事
1600:Lv7 董事长
        ', 'UserConfig'));
        $this->assign('level', $level);

        $self = query_user(array('score', 'title'));

        $this->assign('self', $self);

        $action = D('Admin/Action')->getAction(array('status' => 1));
        $action_module = array();
        foreach ($action as &$v) {
            $v['rule_array'] = unserialize($v['rule']);
            foreach ($v['rule_array'] as &$o) {
                if (is_numeric($o['rule'])) {
                    $o['rule'] = $o['rule'] > 0 ? '+' . intval($o['rule']) : $o['rule'];
                }
                $o['score'] = D('Score')->getType(array('id' => $o['field']));
            }
            if ($v['rule_array'] != false) {
                $action_module[$v['module']]['action'][] = $v;
            }

        }
        unset($v);

        foreach ($action_module as $key => &$a) {
            if (empty($a['action'])) {
                unset($action_module[$key]);
            }
            $a['module'] = D('Common/Module')->getModule($key);
        }

        $this->assign('action_module', $action_module);
        $this->_assignSelf();
        $this->_setTab('score');
        $this->display();
    }

    public function other()
    {

        $this->_setTab('other');
        $this->display();
    }

    public function avatar()
    {

        $this->_setTab('avatar');
        $this->display();
    }

    public function role()
    {
        $userRoleModel = D('UserRole');
        if (IS_POST) {
            $aShowRole = I('post.show_role', 0, 'intval');
            $map['role_id'] = $aShowRole;
            $map['uid'] = is_login();
            $map['status'] = array('egt', 1);
            if (!$userRoleModel->where($map)->count()) {
                $this->error('参数错误！');
            }
            $result = D('Member')->where(array('uid' => is_login()))->setField('show_role', $aShowRole);
            if ($result) {
                clean_query_user_cache(is_login(), array('show_role'));
                $this->success('设置成功！');
            } else {
                $this->error('设置失败！');
            }
        } else {
            $role_id = get_login_role();//当前登录角色
            $roleModel = D('Role');
            $userRoleModel = D('UserRole');

            $already_role_list = $userRoleModel->where(array('uid' => is_login()))->field('role_id,status')->select();
            $already_role_ids = array_column($already_role_list, 'role_id');
            $already_role_list = array_combine($already_role_ids, $already_role_list);

            $map_already_roles['id'] = array('in', $already_role_ids);
            $map_already_roles['status'] = 1;
            $already_roles = $roleModel->where($map_already_roles)->order('sort asc')->select();
            $already_group_ids = array_unique(array_column($already_roles, 'group_id'));

            foreach ($already_roles as &$val) {
                $val['user_status'] = $already_role_list[$val['id']]['status'] != 2 ? ($already_role_list[$val['id']]['status'] == 1) ? '<span style="color: green;">已审核</span>' : '<span style="color: #ff0000;">已禁用<span style="color: 333">(如有疑问，请联系管理员)</span></span>' : '<span style="color: #0003FF;">正在审核</span>';;
                $val['can_login'] = $val['id'] == $role_id ? 0 : 1;
                $val['user_role_status'] = $already_role_list[$val['id']]['status'];
            }
            unset($val);

            $already_group_ids = array_diff($already_group_ids, array(0));//去除无分组角色组
            if (count($already_group_ids)) {
                $map_can_have_roles['group_id'] = array('not in', $already_group_ids);//同组内的角色不显示
            }
            $map_can_have_roles['id'] = array('not in', $already_role_ids);//去除已有角色
            $map_can_have_roles['invite'] = 0;//不需要邀请注册
            $map_can_have_roles['status'] = 1;
            $can_have_roles = $roleModel->where($map_can_have_roles)->order('sort asc')->select();//可持有角色

            $register_type = modC('REGISTER_TYPE', 'normal', 'Invite');
            $register_type = explode(',', $register_type);
            if (in_array('invite', $register_type)) {//开启邀请注册
                $map_can_have_roles['invite'] = 1;
                $can_up_roles = $roleModel->where($map_can_have_roles)->order('sort asc')->select();//可升级角色
                $this->assign('can_up_roles', $can_up_roles);
            }

            $show_role = query_user(array('show_role'));
            $this->assign('show_role', $show_role['show_role']);
            $this->assign('already_roles', $already_roles);
            $this->assign('can_have_roles', $can_have_roles);

            $this->_setTab('role');
            $this->display();
        }

    }

    public function tag()
    {
        $userTagLinkModel = D('Ucenter/UserTagLink');
        if (IS_POST) {
            $aTagIds = I('post.tag_ids', '', 'op_t');
            $result = $userTagLinkModel->editData($aTagIds);
            if ($result) {
                $res['status'] = 1;
            } else {
                $res['status'] = 0;
                $res['info'] = "操作失败！";
            }
            $this->ajaxReturn($res);
        } else {
            $userTagModel = D('Ucenter/UserTag');
            $map = getRoleConfigMap('user_tag', get_login_role());
            $ids = M('RoleConfig')->where($map)->getField('value');
            if ($ids) {
                $ids = explode(',', $ids);
                $tag_list = $userTagModel->getTreeListByIds($ids);
                $this->assign('tag_list', $tag_list);
            }
            $myTags = $userTagLinkModel->getUserTag(is_login());
            $this->assign('my_tag', $myTags);
            $my_tag_ids = array_column($myTags, 'id');
            $my_tag_ids = implode(',', $my_tag_ids);
            $this->assign('my_tag_ids', $my_tag_ids);
            $this->_setTab('tag');
            $this->display();
        }
    }

    public function index()
    {
        $aUid = I('get.uid', is_login(), 'intval');
        $aTab = I('get.tab', '', 'op_t');
        $aNickname = I('post.nickname', '', 'op_t');
        $aSex = I('post.sex', 0, 'intval');
        $aEmail = I('post.email', '', 'op_t');
        $aSignature = I('post.signature', '', 'op_t');
        $aCommunity = I('post.community', 0, 'intval');
        $aDistrict = I('post.district', 0, 'intval');
        $aCity = I('post.city', 0, 'intval');
        $aProvince = I('post.province', 0, 'intval');

        if (IS_POST) {
            $this->checkNickname($aNickname);
            $this->checkSex($aSex);
            $this->checkSignature($aSignature);


            $user['pos_province'] = $aProvince;
            $user['pos_city'] = $aCity;
            $user['pos_district'] = $aDistrict;
            $user['pos_community'] = $aCommunity;

            $user['nickname'] = $aNickname;
            $user['sex'] = $aSex;
            $user['signature'] = $aSignature;
            $user['uid'] = get_uid();

            $rs_member = D('Member')->save($user);

            $ucuser['id'] = get_uid();
            $rs_ucmember = UCenterMember()->save($ucuser);
            clean_query_user_cache(get_uid(), array('nickname', 'sex', 'signature', 'email', 'pos_province', 'pos_city', 'pos_district', 'pos_community'));

            //TODO tox 清空缓存

            S('weibo_at_who_users',null);

            if ($rs_member || $rs_ucmember) {
                $this->success('设置成功。');

            } else {
                $this->success('未修改数据。');
            }

        } else {
            //调用API获取基本信息
            //TODO tox 获取省市区数据
            $user = query_user(array('nickname', 'signature', 'email', 'mobile', 'avatar128', 'rank_link', 'sex', 'pos_province', 'pos_city', 'pos_district', 'pos_community'), $aUid);
            //显示页面
            $this->assign('user', $user);

            $this->accountInfo();

            $this->assign('tab', $aTab);
            $this->getExpandInfo();
            $this->_setTab('info');
            $this->display();
        }

    }

    /**验证用户名
     * @param $nickname
     * @auth 陈一枭
     */
    private function checkNickname($nickname)
    {
        $length = mb_strlen($nickname, 'utf8');
        if ($length == 0) {
            $this->error('请输入昵称。');
        } else if ($length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            $this->error('昵称不能超过'. modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').'个字。');
        } else if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG')) {
            $this->error('昵称不能少于'.modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'个字。');
        }
        $match = preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname);
        if (!$match) {
            $this->error('昵称只允许中文、字母、下划线和数字。');
        }

        $map_nickname['nickname'] = $nickname;
        $map_nickname['uid'] = array('neq', is_login());
        $had_nickname = D('Member')->where($map_nickname)->count();
        if ($had_nickname) {
            $this->error('昵称已被人使用。');
        }
    }


    /**验证签名
     * @param $signature
     * @auth 陈一枭
     */
    private function checkSignature($signature)
    {
        $length = mb_strlen($signature, 'utf8');
        if ($length >= 100) {
            $this->error('签名不能超过100个字');
        }
    }


    /**获取用户扩展信息
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getExpandInfo($uid = null)
    {
        $profile_group_list = $this->_profile_group_list($uid);
        if ($profile_group_list) {
            $info_list = $this->_info_list($profile_group_list[0]['id'], $uid);
            $this->assign('info_list', $info_list);
            $this->assign('profile_group_id', $profile_group_list[0]['id']);
            //dump($info_list);exit;
        }
        foreach ($profile_group_list as &$v) {
            $v['fields'] = $this->_getExpandInfo($v['id']);
        }

        $this->assign('profile_group_list', $profile_group_list);
    }


    /**显示某一扩展分组信息
     * @param null $profile_group_id
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _getExpandInfo($profile_group_id = null)
    {
        $res = D('field_group')->where(array('id' => $profile_group_id, 'status' => '1'))->find();
        if (!$res) {
            return array();
        }
        $info_list = $this->_info_list($profile_group_id);

        return $info_list;
        $this->assign('info_list', $info_list);
        $this->assign('profile_group_id', $profile_group_id);
        //dump($info_list);exit;
        $this->assign('profile_group_list', $profile_group_list);
    }

    /**修改用户扩展信息
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function edit_expandinfo($profile_group_id)
    {
        $field_list = $this->getRoleFieldIds();
        if ($field_list) {
            $map_field['id'] = array('in', $field_list);
        } else {
            $this->error('没有要保存的信息！');
        }
        $map_field['profile_group_id'] = $profile_group_id;
        $map_field['status'] = 1;
        $field_setting_list = D('field_setting')->where($map_field)->order('sort asc')->select();

        if (!$field_setting_list) {
            $this->error('没有要修改的信息！');
        }

        $data = null;
        foreach ($field_setting_list as $key => $val) {
            $data[$key]['uid'] = is_login();
            $data[$key]['field_id'] = $val['id'];
            switch ($val['form_type']) {
                case 'input':
                    $val['value'] = op_t($_POST['expand_' . $val['id']]);
                    if (!$val['value'] || $val['value'] == '') {
                        if ($val['required'] == 1) {
                            $this->error($val['field_name'] . '内容不能为空！');
                        }
                    } else {
                        $val['submit'] = $this->_checkInput($val);
                        if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                            $this->error($val['submit']['msg']);
                        }
                    }
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'radio':
                    $val['value'] = op_t($_POST['expand_' . $val['id']]);
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'checkbox':
                    $val['value'] = $_POST['expand_' . $val['id']];
                    if (!is_array($val['value']) && $val['required'] == 1) {
                        $this->error('请至少选择一个：' . $val['field_name']);
                    }
                    $data[$key]['field_data'] = is_array($val['value']) ? implode('|', $val['value']) : '';
                    break;
                case 'select':
                    $val['value'] = op_t($_POST['expand_' . $val['id']]);
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'time':
                    $val['value'] = op_t($_POST['expand_' . $val['id']]);
                    $val['value'] = strtotime($val['value']);
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'textarea':
                    $val['value'] = op_t($_POST['expand_' . $val['id']]);
                    if (!$val['value'] || $val['value'] == '') {
                        if ($val['required'] == 1) {
                            $this->error($val['field_name'] . '内容不能为空！');
                        }
                    } else {
                        $val['submit'] = $this->_checkInput($val);
                        if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                            $this->error($val['submit']['msg']);
                        }
                    }
                    $val['submit'] = $this->_checkInput($val);
                    if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                        $this->error($val['submit']['msg']);
                    }
                    $data[$key]['field_data'] = $val['value'];
                    break;
            }
        }
        $map['uid'] = is_login();
        $map['role_id'] = get_login_role();
        $is_success = false;
        foreach ($data as $dl) {
            $dl['role_id'] = $map['role_id'];

            $map['field_id'] = $dl['field_id'];
            $res = D('field')->where($map)->find();
            if (!$res) {
                if ($dl['field_data'] != '' && $dl['field_data'] != null) {
                    $dl['createTime'] = $dl['changeTime'] = time();
                    if (!D('field')->add($dl)) {
                        $this->error('信息添加时出错！');
                    }
                    $is_success = true;
                }
            } else {
                $dl['changeTime'] = time();
                if (!D('field')->where('id=' . $res['id'])->save($dl)) {
                    $this->error('信息修改时出错！');
                }
                $is_success = true;
            }
            unset($map['field_id']);
        }
        clean_query_user_cache(is_login(), 'expand_info');
        if ($is_success) {
            $this->success('保存成功！');
        } else {
            $this->error('没有要保存的信息！');
        }
    }

    /**input类型验证
     * @param $data
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    function _checkInput($data)
    {
        if ($data['form_type'] == "textarea") {
            $validation = $this->_getValidation($data['validation']);
            if (($validation['min'] != 0 && mb_strlen($data['value'], "utf-8") < $validation['min']) || ($validation['max'] != 0 && mb_strlen($data['value'], "utf-8") > $validation['max'])) {
                if ($validation['max'] == 0) {
                    $validation['max'] = '';
                }
                $info['succ'] = 0;
                $info['msg'] = $data['field_name'] . "长度必须在" . $validation['min'] . "-" . $validation['max'] . "之间";
            }
        } else {
            switch ($data['child_form_type']) {
                case 'string':
                    $validation = $this->_getValidation($data['validation']);
                    if (($validation['min'] != 0 && mb_strlen($data['value'], "utf-8") < $validation['min']) || ($validation['max'] != 0 && mb_strlen($data['value'], "utf-8") > $validation['max'])) {
                        if ($validation['max'] == 0) {
                            $validation['max'] = '';
                        }
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] . "长度必须在" . $validation['min'] . "-" . $validation['max'] . "之间";
                    }
                    break;
                case 'number':
                    if (preg_match("/^\d*$/", $data['value'])) {
                        $validation = $this->_getValidation($data['validation']);
                        if (($validation['min'] != 0 && mb_strlen($data['value'], "utf-8") < $validation['min']) || ($validation['max'] != 0 && mb_strlen($data['value'], "utf-8") > $validation['max'])) {
                            if ($validation['max'] == 0) {
                                $validation['max'] = '';
                            }
                            $info['succ'] = 0;
                            $info['msg'] = $data['field_name'] . "长度必须在" . $validation['min'] . "-" . $validation['max'] . "之间，且为数字";
                        }
                    } else {
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] . "必须是数字";
                    }
                    break;
                case 'email':
                    if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $data['value'])) {
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] . "格式不正确，必需为邮箱格式";
                    }
                    break;
                case 'phone':
                    if (!preg_match("/^\d{11}$/", $data['value'])) {
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] . "格式不正确，必须为手机号码格式";
                    }
                    break;
            }
        }
        return $info;
    }

    /**处理$validation
     * @param $validation
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    function _getValidation($validation)
    {
        $data['min'] = $data['max'] = 0;
        if ($validation != '') {
            $items = explode('&', $validation);
            foreach ($items as $val) {
                $item = explode('=', $val);
                if ($item[0] == 'min' && is_numeric($item[1]) && $item[1] > 0) {
                    $data['min'] = $item[1];
                }
                if ($item[0] == 'max' && is_numeric($item[1]) && $item[1] > 0) {
                    $data['max'] = $item[1];
                }
            }
        }
        return $data;
    }

    /**分组下的字段信息及相应内容
     * @param null $id 扩展分组id
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _info_list($id = null, $uid = null)
    {

        $fields_list = $this->getRoleFieldIds($uid);
        $info_list = null;

        if (isset($uid) && $uid != is_login()) {
            //查看别人的扩展信息
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'visiable' => '1', 'id' => array('in', $fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = $uid;
        } else if (is_login()) {
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'id' => array('in', $fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = is_login();

        } else {
            $this->error('请先登录！');
        }
        foreach ($field_setting_list as $val) {
            $map['field_id'] = $val['id'];
            $field = D('field')->where($map)->find();
            $val['field_content'] = $field;
            $info_list[$val['id']] = $val;
            unset($map['field_id']);
        }

        return $info_list;
    }

    private function getRoleFieldIds($uid = null)
    {
        $role_id = get_role_id($uid);
        $fields_list = S('Role_Expend_Info_' . $role_id);
        if (!$fields_list) {
            $map_role_config = getRoleConfigMap('expend_field', $role_id);
            $fields_list = D('RoleConfig')->where($map_role_config)->getField('value');
            if ($fields_list) {
                $fields_list = explode(',', $fields_list);
                S('Role_Expend_Info_' . $role_id, $fields_list, 600);
            }
        }
        return $fields_list;
    }


    /**扩展信息分组列表获取
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _profile_group_list($uid = null)
    {
        $profile_group_list = array();
        $fields_list = $this->getRoleFieldIds($uid);
        if ($fields_list) {
            $fields_group_ids = D('FieldSetting')->where(array('id' => array('in', $fields_list), 'status' => '1'))->field('profile_group_id')->select();
            if ($fields_group_ids) {
                $fields_group_ids = array_unique(array_column($fields_group_ids, 'profile_group_id'));
                $map['id'] = array('in', $fields_group_ids);

                if (isset($uid) && $uid != is_login()) {
                    $map['visiable'] = 1;
                }
                $map['status'] = 1;
                $profile_group_list = D('field_group')->where($map)->order('sort asc')->select();
            }
        }
        return $profile_group_list;
    }


    public function changeAvatar()
    {
        $this->defaultTabHash('change-avatar');
        $this->display();
    }


    private function iframeReturn($result)
    {
        $json = json_encode($result);
        $json = htmlspecialchars($json);
        $html = "<textarea data-type=\"application/json\">$json</textarea>";
        echo $html;
        exit;
    }


    public function doChangePassword($old_password, $new_password)
    {
        //调用接口
        $memberModel = UCenterMember();
        $res = $memberModel->changePassword($old_password, $new_password);
        if ($res) {
            $this->success('修改密码成功。', 'refresh');
        } else {
            $this->error($memberModel->getErrorMessage());
        }

    }

    /**
     * @param $sex
     * @return int
     * @auth 陈一枭
     */
    private function checkSex($sex)
    {

        if ($sex < 0 || $sex > 2) {
            $this->error('性别必须属于男、女、保密。');
            return $sex;
        }
        return $sex;
    }

    /**
     * @param $email
     * @param $email
     * @auth 陈一枭
     */
    private function checkEmail($email)
    {
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if (!preg_match($pattern, $email)) {
            $this->error('邮箱格式错误。');
        }

        $map['email'] = $email;
        $map['id'] = array('neq', get_uid());
        $had = UCenterMember()->where($map)->count();
        if ($had) {
            $this->error('该邮箱已被人使用。');
        }
    }

    /**
     * accountInfo   账户信息
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    private function accountInfo()
    {
        $info = UCenterMember()->field('id,username,email,mobile,type')->find(is_login());
        $this->assign('accountInfo', $info);
    }

    /**
     * saveUsername  修改用户名
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function saveUsername()
    {
        $aUsername = $cUsername = I('post.username', '', 'op_t');

        if (!check_reg_type('username')) {
            $this->error('用户名选项已关闭！');
        }


        //判断是否登录
        if (!is_login()) {
            $this->error('请登录后操作！');
        }
        //判断提交的用户名是否为空
        if (empty($aUsername)) {
            $this->error('用户名不能为空！');
        }
        check_username($cUsername, $cEmail, $cMobile);
        if (empty($cUsername)) {
            !empty($cEmail) && $str = '邮箱';
            !empty($cMobile) && $str = '手机';
            $this->error('用户名不能为' . $str);
        }

        //验证用户名是否是字母和数字
        preg_match("/^[a-zA-Z0-9_]{".modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').",".modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')."}$/", $aUsername, $match);
        if (!$match) {
            $this->error('用户名只允许英文字母和数字且长度必须在'.modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').'位之间！');
        }

        $uid = get_uid();
        $mUcenter = UCenterMember();
        //判断用户是否已设置用户名
        $username = $mUcenter->where(array('id' => $uid))->getField('username');
        if (empty($username)) {
            //判断修改的用户名是否已存在
            $id = $mUcenter->where(array('username' => $aUsername))->getField('id');
            if ($id) {
                $this->error('该用户名已经存在！');
            } else {
                //修改用户名
                $rs = $mUcenter->where(array('id' => $uid))->save(array('username' => $aUsername));
                if (!$rs) {
                    $this->error('设置失败！');
                }
                $this->success('设置成功！', 'refresh');
            }
        }
        $this->error('用户名已经确定不允许修改！');
    }

    /**
     * changeaccount  修改帐号信息
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function changeAccount()
    {
        $aTag = I('get.tag', '', 'op_t');
        $aTag = $aTag == 'mobile' ? 'mobile' : 'email';
        $this->assign('cName', $aTag == 'mobile' ? '手机' : '邮箱');
        $this->assign('type', $aTag);
        $this->display();

    }

    public function doSendVerify($account, $verify, $type)
    {
        switch ($type) {
            case 'mobile':
                $content = modC('SMS_CONTENT', '{$verify}', 'USERCONFIG');
                $content = str_replace('{$verify}', $verify, $content);
                $content = str_replace('{$account}', $account, $content);
                $res = sendSMS($account, $content);
                return $res;
                break;
            case 'email':
                //发送验证邮箱

                $content = modC('REG_EMAIL_VERIFY', '{$verify}', 'USERCONFIG');
                $content = str_replace('{$verify}', $verify, $content);
                $content = str_replace('{$account}', $account, $content);
                $res = send_mail($account, modC('WEB_SITE_NAME', 'OpenSNS开源社交系统', 'Config') . '邮箱验证', $content);

                return $res;
                break;
        }

    }

    /**
     * checkVerify  验证验证码
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function checkVerify()
    {

        $aAccount = I('account', '', 'op_t');
        $aType = I('type', '', 'op_t');
        $aVerify = I('verify', '', 'op_t');
        $aUid = I('uid', 0, 'intval');

        if (!is_login() || $aUid != is_login()) {
            $this->error('验证失败');
        }
        $aType = $aType == 'mobile' ? 'mobile' : 'email';
        $res = D('Verify')->checkVerify($aAccount, $aType, $aVerify, $aUid);
        if (!$res) {
            $this->error('验证失败');
        }
        UCenterMember()->where(array('id' => $aUid))->save(array($aType => $aAccount));
        $this->success('验证成功', U('ucenter/config/index'));

    }


    public function cleanRemember()
    {
        $uid = is_login();
        if ($uid) {
            D('user_token')->where('uid=' . $uid)->delete();
            $this->success('清除成功！');
        }
        $this->error('清除失败！');
    }

}