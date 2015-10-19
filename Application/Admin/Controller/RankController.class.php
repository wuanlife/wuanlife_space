<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-7
 * Time: 下午1:25
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Admin\Controller;

use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminConfigBuilder;

/**
 * 后台头衔控制器
 * Class RankController
 * @package Admin\Controller
 * @郑钟良
 */
class RankController extends AdminController
{

    /**
     * 头衔管理首页
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function index($page = 1, $r = 20)
    {
        //读取数据
        $model = D('Rank');
        $list = $model->page($page, $r)->select();
        foreach ($list as &$val) {
            $val['u_name'] = D('member')->where('uid=' . $val['uid'])->getField('nickname');
            $val['types'] = $val['types'] ? '是' : '否';
            $val['label']='<span class="label" style="border-radius: 20px;background-color:'.$val['label_bg'].';color:'.$val['label_color'].';">'.$val['label_content'].'</span>';
            if($val['logo']==0){
                $val['logo']='';
            }
        }
        $totalCount = $model->count();
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('头衔列表')
            ->buttonNew(U('Rank/editRank'))
            ->keyId()->keyTitle()->keyText('u_name', '上传者')->keyImage('logo','图片头衔')->keyHtml('label','文字头衔')->keyCreateTime()->keyLink('types', '前台是否可申请', 'changeTypes?id=###')->keyDoActionEdit('editRank?id=###')->keyDoAction('deleteRank?id=###', '删除')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置头衔前台是否可申请
     * @param null $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function changeTypes($id = null)
    {
        if (!$id) {
            $this->error('请选择头衔');
        }
        $types = D('rank')->where('id=' . $id)->getField('types');
        $types = $types ? 0 : 1;
        $result = D('rank')->where('id=' . $id)->setField('types', $types);
        if ($result) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 删除头衔
     * @param null $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function deleteRank($id = null)
    {
        if (!$id) {
            $this->error('请选择头衔');
        }
        $result = D('rank')->where('id=' . $id)->delete();
        $result1 = D('rank_user')->where('rank_id=' . $id)->delete();
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 编辑头衔
     * @param null $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function editRank($id = null)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;
        if (IS_POST) {
            $data['title']=I('post.title','','text');
            $data['logo']=I('post.logo',0,'intval');
            $data['label_content']=I('post.label_content','','text');
            $data['label_color']=I('post.label_color','','text');
            $data['label_bg']=I('post.label_bg','','text');
            $data['types'] = I('post.types',1,'intval');
            $model = D('rank');
            if ($data['title'] == '') {
                $this->error('请填写标题');
            }

            if($data['logo']==''&&$data['label_content']==''){
                $this->error('图片头衔和文字头衔至少填一个！');
            }
            if ($isEdit) {
                $result = $model->where('id=' . $id)->save($data);
                if (!$result) {
                    $this->error('修改失败');
                }
            } else {
                $data = $model->create($data);
                $data['uid'] = is_login();
                $data['create_time'] = time();
                $result = $model->add($data);
                if (!$result) {
                    $this->error('创建失败');
                }
            }
            $this->success($isEdit ? '编辑成功' : '添加成功', U('Rank/index'));
        } else {
            $rank['types'] = '1';//默认前台可以申请
            //如果是编辑模式
            if ($isEdit) {
                $rank = M('rank')->where(array('id' => $id))->find();
            }
            //显示页面
            $builder = new AdminConfigBuilder();
            $options = array(
                '0' => '否',
                '1' => '是'
            );
            $builder
                ->title($isEdit ? '编辑头衔' : '新增头衔')
                ->keyId()
                ->keyTitle()
                ->keySingleImage('logo', '图片头衔', '图标，不设置文字头衔时，该设置有用')
                ->keyText('label_content','文字头衔')
                ->keyColor('label_color','文字头衔颜色')
                ->keyColor('label_bg','文字头衔标签背景颜色')
                ->keyRadio('types', '前台是否可申请', null, $options)
                ->data($rank)
                ->buttonSubmit(U('editRank'))->buttonBack()
                ->display();
        }
    }

    /**
     * 用户列表
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function userList()
    {
        $nickname = I('nickname','','text');
        $map['status'] = array('egt', 0);
        if (is_numeric($nickname)) {
            $map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
        } else {
            if ($nickname !== '')
                $map['nickname'] = array('like', '%' . (string)$nickname . '%');
        }
        $list = $this->lists('Member', $map);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->meta_title = '用户列表';
        $this->display();
    }

    /**
     * 用户头衔列表
     * @param null $id
     * @param int $page
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function userRankList($id = null, $page = 1)
    {
        if (!$id) {
            $this->error('请选择用户');
        }
        $u_name = D('member')->where('uid=' . $id)->getField('nickname');
        $model = D('rank_user');
        $rankList = $model->where(array('uid' => $id, 'status' => 1))->page($page, 20)->order('create_time asc')->select();
        $totalCount = $model->where(array('uid' => $id, 'status' => 1))->count();
        foreach ($rankList as &$val) {
            $val['title'] = D('rank')->where('id=' . $val['rank_id'])->getField('title');
            $val['is_show'] = $val['is_show'] ? '显示' : '不显示';
        }
        $builder = new AdminListBuilder();
        $builder
            ->title($u_name . '的头衔列表')
            ->buttonNew(U('Rank/userAddRank?id=' . $id), '关联新头衔')
            ->keyId()->keyText('title', '头衔名称')->keyText('reason', '颁发理由')->keyText('is_show', '是否显示在昵称右侧')->keyCreateTime()->keyDoActionEdit('Rank/userChangeRank?id=###')->keyDoAction('Rank/deleteUserRank?id=###', '删除')
            ->data($rankList)
            ->pagination($totalCount, 20)
            ->display();
    }

    /**
     * 新增用户头衔关联
     * @param null $id
     * @param string $uid
     * @param string $reason
     * @param string $is_show
     * @param string $rank_id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function userAddRank($id = null, $uid = '', $reason = '', $is_show = '', $rank_id = '')
    {
        if (IS_POST) {
            $is_Edit = $id ? true : false;
            $data = array('uid' => $uid, 'reason' => $reason, 'is_show' => $is_show, 'rank_id' => $rank_id);
            $model = D('rank_user');
            if ($is_Edit) {
                $data = $model->create($data);
                $data['create_time'] = time();
                $result = $model->where('id=' . $id)->save($data);
                if (!$result) {
                    $this->error('关联失败');
                }
            } else {
                $rank_user = $model->where(array('uid' => $uid, 'rank_id' => $rank_id))->find();
                if ($rank_user) {
                    $this->error('该用户已经拥有该头衔，请选着其他头衔');
                }
                $data = $model->create($data);
                $data['create_time'] = time();
                $data['status'] = 1;
                $result = $model->add($data);
                if (!$result) {
                    $this->error('关联失败');
                } else {
                    $rank = D('rank')->where('id=' . $data['rank_id'])->find();
                    //$logoUrl=getRootUrl().D('picture')->where('id='.$rank['logo'])->getField('path');
                    //$u_name = D('member')->where('uid=' . $uid)->getField('nickname');
                    $content = '管理员给你颁发了头衔：[' . $rank['title'] . ']'; //<img src="'.$logoUrl.'" title="'.$rank['title'].'" alt="'.$rank['title'].'">';

                    $user = query_user(array('username', 'space_link'), $uid);

                    $content1 = '管理员给@' . $user['username'] . ' 颁发了新的头衔：[' . $rank['title'] . ']，颁发理由：' . $reason; //<img src="'.$logoUrl.'" title="'.$rank['title'].'" alt="'.$rank['title'].'">';
                    clean_query_user_cache($uid, array('rank_link'));
                    $this->sendMessage($data, $content);
                    if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                        //写入数据库
                        $model = D('Weibo/Weibo');
                        $result = $model->addWeibo(is_login(), $content1);
                    }
                }
            }
            $this->success($is_Edit ? '编辑关联成功' : '添加关联成功', U('Rank/userRankList?id=' . $uid));
        } else {
            if (!$id) {
                $this->error('请选择用户');
            }
            $data['uid'] = $id;
            $ranks = D('rank')->select();
            if (!$ranks) {
                $this->error('还没有头衔，请先添加头衔');
            }
            foreach ($ranks as $val) {
                $rank_ids[$val['id']] = $val['title'];
            }
            $data['rank_id'] = $ranks[0]['id'];
            $data['is_show'] = 1;
            $builder = new AdminConfigBuilder();
            $builder
                ->title('添加头衔关联')
                ->keyId()->keyReadOnly('uid', '用户ID')->keyText('reason', '关联理由')->keyRadio('is_show', '是否显示在昵称右侧', null, array(1 => '是', 0 => '否'))->keySelect('rank_id', '头衔编号', null, $rank_ids)
                ->data($data)
                ->buttonSubmit(U('userAddRank'))->buttonBack()
                ->display();
        }
    }

    /**
     * 编辑用户头衔关联
     * @param null $id
     * @param string $uid
     * @param string $reason
     * @param string $is_show
     * @param string $rank_id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function userChangeRank($id = null, $uid = '', $reason = '', $is_show = '', $rank_id = '')
    {
        if (IS_POST) {
            $is_Edit = $id ? true : false;
            $data = array('uid' => $uid, 'reason' => $reason, 'is_show' => $is_show, 'rank_id' => $rank_id);
            $model = D('rank_user');
            if ($is_Edit) {
                $data = $model->create($data);
                $data['create_time'] = time();
                $result = $model->where('id=' . $id)->save($data);
                if (!$result) {
                    $this->error('关联失败');
                }
            } else {
                $rank_user = $model->where(array('uid' => $uid, 'rank_id' => $rank_id))->find();
                if ($rank_user) {
                    $this->error('该用户已经拥有该头衔，请选着其他头衔');
                }
                $data = $model->create($data);
                $data['create_time'] = time();
                $result = $model->add($data);
                if (!$result) {
                    $this->error('关联失败');
                } else {
                    $rank = D('rank')->where('id=' . $data['rank_id'])->find();
                    //$logoUrl=getRootUrl().D('picture')->where('id='.$rank['logo'])->getField('path');
                    //$u_name = D('member')->where('uid=' . $uid)->getField('nickname');
                    $content = '管理员给你颁发了头衔：[' . $rank['title'] . ']'; //<img src="'.$logoUrl.'" title="'.$rank['title'].'" alt="'.$rank['title'].'">';

                    $user = query_user(array('username', 'space_link'), $uid);

                    $content1 = '管理员给@' . $user['username'] . ' 颁发了新的头衔：[' . $rank['title'] . ']，颁发理由：' . $reason; //<img src="'.$logoUrl.'" title="'.$rank['title'].'" alt="'.$rank['title'].'">';
                    clean_query_user_cache($uid, array('rank_link'));
                    $this->sendMessage($data, $content);
                    if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                        //写入数据库
                        $model = D('Weibo/Weibo');
                        $result = $model->addWeibo(is_login(), $content1);
                    }
                }
            }
            $this->success($is_Edit ? '编辑关联成功' : '添加关联成功', U('Rank/userRankList?id=' . $uid));
        } else {
            if (!$id) {
                $this->error('请选择要修改的头衔关联');
            }
            $data = D('rank_user')->where('id=' . $id)->find();
            if (!$data) {
                $this->error('该头衔关联不存在');
            }
            $ranks = D('rank')->select();
            if (!$ranks) {
                $this->error('还没有头衔，请先添加头衔');
            }
            foreach ($ranks as $val) {
                $rank_ids[$val['id']] = $val['title'];
            }
            $builder = new AdminConfigBuilder();
            $builder
                ->title('编辑头衔关联')
                ->keyId()->keyReadOnly('uid', '用户ID')->keyText('reason', '关联理由')->keyRadio('is_show', '是否显示在昵称右侧', null, array(1 => '是', 0 => '否'))->keySelect('rank_id', '头衔编号', null, $rank_ids)
                ->data($data)
                ->buttonSubmit(U('userChangeRank'))->buttonBack()
                ->display();
        }
    }

    /**
     * 删除用户头衔管理
     * @param null $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function deleteUserRank($id = null)
    {
        if (!$id) {
            $this->error('请选择头衔关联');
        }
        $result = D('rank_user')->where('id=' . $id)->delete();
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function sendMessage($data, $content, $type = '头衔颁发')
    {
        D('Message')->sendMessage($data['uid'], $type, $content, 'Ucenter/Message/message',array(),is_login(), 1);
    }

    /**
     * 待审核
     * @param int $page
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function rankVerify($page = 1)
    {
        $model = D('rankUser');
        $rankList = $model->where(array('status' => 0))->page($page, 20)->order('create_time asc')->select();
        $totalCount = $model->where(array('status' => 0))->count();
        foreach ($rankList as &$val) {
            $val['title'] = D('rank')->where('id=' . $val['rank_id'])->getField('title');
            $val['is_show'] = $val['is_show'] ? '显示' : '不显示';
            //获取用户信息
            $u_user = D('member')->where('uid=' . $val['uid'])->getField('nickname');
            $val['u_name'] = $u_user;
        }
        unset($val);
        $builder = new AdminListBuilder();
        $builder
            ->title('待审核的头衔列表')
            ->buttonSetStatus(U('setVerifyStatus'), '1', '审核通过', null)->buttonDelete(U('setVerifyStatus'), '审核不通过')
            ->keyId()->keyText('uid', '用户ID')->keyText('u_name', '用户昵称')->keyText('title', '头衔名称')->keyText('reason', '申请理由')->keyText('is_show', '是否显示在昵称右侧')->keyCreateTime()->keyDoActionEdit('Rank/userChangeRank?id=###')
            ->data($rankList)
            ->pagination($totalCount, 20)
            ->display();
    }

    /**
     * 审核不通过
     * @param int $page
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function rankVerifyFailure($page = 1)
    {
        $model = D('rankUser');
        $rankList = $model->where(array('status' => -1))->page($page, 20)->order('create_time asc')->select();
        $totalCount = $model->where(array('status' => -1))->count();
        foreach ($rankList as &$val) {
            $val['title'] = D('rank')->where('id=' . $val['rank_id'])->getField('title');
            $val['is_show'] = $val['is_show'] ? '显示' : '不显示';
            //获取用户信息
            $u_user = D('member')->where('uid=' . $val['uid'])->getField('nickname');
            $val['u_name'] = $u_user;
        }
        unset($val);
        $builder = new AdminListBuilder();
        $builder
            ->title('被驳回的头衔申请列表')
            ->buttonSetStatus(U('setVerifyStatus'), '1', '审核通过', null)
            ->keyId()->keyText('uid', '用户ID')->keyText('u_name', '用户昵称')->keyText('title', '头衔名称')->keyText('reason', '申请理由')->keyText('is_show', '是否显示在昵称右侧')->keyCreateTime()->keyDoActionEdit('Rank/userChangeRank?id=###')
            ->data($rankList)
            ->pagination($totalCount, 20)
            ->display();
    }

    public function setVerifyStatus($ids, $status)
    {

        $model_user = D('rankUser');
        $model = D('rank');
        if ($status == 1) {
            foreach ($ids as $val) {
                $rank_user = $model_user->where('id=' . $val)->field('uid,rank_id,reason')->find();
                $rank = $model->where('id=' . $rank_user['rank_id'])->find();
                $content = '管理员通过了你的头衔申请：[' . $rank['title'] . ']';

                $user = query_user(array('nickname', 'space_link'), $rank_user['uid']);

                $content1 = '管理员通过了@' . $user['nickname'] . ' 的头衔申请：[' . $rank['title'] . ']，申请理由：' . $rank_user['reason'];
                clean_query_user_cache($rank_user['uid'], array('rank_link'));
                $this->sendMessage($rank_user, $content, '头衔申请审核通过');
                if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                    //发微博
                    $model_weibo = D('Weibo/Weibo');
                    $result = $model_weibo->addWeibo(is_login(), $content1);
                }
            }
        } else if ($status = -1) {
            foreach ($ids as $val) {
                $rank_user = $model_user->where('id=' . $val)->field('uid,rank_id')->find();
                $rank = $model->where('id=' . $rank_user['rank_id'])->find();
                $content = '管理员驳回了你的头衔申请：[' . $rank['title'] . ']';
                $this->sendMessage($rank_user, $content, '头衔申请审核不通过');
            }
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('rankUser', $ids, $status);
    }
}
