<?php

class Domain_Post {

    public function getIndexPost() {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getIndexPost();
        return $rs;
    }

    public function getGroupPost($groupID) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getGroupPost($groupID);
        return $rs;
    }
    
    public function getMyGroupPost($userID) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getMyGroupPost($userID);
        return $rs;
    }

    public function getPostDetail($postID) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getPostDetail($postID);
        return $rs;
    }

}
