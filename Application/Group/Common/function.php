<?php
function is_joined($group_id)
{
    return D('Group/GroupMember')->getIsJoin(is_login(),$group_id);
}

function get_group_name($group_id){
    $group =  D('Group')->getGroup($group_id);
    return $group['title'];
}

function group_is_exist($group_id){
    $group =  D('Group')->getGroup($group_id);
    return $group ? true : false;
}

function post_is_exist($post_id){
    $post =  D('GroupPost')->getPost($post_id);
    return $post ? true : false;
}

function get_group_type($group_id){
    $group =  D('Group')->getGroup($group_id);
    return get_type_name($group['type_id']);

}

function get_type_name($type_id){
    $type =  D('GroupType')->getGroupType($type_id);
    return $type['title'];

}

function get_post_category($id){
    $cate =  D('GroupPostCategory')->getPostCategory($id);
    return $cate['title'];
}


function get_lou($k)
{
    $lou = array(
        2 => '沙发',
        3 => '板凳',
        4 => '地板'
    );
    !empty($lou[$k]) && $res = $lou[$k];
    empty($lou[$k]) && $res = $k . '楼';
    return $res;
}

function check_is_bookmark($post_id){
    return D('GroupBookmark')->exists(is_login(), $post_id);
}


function get_group_admin($group_id){
    return get_admin_ids($group_id,4,1);
}

function get_post_admin($post_id){
    return get_admin_ids($post_id,3,1);
}

function get_reply_admin($reply_id){
    return get_admin_ids($reply_id,2,1);
}


function get_lzl_admin($lzl_id){
    return get_admin_ids($lzl_id,1,1);
}


function get_group_creator($group_id){
    $group = D('Group')->getGroup($group_id);
    return $group['uid'];

}



function filter_post_content($content){
    $content = op_h($content);
    $content = limit_picture_count($content);
    $content = op_h($content);
    return $content;
}

function limit_picture_count($content){
  return   D('ContentHandler')->limitPicture($content,modC('GROUP_POST_IMG_COUNT',10,'GROUP'));
}

/**
 * 权限检测时获取要排除的uids(群创建者、群组管理员、自己)
 * @param int $id
 * @param int $type
 * @param int $with_self 是否包含记录的uid
 * @return array|int|mixed
 * @author 郑钟良<zzl@ourstu.com>
 */
function get_admin_ids($id=0,$type=0,$with_self=1)
{
    $uid=0;
    switch($type){
        case '1'://根据贴子楼中楼回复id查询排除者id
            $lzl_reply=M('GroupLzlReply')->find($id);
            $uid=$lzl_reply['uid'];
            $post_id=$lzl_reply['post_id'];
            break;
        case '2'://根据贴子回复id查询排除者id
            $reply = M('GroupPostReply')->find($id);
            $uid=$reply['uid'];
            $post_id=$reply['post_id'];
            break;
        case '3'://根据贴子id查询排除者id
            $post_id=$id;
            break;
        case '4'://根据群组 id查询排除者id
            $group_id=$id;
            break;
        default:
            return -1;
    }
    if($post_id){
        $post=M('GroupPost')->where(array('id' => $post_id, 'status' => 1))->find();
        $group_id=$post['group_id'];
        if(!$uid){
            $uid=$post['uid'];
        }
    }
    $expect_ids=D('GroupMember')->getGroupAdmin($group_id);
    $group=M('Group')->find($group_id);
    if($uid&&$with_self&&$uid!=$group['uid']){
        $expect_ids[]=$group['uid'];
    }
    return $expect_ids;
}