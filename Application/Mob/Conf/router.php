<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-27
 * Time: 下午4:54
 * @author 嘉兴想天信息科技有限公司-郑钟良<zzl@ourstu.com>
 */
return array(

    /**
     * 路由的key必须写全称,且必须全小写. 比如: 使用'wap/index/index', 而非'wap'.
     */
    'router' => array(

        //消息
        /*微博*/
        'Weibo/Index/weiboDetail'               =>'Mob/Weibo/weiboDetail',

        /*专辑*/
        'Issue/Index/issueContentDetail'        =>'/mob/issue/issuedetail',

        /*资讯*/
        'News/Index/detail'                     =>'mob/blog/blogdetail',

        /*问答*/
        'Question/Index/detail'                 =>'mob/question/questiondetail',

        /*论坛*/
        'Forum/Index/detail#'                 =>'mob/forum/postdetail',

        /*活动*/
        'Event/Index/member'                   =>'mob/event/detail',

        /*问答*/
        'Question/index/detail'              =>'mob/question/questiondetail',

        /*群组*/
        'Group/index/detail'                 =>'mob/group/detail',

        /*分类信息*/
        'Cat/Center/rec'                    =>'mob/cat/detail',

        /*积分商城*/
        'Shop/Index/myGoods'                =>'mob/shop/myorder',

        /*微店*/
        'store/center/sold'                 =>'mob/store/soldsell',


        //入口
        /*活动*/
        'Event/index/index'                  =>'mob/event/index',
        /*论坛*/
        'Forum/index/index'                 =>'mob/forum/index',
        /*专辑*/
        'Issue/index/index'                 =>'mob/issue/index',
        /*资讯*/
        'News/index/index'                 =>'mob/blog/index',
        /*会员*/
        'People/index/index'               =>'mob/people/index',
        /*问答*/
        'Question/index/index'             =>'mob/question/index',
        /*微博*/
        'Weibo/index/index'                =>'mob/weibo/index',
        /*用户中心*/
        'Ucenter/index/index'              =>'mob/user/index',
        /*群组*/
        'Group/index/index'                =>'mob/group/index',
        /*分类信息 */
        'Cat/index/index'                   =>'mob/cat/index',
        /*积分商城*/
        'Shop/index/index'                  =>'mob/shop/index',
        /*商城*/
        'Store/index/index'                =>'mob/store/index',

    ),

);