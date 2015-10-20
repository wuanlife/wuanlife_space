<?php
namespace Mob\Model;

use Think\Model;
use Think\Hook;

require_once('./Application/Mob/Common/function.php');
class WeiboModel extends Model
{


    public function addWeibo($uid, $content = '', $type = 'feed', $feed_data = array(), $from = '')
    {
//写入数据库

        //  dump($uid);exit;
        $create_time=time();
        $data = array('uid' => $uid, 'content' => $content, 'type' => $type, 'create_time'=>$create_time, 'data' => serialize($feed_data), 'from' => $from,'status'=>1);

        if (!$data) return false;
        $weibo_id = $this->add($data);

//返回微博编号
        return $weibo_id;
    }





    public function getWeiboDetail($id)
    {
      //  $weibo = S('weibo_' . $id);

       // $check_empty = empty($weibo);
      //  if ($check_empty) {
            $weibo = $this->where(array('status' => 1, 'id' => $id))->find();
            if (!$weibo) {
                return null;
            }
            $weibo_data = unserialize($weibo['data']);
            $class_exists = true;

            $type = array('repost', 'feed', 'image','share');
            if (!in_array($weibo['type'], $type)) {
                $class_exists = class_exists('Addons\\Insert' . ucfirst($weibo['type']) . '\\Insert' . ucfirst($weibo['type']) . 'Addon');
            }
            $weibo['content'] = parse_topic(parse_weibo_mobile_content($weibo['content']));
            if ($weibo['type'] === 'feed' || $weibo['type'] == '' || !$class_exists) {
                $fetchContent = "<p class='word-wrap'>" . $weibo['content'] . "</p>";
            } elseif ($weibo['type'] === 'repost') {
                $fetchContent = A('Mob/WeiboType')->fetchRepost($weibo);
            } elseif ($weibo['type'] === 'image') {
                $fetchContent = A('Mob/WeiboType')->fetchImage($weibo);
            } elseif($weibo['type'] === 'share'){
                $fetchContent = R('Weibo/Share/getFetchHtml',array('param'=>unserialize($weibo['data']),'weibo'=>$weibo),'Widget');
            }
            else {
                $fetchContent = Hook::exec('Addons\\Insert' . ucfirst($weibo['type']) . '\\Insert' . ucfirst($weibo['type']) . 'Addon', 'fetch' . ucfirst($weibo['type']), $weibo);
            }
            $weibo = array(
                'id' => intval($weibo['id']),
                'content' => strval($weibo['content']),
                'create_time' => intval($weibo['create_time']),
                'type' => $weibo['type'],
                'data' => unserialize($weibo['data']),
                'weibo_data' => $weibo_data,
                'comment_count' => intval($weibo['comment_count']),
                'repost_count' => intval($weibo['repost_count']),
                'can_delete' => 0,
                'is_top' => $weibo['is_top'],
                'uid' => $weibo['uid'],
                'fetchContent' => $fetchContent,
                'from' => $weibo['from']

            );
        //    S('weibo_' . $id, $weibo, 60 * 60);
      //  }
        $weibo['user'] = query_user(array('uid', 'nickname', 'avatar64', 'space_url', 'rank_link', 'title'), $weibo['uid']);
        $weibo['can_delete'] = $this->canDeleteWeibo($weibo);


        // 判断转发的原微博是否已经删除
        if($weibo['type'] == 'repost'){
            $source_weibo = $this->getWeiboDetail( $weibo['weibo_data']['sourceId']);
            if(!$source_weibo['uid']){
                if(!$check_empty){
                    S('weibo_' . $id, null);
                    $weibo = $this->getWeiboDetail($id);
                }
            }
        }

        return $weibo;
    }


    private function canDeleteWeibo($weibo)
    {
        //如果是管理员，则可以删除微博
        if (is_administrator(get_uid()) || check_auth('deleteWeibo')) {
            return true;
        }

        //如果是自己发送的微博，可以删除微博
        if ($weibo['uid'] == get_uid()) {
            return true;
        }

        //返回，不能删除微博
        return false;
    }
}