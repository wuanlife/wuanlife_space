<?php


namespace Mob\Widget;

use Think\Action;


class MessageWidget extends Action
{
    public function index()
    {

        $map['to_uid'] = is_login();
        if(is_login()){
            $map['is_read']='0';
            $messages_count = D('Message')->where($map)->count();
        }

        $this->assign('messages_count',$messages_count);
        $this->display(T('Mob@Message/message'));

    }

}