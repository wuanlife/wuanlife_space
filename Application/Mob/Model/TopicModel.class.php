<?php
/**
 * 话题模型
 * 没有话题的情况下添加话题
 * 有话题的时候在输入#的时候弹出
 * @author quick
 *
 */
namespace Mob\Model;

use Think\Model;
use Think\Page;

class TopicModel extends Model
{

    protected $tableName = 'weibo_topic';

    public function addTopic(&$content)
    {
        //检测话题的存在性
        $topic = get_topic($content);
        if (isset($topic) && !is_null($topic)) {
            foreach ($topic as $e) {
                $tik = $this->where(array('name' => $e))->find();
                //没有这个话题的时候创建这个话题
                if (!$tik) {
                    $this->add(array('name' => $e));
                }
            }
        }
    }

    /**获取热门话题
     * @param int $type
     */
    public function getHot($hour = 1, $num = 10, $page = 1)
    {

        $map['create_time'] = array('gt', time() - $hour * 60 * 60);

        $map['status'] = 1;
        $weiboModel = M('Weibo');

        $all_topic = $this->where(array('status' => 1), array(array('read_count' => array('neq', 0))))->select();
        foreach ($all_topic as $key => &$v) {
            $map['content'] = array('like', "%#{$v['name']}#%");
            $v['weibos'] = $weiboModel->where($map)->count();
            if ($v['weibos'] == 0) {
                unset($all_topic[$key]);
            }
            $v['user'] = query_user(array('space_link'), $v['uadmin']);
        }
        unset($v);

        $all_topic = $this->arraySortByKey($all_topic, 'weibos', false);
        $i = 0;
        foreach ($all_topic as &$v) {
            $v['top_num'] = ++$i;
        }
        unset($v);
        $pager = new Page(count($all_topic), $num);
        // dump($all_topic);exit;

        $list['data'] = array_slice($all_topic, ($page - 1) * $num, $num);
        $list['html'] = $pager->show();
        return $list;
    }

    /**
     * 根据数组中的某个键值大小进行排序，仅支持二维数组
     *
     * @param array $array 排序数组
     * @param string $key 键值
     * @param bool $asc 默认正序
     * @return array 排序后数组
     */
    function arraySortByKey(array $array, $key, $asc = true)
    {
        $result = array();
        // 整理出准备排序的数组
        foreach ($array as $k => &$v) {
            $values[$k] = isset($v[$key]) ? $v[$key] : '';
        }
        unset($v);
        // 对需要排序键值进行排序
        $asc ? asort($values) : arsort($values);
        // 重新排列原有数组
        foreach ($values as $k => $v) {
            $result[$k] = $array[$k];
        }

        return $result;
    }
}