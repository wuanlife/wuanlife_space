<?php

class Domain_db {

    public function getIndexPost() {
        $rs = array();

        // $userId = intval($userId);
        // if ($userId <= 0) {
        //     return $rs;
        // }
        $model = new Model_db();
        $rs = $model->getIndexPost();
        return $rs;
    }

    public function getGroupPost() {
        $rs = array();

        // $userId = intval($userId);
        // if ($userId <= 0) {
        //     return $rs;
        // }
        $model = new Model_db();
        $rs = $model->getIndexPost();
        return $rs;
    }
    
    public function getMyGroupPost() {
        $rs = array();

        // $userId = intval($userId);
        // if ($userId <= 0) {
        //     return $rs;
        // }
        $model = new Model_db();
        $rs = $model->getIndexPost();
        return $rs;
    }
}
