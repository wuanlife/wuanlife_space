<?php
/**
 * COOKIE加密解密服务类
 */
class Domain_Crypt implements PhalApi_Crypt {

    public function encrypt($data, $key) {
        return base64_encode($data);
    }

    public function decrypt($data, $key) {
        return base64_decode($data);
    }
}