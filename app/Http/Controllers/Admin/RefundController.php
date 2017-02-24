<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\RefundRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends Controller
{
    protected $refund;

    public function __construct(RefundRepository $refund)
    {
        $this->refund = $refund;
    }

    /**
     * 列表
     * @param Request $request
     * @return $this
     * @author: simayubo
     */
    public function index(Request $request){
        if (!request()->user('admin')->can('refund.list')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        $list = $this->refund->getList($request);
        return view('admin.refund.list')->with(compact('list'));
    }

    /**
     * 详情
     * @param $id
     * @return $this
     * @author: simayubo
     */
    public function show($id){
        if (!request()->user('admin')->can('refund.list')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        $info = \DB::table('refund')
            ->select('refund.*', 'reason.title')
            ->where('refund.id', $id)
            ->leftJoin('reason', 'refund.reason_id', '=', 'reason.id')
            ->first();
        $imgs = [];
        if (!empty($info->pic)){
            $imgs = explode('@', trim($info->pic, '@'));
        }
        $wuliu = $this->refund->getWuliuTracesByRefundId($id);

        return view('admin.refund.show')->with(compact('info', 'imgs', 'wuliu'));
    }

    /**
     * 审核(退款退货审核)
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function shenhe($id){
        if (!request()->user('admin')->can('refund.edit')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        return $this->refund->shenhe($id);
    }

    /**
     * 拒绝审核（拒绝退款退货）
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function jujue($id){
        if (!request()->user('admin')->can('refund.edit')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        return $this->refund->jujue($id);
    }

    /**
     * 退货时后台确认收货
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function shouhuo($id){
        if (!request()->user('admin')->can('refund.edit')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        return $this->refund->shouhuo($id);
    }

    /**
     * 退货时后台拒绝收货
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function jujueShouhuo($id){
        if (!request()->user('admin')->can('refund.edit')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        return $this->refund->jujueShouhuo($id);
    }

}
