<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;

/**
 * 后台配置控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class ConfigController extends AdminController
{

    /**
     * 配置管理
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {
        /* 查询条件初始化 */
        $map = array();
        $map = array('status' => 1, 'title' => array('neq', ''));
        if (isset($_GET['group'])) {
            $map['group'] = I('group', 0);
        }
        if (isset($_GET['name'])) {
            $map['name'] = array('like', '%' . (string)I('name') . '%');
        }
        //   $map=
        $list = $this->lists('Config', $map, 'sort,id');
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('group', C('CONFIG_GROUP_LIST'));
        $this->assign('group_id', I('get.group', 0));
        $this->assign('list', $list);
        $this->meta_title = '配置管理';
        $this->display();
    }

    /**
     * 新增配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add()
    {
        if (IS_POST) {
            $Config = D('Config');
            $data = $Config->create();
            if ($data) {
                if ($Config->add()) {
                    S('DB_CONFIG_DATA', null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info', null);
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Config = D('Config');
            $data = $Config->create();
            if ($data) {
                if ($Config->save()) {
                    S('DB_CONFIG_DATA', null);
                    //记录行为
                    action_log('update_config', 'config', $data['id'], UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);

            if (false === $info) {
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /**
     * 批量保存配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function save($config)
    {
        if ($config && is_array($config)) {
            $Config = M('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA', null);
        $this->success('保存成功！');
    }

    /**
     * 删除配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        if (M('Config')->where($map)->delete()) {
            S('DB_CONFIG_DATA', null);
            //记录行为
            action_log('update_config', 'config', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group()
    {
        $id = I('get.id', 1);
        $type = C('CONFIG_GROUP_LIST');
        $list = M("Config")->where(array('status' => 1, 'group' => $id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();

        if ($list) {
            $this->assign('list', $list);
        }
        $this->assign('id', $id);
        $this->meta_title = $type[$id] . '设置';
        $this->display();
    }

    /**
     * 配置排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort()
    {
        if (IS_GET) {
            $ids = I('get.ids');

            //获取排序的数据
            $map = array('status' => array('gt', -1), 'title' => array('neq', ''));
            if (!empty($ids)) {
                $map['id'] = array('in', $ids);
            } elseif (I('group')) {
                $map['group'] = I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '配置排序';
            $this->display();
        } elseif (IS_POST) {
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                $res = M('Config')->where(array('id' => $value))->setField('sort', $key + 1);
            }
            if ($res !== false) {
                $this->success('排序成功！', Cookie('__forward__'));
            } else {
                $this->eorror('排序失败！');
            }
        } else {
            $this->error('非法请求！');
        }
    }

    /**网站信息设置
     * @auth 陈一枭
     */
    public function website()
    {
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();
        $builder->title('网站信息')->suggest('此处配置网站的一般信息。');
        $builder->keyText('WEB_SITE_NAME', '网站名', '用于邮件,短信,站内信显示');
        $builder->keyText('ICP', '网站备案号', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2');
        $builder->keySingleImage('LOGO', '网站Logo', '网站的logo设置，建议尺寸156*50');
        $builder->keySingleImage('QRCODE', '微信二维码', '悬浮微信二维码');


        $builder->keySingleImage('JUMP_BACKGROUND', '跳转页背景图片', '跳转页背景图片');
        $builder->keyText('SUCCESS_WAIT_TIME', '成功等待时间', '设置成功时页面等待页面');
        $builder->keyText('ERROR_WAIT_TIME', '失败等待时间', '设置失败时页面等待页面');


        $builder->keyEditor('ABOUT_US', '关于我们内容', '页脚关于我们介绍');
        $builder->keyEditor('SUBSCRIB_US', '关注我们', '页脚关注我们内容');
        $builder->keyEditor('COPY_RIGHT', '版权信息', '页脚版权信息');

        $addons = \Think\Hook::get('uploadDriver');
        $opt = array('local' => '本地');
        foreach ($addons as $name) {
            if (class_exists($name)) {
                $class = new $name();
                $config = $class->getConfig();
                if ($config['switch']) {
                    $opt[$class->info['name']] = $class->info['title'];
                }

            }
        }

        $builder->keySelect('PICTURE_UPLOAD_DRIVER', '图片上传驱动', '图片上传驱动', $opt);
        $builder->keySelect('DOWNLOAD_UPLOAD_DRIVER', '附件上传驱动', '附件上传驱动', $opt);

        $builder->group('基本信息', array('WEB_SITE_NAME', 'ICP', 'LOGO', 'QRCODE'));

        $builder->group('页脚信息', array('ABOUT_US', 'SUBSCRIB_US', 'COPY_RIGHT'));

        $builder->group('跳转页面', array('JUMP_BACKGROUND', 'SUCCESS_WAIT_TIME', 'ERROR_WAIT_TIME'));

        $builder->group('上传配置', array('PICTURE_UPLOAD_DRIVER', 'DOWNLOAD_UPLOAD_DRIVER'));

        $builder->data($data);
        $builder->keyDefault('SUCCESS_WAIT_TIME', 2);
        $builder->keyDefault('ERROR_WAIT_TIME', 5);

        $builder->buttonSubmit();
        $builder->display();
    }
}
