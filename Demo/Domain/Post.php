<?php

class Domain_Post {

    public function getIndexPost($page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getIndexPost($page);
        return $rs;
    }

    public function getGroupPost($groupID,$page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getGroupPost($groupID,$page);
        return $rs;
    }
    
    public function getMyGroupPost($userID,$page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getMyGroupPost($userID,$page);
        return $rs;
    }

    public function getPostBase($postID) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getPostBase($postID);
        return $rs;
    }

    public function getPostReply($postID,$page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getPostReply($postID,$page);
        return $rs;
    }
    public function PostReply($data) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->PostReply($data);
        return $rs;
    }
    public function editPost($data) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->editPost($data);
        return $rs;
    }
}
