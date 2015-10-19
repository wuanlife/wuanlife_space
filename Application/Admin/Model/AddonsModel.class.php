<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Think\Model;

/**
 * 插件模型
 * @author yangweijie <yangweijiester@gmail.com>
 */
class AddonsModel extends Model
{

    /**
     * 查找后置操作
     */
    protected function _after_find(&$result, $options)
    {

    }

    protected function _after_select(&$result, $options)
    {

        foreach ($result as &$record) {
            $this->_after_find($record, $options);
        }
    }

    public function install($name)
    {

        $class = get_addon_class($name);
        if (!class_exists($class)) {
            $this->error = '插件不存在';
            return false;
        }
        $addons = new $class;
        $info = $addons->info;
        if (!$info || !$addons->checkInfo())//检测信息的正确性
        {
            $this->error = '插件信息缺失';
            return false;
        }
        session('addons_install_error', null);
        $install_flag = $addons->install();
        if (!$install_flag) {
            $this->error = '执行插件预安装操作失败' . session('addons_install_error');
            return false;
        }
        $addonsModel = D('Addons');
        $data = $addonsModel->create($info);

        if ((is_array($addons->admin_list) && $addons->admin_list !== array()) || method_exists(A('Addons://Mail/Admin'), 'buildList')) {
            $data['has_adminlist'] = 1;
        } else {
            $data['has_adminlist'] = 0;
        }
        if (!$data) {
            $this->error = $addonsModel->getError();
            return false;
        }
        if ($addonsModel->add($data)) {
            $config = array('config' => json_encode($addons->getConfig()));
            $addonsModel->where("name='{$name}'")->save($config);
            $hooks_update = D('Hooks')->updateHooks($name);
            if ($hooks_update) {
                S('hooks', null);
                return true;
            } else {
                $addonsModel->where("name='{$name}'")->delete();
                $this->error = '更新钩子处插件失败,请卸载后尝试重新安装';
                return false;
            }

        } else {
            $this->error = '写入插件数据失败';
            return false;
        }
    }

    /**
     * 文件模型自动完成
     * @var array
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public function getList($addon_dir = '')
    {
        if (!$addon_dir)
            $addon_dir = ONETHINK_ADDON_PATH;
        $dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
        //TODO 新增模块插件的支持
        /* $modules=D('Module')->getAll();
         foreach($modules as $m){
             if($m['is_setup']){
                 $module_dir=APP_PATH.$m['name'].'/Addons/';
                 if(!file_exists($module_dir)){
                     continue;
                 }
                 $tmp_dirs = array_map('basename',glob($module_dir.'*', GLOB_ONLYDIR));
                 $dirs=array_merge($dirs,$tmp_dirs);
             }
         }*/


        if ($dirs === FALSE || !file_exists($addon_dir)) {
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        $addons = array();
        $where['name'] = array('in', $dirs);
        $list = $this->where($where)->field(true)->select();
        foreach ($list as $addon) {
            $addon['uninstall'] = 0;
            $addons[$addon['name']] = $addon;
        }
        foreach ($dirs as $value) {

            if (!isset($addons[$value])) {
                $class = get_addon_class($value);
                if (!class_exists($class)) { // 实例化插件失败忽略执行
                    \Think\Log::record('插件' . $value . '的入口文件不存在！');
                    continue;
                }
                $obj = new $class;
                $addons[$value] = $obj->info;
                if ($addons[$value]) {
                    $addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
                }
            }
        }
        //dump($list);exit;
        int_to_string($addons, array('status' => array(-1 => '损坏', 0 => '禁用', 1 => '启用', null => '未安装')));
        $addons = list_sort_by($addons, 'uninstall', 'desc');
        return $addons;
    }

    /**
     * 获取插件的后台列表
     */
    public function getAdminList()
    {
        $admin = array();
        $db_addons = $this->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if ($db_addons) {
            foreach ($db_addons as $value) {
                $admin[] = array('title' => $value['title'], 'url' => "Addons/adminList?name={$value['name']}");
            }
        }
        return $admin;
    }
}
