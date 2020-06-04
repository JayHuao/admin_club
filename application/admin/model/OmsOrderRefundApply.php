<?php

namespace app\admin\model;

use mirse\Message;
use mirse\Payment;
use think\Config;
use think\Db;
use think\Model;
use think\Session;

class OmsOrderRefundApply extends Model
{
    public function base()
    {
        return $this->belongsTo('OmsOrder', 'order_id');
    }

    public function detail()
    {
        return $this->hasMany('OmsOrderRefundDetail', 'order_refund_apply_id');
    }

    public static function getTotal($filter=null)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('s.name|order_no|ora.remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['status']) && $model->where('ora.status', $filter['status']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['apply_time']) && $model->whereTime('apply_time', 'between', [
            $filter['apply_time'],
            strtotime('+1 day', strtotime($filter['apply_time']))
        ]);

        $total = $model
            ->alias('ora')
            ->join('pms_store s', 's.id = ora.store_id')
            ->count();

        return $total;
    }

    public static function getList($column, $filter, $page, $limit, $order)
    {
        $model = new self();

        !empty($filter['search']) && $model->where('s.name|order_no|ora.remark', 'like', '%' . $filter['search'] . '%');
        !empty($filter['status']) && $model->where('ora.status', $filter['status']);
        !empty($filter['store_id']) && $model->where('store_id', $filter['store_id']);
        !empty($filter['apply_time']) && $model->whereTime('apply_time', 'between', [
            $filter['apply_time'],
            strtotime('+1 day', strtotime($filter['apply_time']))
        ]);

        if (isset($order)) {
            $col = $order[0]['column'];
            $dir = $order[0]['dir'];
            $model->order($column[$col]. ' ' . $dir);
        } else {
            $model->order('apply_time desc');
        }

        $list = $model
            ->alias('ora')
            ->join('pms_store s','s.id = ora.store_id')
            ->join('admin a', 'a.id = ora.handler_id')
            ->field('ora.*,s.name as store_name,a.nickname as handler')
            ->page($page, $limit)
            ->select();

        return $list;
    }

    /**
     * 通过退款申请
     * @param int $applyId 申请id
     */
    public static function passApply($applyId)
    {
        // 修改申请状态
        $refund = self::get($applyId);
        $refund->status = 2; // 状态已通过
        $refund->handler_id = Session::get(Config::get('user_auth_key'))['id'];
        $refund->handle_time = date('Y-m-d H:i:s');
        $refund->save();

        // 退款
        $payment = new Payment();
        $orderId = $refund->order_id;
        $orderNo = $refund->order_no;
        $totalFee = bcmul($refund->pay_amount, 100);
        $refundFee = bcmul($refund->refund_amount, 100);
        $remark = '订单 ' . $orderNo . ' 退款';
        $res_refund = $payment->refund($orderNo, $totalFee, $refundFee, $remark);

        // 发送通知
        $message = new Message();
        $message->sendRefundMessage($orderId, $refund->refund_amount);

        // 减去佣金
        self::deductRefundCommission($refund);

        return $res_refund;
    }

    public static function banApply($applyId, $remark)
    {
        $refund = self::get($applyId);
        $refund->status = 3; // 状态已驳回
        $refund->handler_id = Session::get(Config::get('user_auth_key'))['id'];
        $refund->handle_time = date('Y-m-d H:i:s');
        $refund->handle_remark = $remark;
        $res = $refund->save();

        return $res;
    }

    /**
     * 扣除退款佣金
     */
    public static function deductRefundCommission($refund)
    {
        // 退款详情
        $refundDetail = Db::name('oms_order_refund_detail')
            ->where('order_refund_apply_id', $refund['id'])
            ->select();
        $commission = 0;
        $detail = [];
        foreach ($refundDetail as $key => $value) {
            // 佣金比例
            $rate = Db::name('dms_product_commission_rate')
                ->alias('pcr')
                ->join('pms_products p','p.product_library_id = pcr.product_library_id')
                ->where('pcr.status',1)
                ->where('p.id', $value['product_id'])
                ->value('group_rate');
            $rate = $rate ?: json_decode(Db::name('cog')->where('name', 'distribute')->value('value'), true)['group_rate'];

            $amount = bcmul($value['product_price'], $value['number'], 3); // 商品金额
            $productCommission = bcdiv(bcmul($rate, $amount, 3), 100, 3); // 佣金
            $commission += $productCommission;
            // 佣金详情
            array_push($detail, [
                'product_name'      => $value['product_name'],
                'amount'            => $amount,
                'rate_type'         => 2,
                'rate'              => $rate,
                'commission_amount' => $productCommission
            ]);
        }
        $commission = number_format($commission, 2);

        $remark = '『订单 ' . $refund['order_no'] . '退款』';
        $userId = Db::name('user_position')
            ->where('store_id', $refund['store_id'])
            ->where('position', 1)
            ->value('user_id'); // 团长
        $userCommissionId = \app\api\model\DmsUserCommission::createRefundData(5,0, $userId, $refund['applicant_id'], $refund['order_id'], 0, $refund['refund_amount'], $commission, $remark);
        // 插入用户佣金详情
        $push = ['user_commission_id'=>$userCommissionId];
        array_walk($detail, function (&$value, $key, $push) {
            $value = array_merge($value, $push);
        }, $push);
        Db::name('dms_user_commission_detail')->insertAll($detail);
    }
}
