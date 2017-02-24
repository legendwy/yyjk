<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\v1_0\OrderController;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\GoodsRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use App\Repositories\Eloquent\UserRepository;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

class WechatController extends Controller
{

    protected $weObj;

    public function __construct()
    {
        $options = array(
            'token' => config('weixin.token'), //填写你设定的key
            'encodingaeskey' => config('weixin.aes_key'), //填写加密用的EncodingAESKey
            'appid' => config('weixin.app_id'), //填写高级调用功能的app id
            'appsecret' => config('weixin.secret')  //填写高级调用功能的密钥
        );
        $weObj = new WxApiController($options);
        $this->weObj = $weObj;
    }

    /**
     * User: lf
     */
    public function jsApi(Request $request)
    {
        error_reporting(0);
        //使用jsapi接口
        $jsApi = new \JsApi_pub();
        $order_list = DB::table('order_info as oi')
            ->join('order_goods as og', 'oi.id', '=', 'og.order_id')
            ->where('oi.id', $request->get('oi_id', 213))
            ->select('oi.*', 'og.id as og_id', 'og.price as danjia', 'og.num', 'og.goods_name', 'og.goods_attr_values')
            ->get()->toArray();
        $body = '';
        foreach ($order_list as $k => &$v) {
            $v = get_object_vars($v);
            if ($k < 1) {
                $order_num = $v['order_num'];
                $user_id = $v['user_id'];
            }
//            $body .= $v['goods_name'] . ':' . $v['danjia'] . '*' . $v['num'] . ';';
            $body .= mb_substr($v['goods_name'], 0, 5) . '...;';
        }
        $openid = DB::table('users')->where('id', $user_id)->value('openid');
//        $openid = "obUpfxEp67MnOesdnMu64ct0Chew";
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
        $unifiedOrder->setParameter("openid", $openid); //用户标识
        $unifiedOrder->setParameter("body", $body); //商品描述
        $unifiedOrder->setParameter("out_trade_no", $order_num); //商户订单号
        $totalfee = 0.01 * 100;
        $unifiedOrder->setParameter("total_fee", (int)$totalfee); //总金额
        $unifiedOrder->setParameter("notify_url", 'http://' . $_SERVER['HTTP_HOST'] . "/notify"); //通知地址
        $unifiedOrder->setParameter("trade_type", "JSAPI"); //交易类型
        $unifiedOrder->setParameter("input_charset", "UTF-8");
        //获取统一支付接口结果
        $prepay_id = $unifiedOrder->getPrepayId();
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();
//        dd($unifiedOrder);
        $jsApiParameters = json_decode($jsApiParameters, TRUE);
        $base = new BaseController();
        if ($jsApiParameters['package'] == 'prepay_id=' || $order_list['status'] != -1) {
            $base->returnMsg(true, 422, '当前订单存在异常，不能使用支付', 422);
        }
//        return $jsApiParameters;
        return $base->returnMsg(true, 200, 'true', $jsApiParameters, 200);
    }

