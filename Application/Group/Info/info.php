<?php
return array(
    //模块名
    'name' => 'Group',
    //别名
    'alias' => '群组',
    //版本号
    'version' => '2.3.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '群组模块，允许用户建立自己的圈子',
    //开发者
    'developer' => '嘉兴想天信息科技有限公司',
    //开发者网站
    'website' => 'http://www.ourstu.com',
    //前台入口，可用U函数
    'entry' => 'Group/index/index',
    //后台入口
    'admin_entry' => 'Admin/Group/group',
    'icon' => 'flag',
    'can_uninstall' => 1
);