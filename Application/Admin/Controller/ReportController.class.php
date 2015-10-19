<?php

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

require_once(ONETHINK_ADDON_PATH . 'Report/Common/function.php');


class ReportController extends AdminController
{

    public function lists($page = 1, $r = 20){
        $map['status'] = array('egt', 0);
        $list = M('Report')->where($map)->page($page, $r)->select();
        //   $simplify_list = array_column($list, 'id');
        $reportCount = M('Report')->where($map)->count();

        int_to_string($list);

        $builder = new AdminListBuilder();
        $builder->title("举报处理列表");

        $builder->setStatusUrl(U('setStatus'))
            ->buttonModalPopup(U('handleEject'), '', "处理", $attr = array())
            ->buttonDisable('','忽略处理')
            ->buttonDelete(U('deleteReport'),'删除举报')
            ->keyId()
            ->keyLink('url',"举报链接",'{$url}')
            ->keyUid('uid',"举报用户的ID")
            ->keyText('reason',"举报原因")
            ->keyText('content',"举报原因")
            ->keyText('type',"举报类型")
            ->keyCreateTime('create_time',"创建时间")
            ->keyUpdateTime('update_time',"更新时间")
            ->keyUpdateTime('handle_time',"处理时间")
            ->keyDoActionModalPopup('handleEject?id=###','处理','操作',array('data-title'=>'迁移用户到其他身份'))
            ->keyDoActionEdit(' ','忽略处理')
            ->key('status', '状态', 'status',array('0'=>'忽略处理','1'=>'已处理','2'=>'正在处理'));

        $builder->data($list);
        $builder->pagination($reportCount, $r);
        $builder->display();
    }
    public function setStatus($ids,$status=1){
        $ids=I('ids',array());
        $status=$_GET['status'];
        $status!=1&&$status=0;
        D('Addons://Report/Report')->processingTime();
        $builder = new AdminListBuilder;
        $builder->doSetStatus('Report', $ids, $status);
    }

    public function deleteReport(){
        $ids=I('ids',array());
        $map['id']=array('in',$ids);
        $result = D('Addons://Report/Report')->where($map)->delete();
        if ($result) {
            $this->success('删除成功', 0);
        } else {
            $this->error('删除失败');
        }
    }

    public function handleEject(){
        $this->display();
    }


}