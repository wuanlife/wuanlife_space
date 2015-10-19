<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-6-9
 * Time: 上午8:59
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Ucenter\Widget;

use Think\Action;

/**
 * Class RegStepWidget  注册步骤
 * @package Usercenter\Widget
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
class RegStepWidget extends Action
{

    public $mStep = array(
        'change_avatar'=>'修改头像',
        'expand_info'=>'填写扩展资料',
        'set_tag'=>'选择个人标签',
    );

    public function  view()
    {
        $aStep = I('get.step','','op_t');
        //调用方法输出参数
        if(method_exists($this,$aStep)){
            $this->$aStep();
        }
        $this->display(T('Ucenter@Step/'.$aStep));
    }

    private function change_avatar()
    {
        $aUid = session('temp_login_uid');
        if(empty($aUid)){
            $this->error('参数错误');
        }
        $this->assign('user',query_user(array('avatar128')));
        $this->assign('uid',$aUid);
    }

    private function expand_info()
    {
        $aUid = session('temp_login_uid');
        if( empty($aUid)){
            $this->error('参数错误');
        }
        $this->getExpandInfo($aUid);
        $this->assign('uid',$aUid);
    }

    private function set_tag()
    {
        $userTagLinkModel=D('Ucenter/UserTagLink');
        $aUid = session('temp_login_uid');
        $aRole = session('temp_login_role_id');
        $userTagModel=D('Ucenter/UserTag');
        $map=getRoleConfigMap('user_tag',$aRole);
        $ids=M('RoleConfig')->where($map)->getField('value');
        if($ids){
            $ids=explode(',',$ids);
            $tag_list=$userTagModel->getTreeListByIds($ids);
            $this->assign('tag_list',$tag_list);
        }
        if(!count($tag_list)){
            redirect(U('Ucenter/member/step', array('step' => get_next_step('set_tag'))));
        }
        $myTags=$userTagLinkModel->getUserTag($aUid);
        $this->assign('my_tag',$myTags);
        $my_tag_ids=array_column($myTags,'id');
        $my_tag_ids=implode(',',$my_tag_ids);
        $this->assign('my_tag_ids',$my_tag_ids);
    }
    public function do_set_tag()
    {
        $userTagLinkModel=D('Ucenter/UserTagLink');
        $aTagIds=I('post.tag_ids','','op_t');
        $result=$userTagLinkModel->editData($aTagIds);
        if($result){
            $res['status']=1;
        }else{
            $res['status']=0;
            $res['info']="操作失败！";
        }
        return $res;
    }

    /**获取用户扩展信息
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function getExpandInfo($uid = null)
    {
        $profile_group_list = $this->_profile_group_list($uid);
        if(!count($profile_group_list)){
            redirect(U('Ucenter/member/step', array('step' => get_next_step('expand_info'))));
        }
        foreach ($profile_group_list as &$v) {
            $v['fields']=$this->_info_list($v['id']);;
        }
        $this->assign('profile_group_list', $profile_group_list);
    }

    /**扩展信息分组列表获取
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _profile_group_list($uid = null)
    {
        $profile_group_list=array();
        $fields_list=$this->getRoleFieldIds($uid);
        if($fields_list){
            $fields_group_ids=D('FieldSetting')->where(array('id'=>array('in',$fields_list), 'status' => '1'))->field('profile_group_id')->select();
            if(count($fields_group_ids)){
                $fields_group_ids=array_unique(array_column($fields_group_ids,'profile_group_id'));
                $map['id']=array('in',$fields_group_ids);
                $map['status'] = 1;
                $profile_group_list = D('field_group')->where($map)->order('sort asc')->select();
            }
        }
        return $profile_group_list;
    }

    private function getRoleFieldIds($uid=null){
        $role_id=session('temp_login_role_id')?session('temp_login_role_id'):get_role_id($uid);
        $fields_list=S('Role_Register_Expend_Info_'.$role_id);
        if(!$fields_list){
            $map_role_config=getRoleConfigMap('register_expend_field',$role_id);
            $fields_list=D('RoleConfig')->where($map_role_config)->getField('value');
            if($fields_list){
                $fields_list=explode(',',$fields_list);
                S('Role_Register_Expend_Info_'.$role_id,$fields_list,600);
            }
        }
        return $fields_list;
    }

    /**分组下的字段信息及相应内容
     * @param null $id 扩展分组id
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _info_list($id = null, $uid = null)
    {
        $fields_list=$this->getRoleFieldIds($uid);
        $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1','id'=>array('in',$fields_list)))->order('sort asc')->select();
        if (!$field_setting_list) {
            return null;
        }
        $field_setting_list=array_combine(array_column($field_setting_list,'id'),$field_setting_list);
        return $field_setting_list;
    }

    /**input类型验证
     * @param $data
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _checkInput($data)
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
    private function _getValidation($validation)
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
    /**修改用户扩展信息
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function edit_expandinfo()
    {
        $field_list=$this->getRoleFieldIds();
        if($field_list){
            $map_field['id']=array('in',$field_list);
        }else{
            $this->error('没有要保存的信息！');
        }
        $map_field['status']=1;
        $field_setting_list = D('field_setting')->where($map_field)->order('sort asc')->select();

        if (!$field_setting_list) {
            $this->error('没有要修改的信息！');
        }

        $data = null;
        foreach ($field_setting_list as $key => $val) {
            $data[$key]['uid'] = session('temp_login_uid')?session('temp_login_uid'):is_login();
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
        $map['uid'] = session('temp_login_uid')?session('temp_login_uid'):is_login();
        $map['role_id']=session('temp_login_role_id')?session('temp_login_role_id'):get_role_id($map['uid']);;
        $result['status'] = 1;
        foreach ($data as $dl) {
            $dl['role_id']=$map['role_id'];

            $map['field_id'] = $dl['field_id'];
            $res = D('field')->where($map)->find();
            if (!$res) {
                if ($dl['field_data'] != '' && $dl['field_data'] != null) {
                    $dl['createTime'] = $dl['changeTime'] = time();
                    if (!D('field')->add($dl)) {
                        $result['info']='信息添加时出错！';
                        $result['status']=0;
                    }
                }
            } else {
                $dl['changeTime'] = time();
                if (!D('field')->where('id=' . $res['id'])->save($dl)) {
                    $result['info']='信息修改时出错！';
                    $result['status']=0;
                }
            }
            unset($map['field_id']);
        }
        return $result;
    }

} 