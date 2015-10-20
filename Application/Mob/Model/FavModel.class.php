<?php
namespace Mob\Model;

use Think\Model;

class FavModel extends Model
{
    protected $tableName = 'cat_fav';

    function checkFav($mid, $info_id)
    {
        $map['uid'] = $mid;
        $map['info_id'] = $info_id;
        return D('cat_fav')->where($map)->count();


    }

    function doFav($mid, $info_id)
    {
        $fav['uid'] = $mid;
        $fav['cTime'] = time();
        $fav['info_id'] = $info_id;
        if ($this->add($fav)) {
            return 1;
        } else {
            return 0;
        }
    }

    function doDisFav($mid, $info_id)
    {
        $fav['uid'] = $mid;
        $fav['info_id'] = $info_id;
        if ($this->where($fav)->delete()) {
            return 1;
        } else {
            return 0;
        }
    }

    function getList($map = '', $num = 10, $order = 'cTime desc')
    {
    }

    function getLimit($map = '', $num = 10, $order = 'cTime desc')
    {
    }

    function getById($id)
    {
    }
}