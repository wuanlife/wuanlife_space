<?php
function parse_at_users($content,$disabel_hight=false)
{
    $content = $content . ' ';
    //找出被AT的用户
    $at_usernames = get_at_usernames($content);

    //将@用户替换成链接
    foreach ($at_usernames as $e) {
        $user = D('Member')->where(array('nickname' => $e))->find();
        if ($user) {
            $query_user = query_user(array('space_url','avatar32'), $user['uid']);
            if(modC('HIGH_LIGHT_AT',1,'Weibo') && !$disabel_hight){
                $content = str_replace("@$e", " <a class='user-at hl ' ucard=\"$user[uid]\" href=\"$query_user[space_url]\"><img src=\"$query_user[avatar32]\">@$e </a> ", $content);
            }else{
                $content = str_replace("@$e", " <a ucard=\"$user[uid]\" href=\"$query_user[space_url]\">@$e </a> ", $content);
            }

        }
    }

    //返回替换的文本
    return $content;
}

/**
 * get_at_usernames  获取@用户的用户名
 * @param $content
 * @return array
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function get_at_usernames($content)
{
    //正则表达式匹配
    $user_pattern = "/\\@([^\\#|\\s|^\\<]+)/";
    preg_match_all($user_pattern, $content, $users);

    //返回用户名列表
    return array_unique($users[1]);
}

/**
 * get_at_uids  获取@的用户的uid
 * @param $content
 * @return array
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function get_at_uids($content)
{
    $usernames = get_at_usernames($content);
    $result = array();
    foreach ($usernames as $username) {
        $user = D('Member')->where(array('nickname' => op_t($username)))->field('uid')->find();
        $result[] = $user['uid'];
    }
    return $result;
}

function parse_comment_content($content)
{
    //就目前而言，评论内容和微博的格式是一样的。
    return parse_weibo_content($content);
}

function parse_weibo_content($content)
{
    $content = shorten_white_space($content);
    $content = op_t($content,false);
    $content = parse_url_link($content);
    $content = parse_expression($content);
    $content = parse_at_users($content);

    $content = parseWeiboContent($content);
    return $content;
}

function shorten_white_space($content)
{
    $content = preg_replace('/\s+/', ' ', $content);
    return $content;
}

function parse_url_link($content)
{
    $content = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
        "'<a class=\"label label-badge\" href=\"$1\" target=\"_blank\"><i class=\"icon-link\" title=\"$1\"></i></a>$4'", $content
    );
    return $content;
}

function parseWeiboContent($content)
{
    hook('parseWeiboContent', array('content' => &$content));
    return $content;

}



function parse_content($content){
    return $content;
}