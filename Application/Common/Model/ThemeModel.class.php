<?php
namespace Common\Model;

use Think\Model;

class ThemeModel extends Model
{
    public function setTheme($name)
    {
        if (D('Config')->where(array('name' => '_THEME_NOW_THEME'))->count()) {
            $res = D('Config')->where(array('name' => '_THEME_NOW_THEME'))->setField('value', $name);
        } else {
            $config['name'] = '_THEME_NOW_THEME';
            $config['type'] = 0;
            $config['title'] = '';
            $config['group'] = 0;
            $config['extra'] = '';
            $config['remark'] = '';
            $config['create_time'] = time();
            $config['update_time'] = time();
            $config['status'] = 1;
            $config['value'] = $name;
            $config['sort'] = 0;
            $res = D('Config')->add($config);
        }

        if ($res) {
            S('conf_THEME_NOW_THEME', $name);
            cookie('TO_LOOK_THEME', $name, array('prefix' => 'OSV2'));
            clean_cache(RUNTIME_PATH.'Cache/');//清除模板缓存
            return true;

        } else {
            $this->error = '写入数据库失败。';
            return false;
        }
    }

}

?>