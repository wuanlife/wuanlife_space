<?php
namespace Mob\Model;

use Think\Model;

/**
 * 所属项目 商业版.
 * 开发者: 陈一枭
 * 创建日期: 14-10-10
 * 创建时间: 上午9:06
 * 版权所有 想天软件工作室(www.ourstu.com)
 */
class CurrencyModel extends Model
{
    protected $tableName = 'member';
    protected $mField = 'score4';

    protected function _initialize()
    {
        parent::_initialize();
        //获取到货币配置
        $this->mField = 'score' . modC('CURRENCY_TYPE', '4', 'Store');
    }

    /**
     * 获取微店交易积分值
     * @param int $uid
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getCurrency($uid = 0)
    {
        !$uid && $uid = is_login();
        $score_id = modC('CURRENCY_TYPE', 4, 'Store');
        $scoreModel = D('Ucenter/Score');
        $currency = $scoreModel->getUserScore($uid, $score_id);
        return $currency;
    }

    /**
     * 获取微店交易积分类型
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getCurrency_info()
    {
        $score_id = modC('CURRENCY_TYPE', 4, 'Store');
        $scoreModel = D('Ucenter/Score');
        $currency = $scoreModel->getType($score_id);
        return $currency;
    }

    /**付款
     * @param int $price
     * @param int $uid
     * @return bool
     * @auth 陈一枭
     */
    public function pay($price = 0, $uid = 0)
    {
        $info = $this->getCurrency_info();
        if ($price <= 0) {
            $this->error = '支付' . $info['title'] . '小于0' . $info['unit'];
            return false;
        }
        $currency = $this->getCurrency($uid);
        $hasLeft = $currency - $price;
        if ($hasLeft >= 0) {
            return $this->adjust(-$price, $uid);
        } else {
            $this->error = '余额不足。';
            return false;
        }
    }

    /**
     * 调整积分，允许增减
     * @param int $amount
     * @param int $uid
     * @return bool
     * @auth 陈一枭
     */
    public function adjust($amount = 0, $uid = 0)
    {
        if ($uid == 0) {
            $uid = is_login();
        }
        if ($amount == 0) {
            $this->error = '调整数值为0';
            return false;
        }
        $result = $this->where(array('uid' => $uid))->setInc($this->mField, $amount);

        if ($result) {
            return true;
        } else {
            $this->error = '设置货币失败。';
            return false;
        }
    }
}