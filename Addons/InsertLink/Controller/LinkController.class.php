<?php

namespace Addons\InsertLink\Controller;

use Home\Controller\AddonsController;

class LinkController extends AddonsController
{


    public function getLinkContent()
    {
        require_once('./ThinkPHP/Library/Vendor/Collection/phpQuery.php');
        $link = op_t(I('post.url'));
        $content = get_content_by_url($link);

        $charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $content, $temp) ? strtolower($temp[1]) : "utf-8";
        \phpQuery::$defaultCharset = $charset;
        \phpQuery::newDocument($content);
        $title = pq("meta[name='title']")->attr('content');
        if (empty($title)) $title = pq("title")->html();
        $title = iconv($charset, "UTF-8", $title);
        $keywords = pq("meta[name='keywords'],meta[name='Keywords']")->attr('content');
        $description = pq("meta[name='description'],meta[name='Description']")->attr('content');
        $url = parse_url($link);
        $img = pq("img")->eq(0)->attr('src');
        if (is_bool(strpos($img, 'http://'))) {
            $img = 'http://' . $url['host'] . $img;
        }

        $title = text($title);
        $description = text($description);
        $keywords = text($keywords);

        $return['title'] = $title;
        $return['img'] = $img;
        $return['description'] = empty($description) ? $title : $description;
        $return['keywords'] = empty($keywords) ? $title : $keywords;
        exit(json_encode($return));
    }


}