<?php

return array(
    'action'=>array(
        'title'=>'签到绑定行为：',
        'type'=>'select',
        'options'=>get_option(),
    )

);



function get_option(){
    $opt = D('Action')->getActionOpt();
    $return = array(0=>'不绑定');
    foreach($opt as $v){
        $return[$v['name']] = $v['title'];
    }
    return $return;
}