    /**
     * 微信支付回调地址
     */
    public function notify(GoodsRepository $goods)
    {
        error_reporting(0);
        //使用通用通知接口
        $notify = new \Notify_pub();
        //存储微信的回调
//        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml = file_get_contents('php://input');
        $notify->saveData($xml);
        //验证签名，并回应微信。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //返回信息
        } else {
            DB::table('logs')->insert(['title' => 6, 'content' => 6]);
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码
        }
//        $returnXml = $notify->returnXml();
//        echo $returnXml;
        if ($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
            } elseif ($notify->data["result_code"] == "FAIL") {
            } else {
                //支付成功
                $out_trade_no = $notify->data['out_trade_no'];
                $order_info = DB::table('order_info')->where('order_num', $out_trade_no)->first();
                if ($order_info->status == -1) {
                    if ($goods->pay($order_info->id, '', $order_info->user_id, 2, 0) == 1) {
                    }
                }

            }
        }
    }

    public function index()
    {
        if (!isset($_GET['echostr'])) {
            $this->responseMsg();
        } else {
            $this->weObj->valid();
        }
    }


    /**
     * 自动回复消息
     */
    protected function responseMsg()
    {
        $postStr = file_get_contents('php://input');
//        $this->weObj->sendCustomMessage(array('touser' => 'obUpfxEp67MnOesdnMu64ct0Chew', 'msgtype' => 'text', 'text' => array('content' => '您的专属二维码正在努力生成中，请等待.....若生成失败，请重新点击即可!')));
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            //用户发送的消息类型判断
            switch ($RX_TYPE) {
//                case "text":    //文本消息
//                    $result = $this->transmitText($postObj, 'hello');
//                    break;
                case "event":   //事件
                    $result = $this->receiveEvent($postObj);
                    break;
//                default:
//                    $result = "unknow msg type: " . $RX_TYPE;
//                    break;
            }
//            echo $result;
        } else {
            exit('');
        }
    }

    /**
     * 事件处理逻辑
     */
    protected function receiveEvent($postObj)
    {
        $event = $postObj->Event;
        switch ($event) {
            case 'subscribe':
                $this->guanzhu($postObj);
                break;
            case 'CLICK':
                $this->receiveClick($postObj);
                break;
            case 'SCAN':
                $this->fromQrcode($postObj);
                break;
            default:
                break;
        }
    }

    /**
     * click事件处理逻辑
     */
    protected function receiveClick($object)
    {
        switch ($object->EventKey) {
            case "QRCode"://生成二维码链接
                $userInfo = $this->getUserinfo($object);
                $this->weObj->sendCustomMessage(array('touser' => $userInfo->openid, 'msgtype' => 'text', 'text' => array('content' => '您的专属二维码正在努力生成中，请等待.....若生成失败，请重新点击即可!')));
                $medid_time = !empty($userInfo->medid_time) ? $userInfo->medid_time : 0;
                if (time() - $medid_time > 252000) {//没有推广二维码或过期
                    if (!$userInfo->qrcode) {
                        $res = $this->weObj->getQRCode($userInfo->id, 1);
                        $Qurl = $this->weObj->getQRUrl($res['ticket']);
                        $path = $this->downLoadQr($Qurl, $userInfo->id);
                        DB::table('users')->where('id', $userInfo->id)->update(['qrcode' => $path]);
                        $userInfo->qrcode = $path;
                    }
//                    $header = $this->downHeader($userInfo->id);
//                    $path = $this->mergeImage($userInfo->qrcode, $header, $userInfo->id);//合成推广二维码
                    $path = $userInfo->qrcode;
//                    echo $this->transmitText($object, $path);return;
                    $media = $this->uploadImage($object, $path);
                    DB::table('users')->where('id', $userInfo->id)->update(['media' => $media, 'medid_time' => time()]);
                    $userInfo->media = $media;
                }
                $content = $this->transmitImage($object, array('MediaId' => $userInfo->media));
                break;
            case "meimei";
                $text = "亲爱的健康团粉/可爱，您好，欢迎进入胜者有益健康系统，直接编辑您所遇到的问题编辑发送就可以了哦，云妹妹已做好充分准备，时刻为你答疑解惑/示爱，谢谢您的参与和支持/示爱。工作时间周日到周五：9:00-21:30，周六：9:00-18:00，请在工作时间内咨询问题哦";
                $content = $this->transmitText($object, $text);
                break;
        }
        echo $content;
        return;
    }

    //生成二维码
    public function webQRCode($id)
    {
        $res = $this->weObj->getQRCode($id, 1);
        $Qurl = $this->weObj->getQRUrl($res['ticket']);
        $path = $this->downLoadQr($Qurl, $id);
        if (DB::table('users')->where('id', $id)->update(['qrcode' => $path])) {
            return $path;
        } else {
            return false;
        }
    }

    /**
     * 关注后操作
     * @return [type] [description]
     */
    protected function guanzhu($postObj)
    {
        $openid = $postObj->FromUserName;
        $userinfo = DB::table('users')->where('openid', $openid)->first();
        if ($userinfo) {
            $reply_text = $userinfo->nickname . '欢迎回来！';
        } else {
            $userinfo = $this->getUserinfo($postObj);
            $reply_text = "感谢您关注有益健康！";
        }
        if ($userinfo->pid) {
            $p_userinfo = DB::table('users')->where('id', $userinfo->pid)->first();
            $reply_text .= '您的上级为：' . $p_userinfo->nickname;
        } else {
            $this->fromQrcode($postObj, $userinfo);
        }
        echo $this->transmitText($postObj, $reply_text);
        return;
    }

    /**
     * 扫描二维码进入
     */
    protected function fromQrcode($postObj, $userinfo = NULL)
    {
        $eventKey = $postObj->EventKey;
        /*
         * 如果不为空的话..说明是从带参数的二维码扫描进入的
         * 事件类型， Event:subscribe 未关注时返回数据格式 arrar('event'=>'subscribe', 'key'=>'qrscene_2')  (qrscene_2: qrscene是前缀，2是参数值)
         * 事件类型，Event:SCAN 关注时返回数据格式 arrar('event'=>'subscribe', 'key'=>'2')  (2是参数值)
         */

        $event = $postObj->Event;
        if ($event == 'subscribe') {
            $pid = explode('qrscene_', $eventKey);
            $pid = $pid[1];
        } else {
            $pid = $eventKey;
        }
        if ($pid == 0) {
            echo $this->transmitText($postObj, "感谢您关注有益健康！");
            return;
        }
        if (!$userinfo) {
            $userinfo = $this->getUserinfo($postObj);
        }
        //确定上级
        if ($userinfo->id == $pid) {
            echo $this->transmitText($postObj, '不能对自己的二维码进行操作！');
            return false;
        }
        if (!$userinfo->pid) {
            $p_userinfo = DB::table('users')->where('id', (int)$pid)->first();
            if (!$p_userinfo->status) {
                echo $this->transmitText($postObj, '该二维码的用户已被禁用！');
                return false;
            }
            $data['pid'] = (int)$pid;
            $data['tuiguang_time'] = time();
            if ($res = DB::table('users')->where('id', $userinfo->id)->update($data)) {
                $userinfo->pid = $pid;
            };
        }
        $nickname = DB::table('users')->where('id', $userinfo->pid)->value('nickname');
        echo $this->transmitText($postObj, '您的上级为：' . $nickname);
        return false;
    }

    //绑定上下级
    public function bingPid($pid, $child)
    {
        $child_pid = DB::table('users')->where('id', (int)$child)->value('pid');
        if ($child_pid != 0 || $pid == $child) {
            return false;
        }
        if (DB::table('users')->where('id', (int)$child)->update(['pid' => (int)$pid])) {
            return true;
        };
        return false;
    }

    /**
     * 获取JsApi使用签名
     */
    public function getJsSign($goods_id, $uid,$url)
    {
        file_put_contents("easywechat.log",$url);
        $jump_url = 'http://smyb05.huiyaoba.com/product/' . $goods_id . '?user_pid=' . $uid;
        if (count(explode('singlemessage', $_SERVER['QUERY_STRING'])) > 1) {
            $str = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $url = substr($str, 0, -36);
        }
        $signPackage = $this->weObj->getJsSign($url, '12345678901', 'asdfghjkl');
        $signPackage['url'] = $jump_url;
        return $signPackage;
    }

    public function ss()
    {
        $this->sendTemplateMessage(72, 1);
    }

    /**
     * 发送模板消息
     * @param type $order_id
     * @param type $status 1，待发货；2，已发货;7，退货中,10，同意退款,8，退货完成
     */
    public function sendTemplateMessage($order_id, $status = null)
    {
        if ($status == 1 || $status == 2) {
            $order_list = DB::table('order_info as oi')
                ->join('order_goods as og', 'oi.id', '=', 'og.order_id')
                ->leftJoin('logistics', 'oi.wuliu_gongsi', '=', 'logistics.id')
                ->where('oi.id', $order_id)
                ->select('oi.*', 'og.id as og_id', 'og.price as danjia', 'og.num', 'og.goods_name', 'og.goods_attr_values', 'logistics.name as wuliu_name')
                ->get()->toArray();
            $goods_name = '';
            foreach ($order_list as $k => &$v) {
                $v = get_object_vars($v);
                if ($k < 1) {
                    $order_num = $v['order_num'];
                    $wuliu_name = $v['wuliu_name'];
                    $wuliu_num = $v['wuliu_num'];
                    $user_id = $v['user_id'];
                    $price = $v['pay_price'];
                }
                $goods_name .= $v['goods_name'] . ':(' . $v['danjia'] . '*' . $v['num'] . ');';
            }
        } elseif ($status == 3) {
            $withdraw_info = DB::table('withdraw')
                ->where('id', $order_id)
                ->first();
            $user_id = $withdraw_info->user_id;
        } else {
//            DB::connection()->enableQueryLog(); // 开启查询日志
//
            $refund_info = DB::table('refund')
                ->join('order_goods as og', 'refund.order_id', '=', 'og.id')
                ->where('refund.id', $order_id)
                ->select('refund.*', 'og.goods_name')
                ->first();
//            return DB::getQueryLog();
            $user_id = $refund_info->uid;
        }
        $openid = DB::table('users')->where('id', $user_id)->value('openid');
        if ($status == 1) {
            $url = 'http://smyb05.huiyaoba.com/center/order_xq/' . $order_id;
            $first = "您的订单已支付成功！";
            $remark = "感谢您购买我们的产品,我们将尽快配送~~";
            $template_id = "EQLQlAGKCSrT6qfXshY12TVar7GCPmCKYGe7ReF6o50";
            //构建模板
            $data = array(
                "touser" => "$openid",
                "template_id" => "$template_id",
                "url" => "$url",
                "topcolor" => "#FF0000",
                'data' => array(
                    "first" => array(
                        "value" => "$first",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" => "$goods_name",
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" => "$order_num",
                        "color" => "#173177"
                    ),
                    "keyword3" => array(
                        "value" => $price,
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" => $remark,
                        "color" => "#173177"
                    ),
                )
            );
        } else if ($status == 2) {
            $url = 'http://smyb05.huiyaoba.com/center/order_xq/' . $order_id;
            $template_id = "1-ntWdfi6CvZsOEaop5vaCCJOt2JSQZobEWie-CZtU4";
            $first = "您购买的商品已经发货啦！";
            $remark = "再次感谢您购买我们的产品,请多多支持！~_~ ";
            //构建模板
            $data = array(
                "touser" => "$openid",
                "template_id" => "$template_id",
                "url" => "$url",
                "topcolor" => "#FF0000",
                'data' => array(
                    "first" => array(
                        "value" => "$first",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" => "$order_num",
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" => "$wuliu_name",
                        "color" => "#173177"
                    ),
                    "keyword3" => array(
                        "value" => "$wuliu_num",
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" => $remark,
                        "color" => "#173177"
                    ),
                )
            );
        } elseif ($status == 3) {
            $url = '';
            $template_id = "s5Vrq6P-D64vCC_h-x2lvT6vRcK7EPKcfmYuwzfI8Rk";
            $value = $withdraw_info->status == 1 ? '已同意' : '已拒绝';
            $first = "您提交的提现申请" . $value;
            $keyword1 = '有意健康';
            $keyword2 = $withdraw_info->money;
            $keyword3 = $withdraw_info->status == 1 ? '已同意' : '已拒绝';
            $remark = $withdraw_info->status == 1 ? '感谢您选择有意健康,请多多支持！~_~ ' : '拒绝理由：' . $withdraw_info->reason;
            $keyword3 = $withdraw_info->type == 1 ? '微信提现' : '银行卡提现';
            //构建模板
            $data = array(
                "touser" => "$openid",
                "template_id" => "$template_id",
                "url" => "$url",
                "topcolor" => "#FF0000",
                'data' => array(
                    "first" => array(
                        "value" => "$first",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" => "$keyword1",
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" => "$keyword2",
                        "color" => "#173177"
                    ),
                    "keyword3" => array(
                        "value" => "$keyword3",
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" => $remark,
                        "color" => "#173177"
                    ),
                )
            );
        } else {
            $template_id = "FI0GBiG0ddF-UI6SKMVnh8NgoVDy99ZllCu0xMjidgE";
            $key = $refund_info->type == 1 ? "退款" : "退货";
            $remark = "感谢您选择有意健康,如有疑问请与客服联系！";

            if ($refund_info->status == 1) {
                if ($refund_info->type == 2 && $status) {
                    $value = "因为商品问题，已被拒收货";
                } else {
                    $value = "已被拒";
                }

            } elseif ($refund_info->status == 2) {
                $value = "已同意退货";
                $address = DB::table('config')->where('id', 11)->value('value');
                $remark = '收货地址：' . $address;
            } elseif ($refund_info->status == 3) {
                $value = "已同意退款";
            } elseif ($refund_info->status == 4) {
                $value = "退货已完成";
            }
//            DB::connection()->enableQueryLog(); // 开启查询日志
            $first = "您的" . $key . "申请" . $value;
            $goods_order = DB::table('order_goods')
                ->join('order_info', 'order_goods.order_id', '=', 'order_info.id')
                ->where('order_goods.id', $refund_info->order_id)
                ->select('order_info.add_time', 'order_info.order_num')
                ->first();
//            $queries = DB::getQueryLog(); // 获取查询日志
//            dump($order_id);
//            dd($queries);
            $time = $goods_order->add_time;
            $url = "";
            //构建模板
            $data = array(

                "touser" => "$openid",
                "template_id" => "$template_id",
                "url" => "$url",
                "topcolor" => "#FF0000",

                'data' => array(
                    "first" => array(
                        "value" => "$first",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" => "$goods_order->order_num",
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" => "$time",
                        "color" => "#173177"
                    ),
                    "keyword3" => array(
                        "value" => "$refund_info->goods_name",
                        "color" => "#173177"
                    ),
                    "keyword4" => array(
                        "value" => "$refund_info->money",
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" => $remark,
                        "color" => "#173177"
                    ),
                )
            );
        }

        $res = $this->weObj->sendTemplateMessage($data);
        return $res;
    }

    //获取个人信息
    protected function getUserinfo($postObj = null, $openid = null)
    {
        if ($postObj) {
            $openid = $postObj->FromUserName;
        }

        $userInfo = DB::table('users')->where('openid', $openid)->first();
        if ($userInfo) {
            if ($userInfo->status == 0 && $postObj != '') {
                echo $this->transmitText($postObj, '您的账号已被禁用！');
                exit;
            } elseif ($userInfo->status == 0 && $postObj == '') {
                $res = $this->weObj->sendCustomMessage(array('touser' => $openid, 'msgtype' => 'text', 'text' => array('content' => '您账号已被禁用!')));
                echo "<script>window.close();</script>";
//                exit('您的账号已被禁用！');
            }
        }
        $tmp_user_info = $userInfo;
        $medid_time = !empty($userInfo->medid_time) ? $userInfo->medid_time : 0;
        $header_time = !empty($userInfo->header_time) ? $userInfo->header_time : 0;

//        if (!$userInfo || (time() - $medid_time) > 190000) {
        if (!$userInfo) {
            //获取用户信息
            $access_token = $this->weObj->checkAuth();
            $userInfo = $this->weObj->getUiUserInfo($access_token, $openid);
//            if (!$userInfo['subscribe']) {
//                exit('<script>alert("请先关注“友意健康生活馆”"),window.close();</script>');
//            }
            $count = DB::table('users')->count();
            $data['openid'] = $userInfo['openid'];
            if (!empty($userInfo['nickname'])) {
                $data['nickname'] = trim($this->wx_expression($userInfo['nickname']));
                $data['name'] = $data['nickname'] . '_' . $count;
                $data['headimgurl'] = $userInfo['headimgurl'];
                $data['sex'] = $userInfo['sex'];
            }
            if ($tmp_user_info) {

                //更新
                unset($data['name']);
                DB::table('users')->where('id', $tmp_user_info->id)->update($data);
                $user_id = $tmp_user_info->id;
            } else {
                //注册添加
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $user_id = DB::table('users')->insertGetId($data);
                if (!$user_id) {
                    return false;
                }
            }
        } else {
            $user_id = $tmp_user_info->id;
        }
//        if((time() - $header_time) > 26000){
//            $this->downHeader($user_id, 1);//下载头像
//        }
        return DB::table('users')->where('id', $user_id)->first();
    }

    //过滤掉emoji表情
    function wx_expression($value)
    {
        $value = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {
            return '@E' . base64_encode($r[0]);
        }, $value);

        $countt = substr_count($value, "@");
        for ($i = 0; $i < $countt; $i++) {
            $c = stripos($value, "@");
            $value = substr($value, 0, $c) . substr($value, $c + 10, strlen($value) - 1);
        }
        $value = preg_replace_callback('/@E(.{6}==)/', function ($r) {
            return base64_decode($r[1]);
        }, $value);
        return $value;
    }

    //下载t头像到项目服务器
    protected function downHeader($id, $flg = 0)
    {
        $user_info = DB::table('users')->where('id', $id)->first();
        $header_time = !empty($user_info->header_time) ? $user_info->header_time : 0;
//        $url = $user_info->headimgurl;
        $url = 'http://wx.qlogo.cn/mmopen/XNB54PwzwCcRSzSsoVPQaX5JZGXpJ3h5UOpH7ATElj4LHoF5KbsYrskWNxL2vdMvRbF57VTRw1pTuseRmDtVtmIGCHeticrsk/0';
        if ($url == "") {
            return false;
        }
        if ($flg || ((time() - $header_time) > 189000)) {
            $filename = $id . '.jpg';
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
            if (file_exists('./uploads/header/' . $filename)) {
                unlink('./uploads/header/' . $filename);
            }
            $fp2 = fopen('./uploads/header/' . $filename, "a");
            if (fwrite($fp2, $img) === false) {
                return '下载用户头像失败: 无法写入图片';
            }
            fclose($fp2);
            $header = '/uploads/header/' . $filename;
            DB::table('users')->where('id', $id)->update(['header_time' => time()]);
        } else {
            $header = '/uploads/header/' . $id . '.jpg';
        }

        if (!$flg) {
            return $header;
        }
    }

    //下载二维码到项目服务器
    protected function downLoadQr($url, $id)
    {
        if ($url == "") {
            return false;
        }
        $filename = $id . '.jpg';
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        if (file_exists('./uploads/qrcode/' . $filename)) {
            unlink('./uploads/qrcode/' . $filename);
        }
        $fp2 = fopen('./uploads/qrcode/' . $filename, "a");
        if (fwrite($fp2, $img) === false) {
            return '下载用户推广二维码失败: 无法写入图片';
        }
        fclose($fp2);
        $qrcode = '/uploads/qrcode/' . $filename;
        return $qrcode;
    }

    //二维码与背景图合并
    public function mergeImage($qrcode, $header, $id)
    {
        //二维码合成
        //背景图
        $bg_img = 'http://' . $_SERVER['HTTP_HOST'] . '/img/gzwx.jpg';
        $filename = 'http://' . $_SERVER['HTTP_HOST'] . $qrcode;
        //缩放比例
        $per = 0.30;
        //原始宽高
        list($width, $height) = getimagesize($filename);
        //缩放后宽高
        $n_w = $width * $per;
        $n_h = $height * $per;
        //缩放后二维码
        $new = imagecreatetruecolor($n_w, $n_h);
        $img = imagecreatefromjpeg($filename);
        //copy部分图像并调整
        imagecopyresized($new, $img, 0, 0, 0, 0, $n_w, $n_h, $width, $height);
        $im1 = imagecreatefromjpeg($bg_img);
        //合并
        imagecopymerge($im1, $new, 144, 240, 0, 0, imagesx($new), imagesy($new), 100);
        //文字合成
        $user_info = DB::table('users')->where('id', $id)->first();
        $im = imagecreate(200, 200);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagecolortransparent($im, $white);  //imagecolortransparent() 设置具体某种颜色为透明色，若注释
        $black = imagecolorallocate($im, 255, 255, 255);
//        return $user_info->nickname;
        imagettftext($im, 14, 0, 5, 40, $black, './uploads/qrcode/simkai.ttf', $user_info->nickname); //字体设置部分linux和windows的路径可能不同
        //合并
        imagecopymerge($im1, $im, 3, 185, 0, 0, imagesx($im), imagesy($im), 100);
        //头像合并
        $filename = $filename = 'http://' . $_SERVER['HTTP_HOST'] . $header;
        $per = 0.13;
        list($width, $height) = getimagesize($filename);
        if ($width > 650 || $height > 650) {
            $per = 0.07;
        }
        $n_w = $width * $per;
        $n_h = $height * $per;
        $new = imagecreatetruecolor($n_w, $n_h);
        $img = imagecreatefromjpeg($filename);
        //copy部分图像并调整
        imagecopyresized($new, $img, 0, 0, 0, 0, $n_w, $n_h, $width, $height);
        //合并
        imagecopymerge($im1, $new, 5, 120, 0, 0, imagesx($new), imagesy($new), 100);
        //保存
        if (file_exists('./uploads/merge/' . $id . '.jpg')) {
            unlink('./uploads/merge/' . $id . '.jpg');
        }
        imagejpeg($im1, './uploads/merge/' . $id . '.jpg');
        //路径
        $path = '/uploads/merge/' . $id . '.jpg';
        return $path;
    }

    //下载二维码到微信服务器
    protected function uploadImage($object, $image)
    {
        if (class_exists('\CURLFile')) {
            $field = array('media' => new \CURLFile(realpath('.' . $image)));
        } else {
            $field = array('media' => '@' . realpath('.' . $image));
        }
//        $res = $this->weObj->uploadForeverMedia($field, 'image');//永久
        $res = $this->weObj->uploadMedia($field, 'image');//临时
        $media_id = $res['media_id'];
        return $media_id;
    }

    /**
     * 用户授权,默认获取详细信息
     */
    public function auth(Request $request)
    {
        $return_url = $request->get('return_url');
        $url = str_replace('/', '_', $return_url);
        $url = $this->weObj->getOauthRedirect(urlencode('http://' . $_SERVER['HTTP_HOST'] . '/openid_returnUrl/url/' . $url), 'lf', 'snsapi_userinfo');
        return redirect($url);
//        return redirect('openid_returnUrl/url/'.$url);
        //header('Location:' . $url);
    }

    /**
     * 用户授权回调地址
     */
    public function openid_returnUrl($url)
    {
        $url = $url = str_replace('_', '/', $url);
        $res = $this->weObj->getOauthAccessToken();
        $user_info = $this->getUserinfo('', $res['openid']);
//        $user_info = $this->weObj->getOauthUserinfo($res['access_token'], $res['openid']);
//        dd($user_info);
//        $user_info = User::find(41);
        if ($user_info) {
            $token = JWTAuth::fromUser($user_info);
//            dump($token);dd();
        } else {
            $token = '';
        }
        $data = [
            'token' => $token,
            'url' => $url
        ];
        //dd($data);

        return view('wxlogin')->with(compact('data'));
    }

    public function access_token()
    {
        $access_token = $this->weObj->checkAuth();
        dd($access_token);
    }

    /**
     * 接受文本处理
     */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    /*
     * 回复图片消息
     */

    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";
        $item_str = sprintf($itemTpl, $imageArray['MediaId']);
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    /**
     * 创建菜单
     *
     */
    public function creatMenus()
    {
        $data = array(
            'button' => array(
                0 => array(
                    'type' => 'view',
                    'name' => '商城入口',
                    'url' => 'http://smyb05.huiyaoba.com/',
                ),
                1 => array(
                    'name' => '操作',
                    'sub_button' => array(
                        0 => array(
                            'type' => 'click',
                            'name' => '推广二维码',
                            'key' => 'QRCode',
                        ),
                        1 => array(
                            'type' => 'view',
                            'name' => '我的中心',
                            'url' => 'http://smyb05.huiyaoba.com/center',
                        ),
//                        2 => array(
//                            'type' => 'view',
//                            'name' => '查看我的订单',
//                            'url' => 'http://smyb05.huiyaoba.com/',ymt135@$^
//                        )
                    ),
                ),
                2 => array(
                    'type' => 'view',
                    'name' => '联系客服',
                    'url' => 'https://youyijiankang.qiyukf.com/client?k=662ad898f35db70353eb453b140ca343&wp=1',
                )
            ),
        );
        if ($this->weObj->createMenu($data)) {
            echo 'success';
        } else {
            echo 'false';
        }
    }
}
