<?php
/**
 * 邀请注册
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-23
 * Time: 下午2:52
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Admin\Controller;


use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

class InviteController extends AdminController
{
    protected $inviteModel;
    protected $inviteTypeModel;
    protected $inviteBuyLogModel;
    protected $inviteLogModel;
    protected $inviteUserInfoModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->inviteModel=D('Ucenter/Invite');
        $this->inviteTypeModel=D('Ucenter/InviteType');
        $this->inviteBuyLogModel=D('Ucenter/InviteBuyLog');
        $this->inviteLogModel=D('Ucenter/InviteLog');
        $this->inviteUserInfoModel=D('Ucenter/InviteUserInfo');
    }

    /**
     * 邀请注册基本配置
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function config()
    {
        $builder = new AdminConfigBuilder;
        $data = $builder->handleConfig();
        !isset($data['REGISTER_TYPE'])&&$data['REGISTER_TYPE']='normal';

        $register_options=array(
            'normal'=>'普通注册',
            'invite'=>'邀请注册'
        );
        $builder->title('邀请注册信息配置')
            ->keyCheckBox('REGISTER_TYPE', '注册类型', '勾选为开启',$register_options)
            ->data($data)
            ->buttonSubmit()
            ->buttonBack()
            ->display();
    }

    /**
     * 邀请码类型列表
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function index()
    {
        $data_list=$this->inviteTypeModel->getList();
        $builder=new AdminListBuilder();
        $builder->title('邀请码类型列表')
            ->buttonNew(U('Invite/edit'))
            ->button('删除',array('class' => 'btn ajax-post confirm', 'url' => U('Invite/setStatus', array('status' => -1)), 'target-form' => 'ids', 'confirm-info' => "确认删除身份？删除后不可恢复！"))
            ->keyId()->keyTitle()->keyText('length','邀请码长度')->keyText('time_show','有效时长')
            ->keyText('cycle_num','周期内可购买个数')->keyText('cycle_time_show','周期时长')
            ->keyText('roles_show','绑定身份')->keyText('auth_groups_show','允许购买的用户组')
            ->keyText('pay','每个额度消费')->keyText('income','每个成功后获得')
            ->keyBool('is_follow','成功后是否互相关注')->keyCreateTime()->keyUpdateTime()
            ->keyDoActionEdit('Invite/edit?id=###')
            ->data($data_list)
            ->display();
    }

    /**
     * 编辑邀请码类型
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function edit()
    {
        $aId=I('id',0,'intval');
        $is_edit=$aId?1:0;
        $title=$is_edit?"编辑":"新增";
        if(IS_POST){
            $data['title']=I('post.title','','op_t');
            $data['length']=I('post.length',0,'intval');
            $data['time_num']=I('post.time_num',0,'intval');
            $data['time_unit']=I('post.time_unit','second','op_t');
            $data['cycle_num']=I('post.cycle_num',0,'intval');
            $data['cycle_time_num']=I('post.cycle_time_num',0,'intval');
            $data['cycle_time_unit']=I('post.cycle_time_unit','second','op_t');
            $data['roles']=I('post.roles',array());
            $data['auth_groups']=I('post.auth_groups',array());
            $data['pay_score_type']=I('post.pay_score_type',1,'intval');
            $data['pay_score']=I('post.pay_score',0,'intval');
            $data['income_score_type']=I('post.income_score_type',1,'intval');
            $data['income_score']=I('post.income_score',0,'intval');
            $data['is_follow']=I('post.is_follow',0,'intval');
            if($is_edit){
                $data['id']=$aId;
                $result=$this->inviteTypeModel->saveData($data);
            }else{
                $result=$this->inviteTypeModel->addData($data);
            }
            if($result){
                $this->success($title.'邀请码类型成功！',U('Invite/index'));
            }else{
                $this->error($title.'邀请码类型失败！'.$this->inviteTypeModel->getError());
            }
        }else{
            if($is_edit){
                $map['id']=$aId;
                $data=$this->inviteTypeModel->getData($map);

                $data['time']=explode(' ',$data['time']);
                $data['time_num']=$data['time'][0];
                $data['time_unit']=$data['time'][1];

                $data['cycle_time']=explode(' ',$data['cycle_time']);
                $data['cycle_time_num']=$data['cycle_time'][0];
                $data['cycle_time_unit']=$data['cycle_time'][1];
            }

            $data['length']=$data['length']?$data['length']:11;

            $score_option=$this->_getMemberScoreType();
            $role_option=$this->_getRoleOption();
            $auth_group_option=$this->_getAuthGroupOption();
            $is_follow_option=array(
                0=>'否',
                1=>'是'
            );

            $builder=new AdminConfigBuilder();

            $builder->title($title.'邀请码类型');
            $builder->keyId()->keyTitle()->keyText('length','邀请码长度')
                ->keyMultiInput('time_num|time_unit','有效时长','时间+单位',array(array('type'=>'text','style'=>'width:295px;margin-right:5px'),array('type'=>'select','opt'=>get_time_unit(),'style'=>'width:100px')))
                ->keyInteger('cycle_num','周期内可购买个数')
                ->keyMultiInput('cycle_time_num|cycle_time_unit','周期时长','时间+单位',array(array('type'=>'text','style'=>'width:295px;margin-right:5px'),array('type'=>'select','opt'=>get_time_unit(),'style'=>'width:100px')))
                ->keyChosen('roles','绑定身份','',$role_option)
                ->keyChosen('auth_groups','允许购买的用户组','',$auth_group_option)
                ->keyMultiInput('pay_score_type|pay_score','每个邀请额度消费','积分类型+个数',array(array('type'=>'select','opt'=>$score_option,'style'=>'width:100px;margin-right:5px'),array('type'=>'text','style'=>'width:295px')))
                ->keyMultiInput('income_score_type|income_score','每个邀请成功后获得','积分类型+个数',array(array('type'=>'select','opt'=>$score_option,'style'=>'width:100px;margin-right:5px'),array('type'=>'text','style'=>'width:295px')))
                ->keyRadio('is_follow','成功后是否互相关注','',$is_follow_option)
                ->buttonSubmit()->buttonBack()
                ->data($data)
                ->display();
        }
    }

    /**
     * 真删除邀请码类型
     * @param mixed|string $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setStatus($ids,$status=-1)
    {
        $ids=is_array($ids)?$ids:explode(',',$ids);
        //删除邀请码类型，真删除
        if($status==-1){
            $this->inviteTypeModel->deleteIds($ids);
            $this->success('操作成功！');
        }else{
            $this->error('未知操作！');
        }

    }

    /**
     * 邀请码列表页
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function invite($page=1,$r=20)
    {
        $aBuyer=I('buyer',0,'intval');
        if($aBuyer==1){
            $map['uid']=array('gt',0);
        }else{
            $map['uid']=array('lt',0);
        }
        $aStatus=I('status',1,'intval');
        $status=$aStatus;
        if($aStatus==3){
            $status=1;
            $map['end_time']=array('lt',time());
        }else if($aStatus==1){
            $map['end_time']=array('egt',time());
        }
        $map['status']=$status;

        $aType=I('type',0,'intval');
        if($aType!=0){
            $map['invite_type']=$aType;
        }

        list($list,$totalCount)=$this->inviteModel->getList($map,$page,$r);
        $typeOptions=$this->_getTypeList();
        foreach($typeOptions as &$val){
            $val['value']=$val['title'];
        }
        unset($val);
        $typeOptions=array_merge(array(array('id'=>0,'value'=>'全部')),$typeOptions);
        if($aStatus==1){
            $this->assign('invite_list',$list);
            $this->assign('buyer',$aBuyer);
            $this->assign('type_list',$typeOptions);
            $this->assign('now_type',$aType);
            //生成翻页HTML代码
            C('VAR_PAGE', 'page');
            $pager = new \Think\Page($totalCount, $r, $_REQUEST);
            $pager->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $paginationHtml = $pager->show();
            $this->assign('pagination', $paginationHtml);
            $this->display();
        }else{
            $builder=new AdminListBuilder();
            $builder->title('邀请码列表页面')
                ->setSelectPostUrl(U('Invite/invite'))
                /*->buttonDelete(U('Invite/delete'))*/
                ->buttonModalPopup(U('Invite/createCode'),array(),'生成邀请码',array('data-title'=>'生成邀请码'))
                ->buttonDelete(U('Invite/deleteTrue'),'清空无用邀请码(真删除)')
                ->select('邀请码类型：','type','select','','','',$typeOptions)
                ->select('','status','select','','','',array(array('id'=>'1','value'=>'可注册'),array('id'=>'3','value'=>'已过期'),array('id'=>'2','value'=>'已退还'),array('id'=>'0','value'=>'用完无效'),array('id'=>'-1','value'=>'管理员删除')))
                ->select('','buyer','select','','','',array(array('id'=>'-1','value'=>'管理员生成'),array('id'=>'1','value'=>'用户购买')))
                ->keyId()
                ->keyText('code','邀请码')
                ->keyText('code_url','邀请码链接')
                ->keyText('invite','邀请码类型')
                ->keyText('buyer','购买者')
                ->keyText('can_num','可注册几个')
                ->keyText('already_num','已注册几个')
                ->keyTime('end_time','有效期至')
                ->keyCreateTime()
                ->data($list)
                ->pagination($totalCount,$r)
                ->display();
        }

    }

    /**
     * 生成邀请码
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function createCode()
    {
        if(IS_POST){
            $data['invite_type']=I('post.invite',0,'intval');
            $aCodeNum=I('post.code_num',0,'intval');
            $aCanNum=$data['can_num']=I('post.can_num',0,'intval');
            if($aCanNum<=0||$aCodeNum<=0){
                $result['status']=0;
                $result['info']='生成个数和可注册个数都不能小于1！';
            }else{
                $result=$this->inviteModel->createCodeAdmin($data,$aCodeNum);
            }
            $this->ajaxReturn($result);
        }else{
            $type_list=$this->_getTypeList();
            $this->assign('type_list',$type_list);
            $this->display('create');
        }
    }

    /**
     * 伪删除邀请码
     * @param string $ids
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function delete($ids)
    {
        $ids=is_array($ids)?$ids:explode(',',$ids);
        $result=$this->inviteModel->where(array('id'=>array('in',$ids)))->setField('status','-1');
        if($result){
            $this->success('操作成功！');
        }else{
            $this->error('操作失败！'.$this->inviteModel->getError());
        }
    }

    /**
     * 删除无用的邀请码
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function deleteTrue()
    {
        $map['status']=array('neq',1);
        $map['end_time']=array('lt',time());
        $map['_logic']='OR';
        $result=$this->inviteModel->where($map)->delete();
        if($result){
            $this->success('操作成功！');
        }else{
            $this->error('操作失败！'.$this->inviteModel->getError());
        }
    }

    /**
     * 用户兑换名额记录
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function buyLog($page=1,$r=20)
    {
        $aInviteType=I('invite_type',0,'intval');
        $aOrder=I('order',0,'intval');
        if($aInviteType){
            $map['invite_type']=$aInviteType;
        }
        if($aOrder==0){
            $order='create_time desc';
        }elseif($aOrder==1){
            $order='create_time asc';
        }elseif($aOrder==2){
            $order='uid asc,invite_type asc,create_time desc';
        }
        list($list,$totalCount)=$this->inviteBuyLogModel->getList($map,$page,$order,$r);
        $orderOptions=array(
            array('id'=>0,'value'=>'最新创建'),
            array('id'=>1,'value'=>'最早创建'),
            array('id'=>2,'value'=>'用户')
        );
        $typeOptions=$this->_getTypeList();
        foreach($typeOptions as &$val){
            $val['value']=$val['title'];
        }
        unset($val);
        $typeOptions=array_merge(array(array('id'=>0,'value'=>'全部')),$typeOptions);

        $builder=new AdminListBuilder();
        $builder->title('用户兑换名额记录')
            ->setSelectPostUrl(U('Invite/buyLog'))
            ->select('邀请码类型：','invite_type','select','','','',$typeOptions)
            ->select('排序方式：','order','select','','','',$orderOptions)
            ->keyId()
            ->keyText('user','购买者')
            ->keyText('invite_type_title','邀请码类型')
            ->keyText('num','兑换数量（名额）')
            ->keyText('content','信息')
            ->keyCreateTime()
            ->pagination($totalCount,$r)
            ->data($list)
            ->display();
    }

    /**
     * 用户邀请信息列表
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function userInfo($page=1,$r=20)
    {
        $aInviteType=I('invite_type',0,'intval');
        if($aInviteType){
            $map['invite_type']=$aInviteType;
        }
        list($list,,$totalCount)=$this->inviteUserInfoModel->getList($map,$page,$r);

        $typeOptions=$this->_getTypeList();
        foreach($typeOptions as &$val){
            $val['value']=$val['title'];
        }
        unset($val);
        $typeOptions=array_merge(array(array('id'=>0,'value'=>'全部')),$typeOptions);

        $builder=new AdminListBuilder();
        $builder->title('用户信息')
            ->setSelectPostUrl(U('Invite/userInfo'))
            ->select('邀请码类型：','invite_type','select','','','',$typeOptions)
            ->keyId()
            ->keyText('user','用户')
            ->keyText('invite_type_title','邀请码类型')
            ->keyText('num','可邀请名额')
            ->keyText('already_num','已经邀请名额')
            ->keyText('success_num','成功邀请名额')
            ->keyDoActionEdit('Invite/editUserInfo?id=###')
            ->pagination($totalCount,$r)
            ->data($list)
            ->display();
    }

    /**
     * 编辑用户邀请信息
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function editUserInfo()
    {
        $aId=I('id',0,'intval');
        if($aId<=0){
            $this->error('参数错误！');
        }
        if(IS_POST){
            $data['num']=I('num',0,'intval');
            $data['already_num']=I('already_num',0,'intval');
            $data['success_num']=I('success_num',0,'intval');
            if($data['num']<0||$data['already_num']<0||$data['success_num']<0){
                $this->error('请填入正确数据！');
            }
            $result=$this->inviteUserInfoModel->saveData($data,$aId);
            if($result){
                $this->success('编辑成功！',U('Admin/Invite/userInfo'));
            }else{
                $this->error('编辑失败！');
            }
        }else{
            $map['id']=$aId;
            $data=$this->inviteUserInfoModel->getInfo($map);

            $builder=new AdminConfigBuilder();
            $builder->title('编辑用户邀请信息')
                ->keyId()
                ->keyReadOnly('uid','用户id')
                ->keyReadOnly('invite_type','邀请码类型id')
                ->keyInteger('num','可邀请名额')
                ->keyInteger('already_num','已邀请名额')
                ->keyInteger('success_num','成功邀请名额')
                ->data($data)
                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }

    /**
     * 邀请日志
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function inviteLog($page=1,$r=20)
    {
        list($list,,$totalCount)=$this->inviteLogModel->getList($page,$r);
        $builder=new AdminListBuilder();
        $builder->title('邀请注册记录')
            ->keyId()
            ->keyText('user','注册者')
            ->keyText('inviter','邀请者')
            ->keyText('invite_type_title','邀请码类型')
            ->keyText('content','信息')
            ->keyCreateTime('create_time','注册时间')
            ->pagination($totalCount,$r)
            ->data($list)
            ->display();
    }

    /**
     * 导出cvs
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function cvs()
    {
        $aIds=I('ids',array());

        if(count($aIds)){
            $map['id']=array('in',$aIds);
        }else{
            $map['status']=array('in',array(1,0,-1));
            $dataListBack=$this->inviteModel->getListAll(array('status'=>2));
        }
        $dataList=$this->inviteModel->getListAll($map,'status desc,end_time desc');
        if(!count($dataList)&&!count($dataListBack)){
            $this->error('没有数据！');
        }
        if(count($dataListBack)){
            if(count($dataList)){
                $dataList=array_merge($dataList,$dataListBack);
            }else{
                $dataList=$dataListBack;
            }
        }
        $data="id,邀请码类型,邀请码,邀请链接,购买者,可注册数,已经注册数,有效期,状态,生成时间\n";
        foreach ($dataList as $val) {
            if($val['status']==-1){
                $val['status']="管理员删除";
            }elseif($val['status']==0){
                $val['status']="用完无效";
            }elseif($val['status']==1){
                if($val['end_time']<=time()){
                    $val['status']="已过期";
                }else{
                    $val['status']="可注册";
                }
            }elseif($val['status']==2){
                $val['status']="已退还";
            }
            $val['end_time']=time_format($val['end_time']);
            $val['create_time']=time_format($val['create_time']);
            $data.=$val['id'].",[".$val['invite_type']."]".$val['invite'].",".$val['code'].",".$val['code_url'].",[".$val['uid']."]".$val['buyer'].",".$val['can_num'].",".$val['already_num'].",".$val['end_time'].",".$val['status'].",".$val['create_time']."\n";
        }
        $data=iconv('utf-8','gb2312',$data);
        $filename = date('Ymd').'.csv'; //设置文件名
        $this->export_csv($filename,$data); //导出
    }

    private function export_csv($filename,$data) {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        echo $data;
    }

    //私有函数 start

    /**
     * 获取身份列表
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _getRoleOption()
    {
        $role_option=D('Role')->where(array('status'=>1))->order('sort asc')->field('id,title')->select();
        return $role_option;
    }

    /**
     * 获取权限用户组列表
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _getAuthGroupOption()
    {
        $role_option=D('AuthGroup')->where(array('status'=>1))->field('id,title')->select();
        return $role_option;
    }

    /**
     * 获取积分类型列表
     * @return array
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _getMemberScoreType()
    {
        $score_option=D('UcenterScoreType')->where(array('status'=>1))->field('id,title')->select();
        $score_option=array_combine(array_column($score_option,'id'),array_column($score_option,'title'));
        return $score_option;
    }

    private function _getTypeList(){
        $map['status']=1;
        $type_list=$this->inviteTypeModel->getSimpleList($map);
        return $type_list;
    }

    //私有函数 end
} 