<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台频道控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class ChannelController extends AdminController
{

    /**
     * 频道列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {
        $Channel = D('Channel');
        if (IS_POST) {
            $one = $_POST['nav'][1];
            if (count($one) > 0) {
                M()->execute('TRUNCATE TABLE ' . C('DB_PREFIX') . 'channel');

                for ($i = 0; $i < count(reset($one)); $i++) {
                    $data[$i] = array(
                        'pid' => 0,
                        'title' => op_t($one['title'][$i]),
                        'url' => op_t($one['url'][$i]),
                        'sort' => intval($one['sort'][$i]),
                        'target' => intval($one['target'][$i]),
                        'color' => op_t($one['color'][$i]),
                        'band_text' => op_t($one['band_text'][$i]),
                        'band_color' => op_t($one['band_color'][$i]),
                        'icon' => op_t(str_replace('icon-', '', $one['icon'][$i])),
                        'status' => 1

                    );
                    $pid[$i] = $Channel->add($data[$i]);
                }
                $two = $_POST['nav'][2];

                for ($j = 0; $j < count(reset($two)); $j++) {
                    $data_two[$j] = array(
                        'pid' => $pid[$two['pid'][$j]],
                        'title' => op_t($two['title'][$j]),
                        'url' => op_t($two['url'][$j]),
                        'sort' => intval($two['sort'][$j]),
                        'target' => intval($two['target'][$j]),
                        'color' => op_t($two['color'][$j]),
                        'band_text' => op_t($two['band_text'][$j]),
                        'band_color' => op_t($two['band_color'][$j]),
                        'icon' => op_t(str_replace('icon-', '', $two['icon'][$j])),
                        'status' => 1
                    );
                    $res[$j] = $Channel->add($data_two[$j]);
                }
                S('common_nav',null);
                $this->success('修改成功');
            }
            $this->error('导航至少存在一个。');


        } else {
            /* 获取频道列表 */
            $map = array('status' => array('gt', -1), 'pid' => 0);
            $list = $Channel->where($map)->order('sort asc,id asc')->select();
            foreach ($list as $k => &$v) {
                $module = D('Module')->where(array('entry' => $v['url']))->find();
                $v['module_name'] = $module['name'];
                $child = $Channel->where(array('status' => array('gt', -1), 'pid' => $v['id']))->order('sort asc,id asc')->select();
                foreach ($child as $key => &$val) {
                    $module = D('Module')->where(array('entry' => $val['url']))->find();
                    $val['module_name'] = $module['name'];
                }
                unset($key, $val);
                $child && $v['child'] = $child;
            }

            unset($k, $v);
            $this->assign('module', $this->getModules());
            $this->assign('list', $list);

            $this->meta_title = '导航管理';
            $this->display();
        }

    }

    public function getModule()
    {
        $this->success($this->getModules());
    }


    /**
     * 频道列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index1()
    {
        $pid = i('get.pid', 0);
        /* 获取频道列表 */
        $map = array('status' => array('gt', -1), 'pid' => $pid);
        $list = M('Channel')->where($map)->order('sort asc,id asc')->select();

        $this->assign('list', $list);
        $this->assign('pid', $pid);
        $this->meta_title = '导航管理';
        $this->display();
    }


    /**
     * 添加频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add()
    {
        if (IS_POST) {
            $Channel = D('Channel');
            $data = $Channel->create();
            if ($data) {
                $id = $Channel->add();
                if ($id) {
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_channel', 'channel', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Channel->getError());
            }
        } else {
            $pid = I('get.pid', 0);
            //获取父导航
            if (!empty($pid)) {
                $parent = M('Channel')->where(array('id' => $pid))->field('title')->find();
                $this->assign('parent', $parent);
            }
            $pnav = D('Channel')->where(array('pid' => 0))->select();
            $this->assign('pnav', $pnav);
            $this->assign('pid', $pid);
            $this->assign('info', null);
            $this->meta_title = '新增导航';
            $this->display('edit');
        }
    }

    /**
     * 编辑频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Channel = D('Channel');
            $data = $Channel->create();
            if ($data) {
                if ($Channel->save()) {
                    //记录行为
                    action_log('update_channel', 'channel', $data['id'], UID);
                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Channel->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Channel')->find($id);

            if (false === $info) {
                $this->error('获取配置信息错误');
            }

            $pid = i('get.pid', 0);

            //获取父导航
            if (!empty($pid)) {
                $parent = M('Channel')->where(array('id' => $pid))->field('title')->find();
                $this->assign('parent', $parent);
            }
            $pnav = D('Channel')->where(array('pid' => 0))->select();
            $this->assign('pnav', $pnav);
            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑导航';
            $this->display();
        }
    }

    /**
     * 删除频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        if (M('Channel')->where($map)->delete()) {
            //记录行为
            action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 导航排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort()
    {
        if (IS_GET) {
            $ids = I('get.ids');
            $pid = I('get.pid');

            //获取排序的数据
            $map = array('status' => array('gt', -1));
            if (!empty($ids)) {
                $map['id'] = array('in', $ids);
            } else {
                if ($pid !== '') {
                    $map['pid'] = $pid;
                }
            }
            $list = M('Channel')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '导航排序';
            $this->display();
        } elseif (IS_POST) {
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                $res = M('Channel')->where(array('id' => $value))->setField('sort', $key + 1);
            }
            if ($res !== false) {
                $this->success('排序成功！');
            } else {
                $this->eorror('排序失败！');
            }
        } else {
            $this->error('非法请求！');
        }
    }
}
