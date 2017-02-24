<html>
<body>
<script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall() {
        WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                {!! $jsApiParameters !!},
                function (res) {
//                    alert(res.err_code+res.err_desc+res.err_msg);
                    //如果支付成功
                    if (res.err_msg == 'get_brand_wcpay_request:ok') {
                        //支付成功后跳转的地址
                        location.href = '/index.php/Home/User/index';
                    } else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                        alert('请尽快完成支付哦！');
                    } else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                        alert('支付失败');
                    } else {
                        alert('意外错误');
                    }
                }
        );
    }
    function callpay() {
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        } else {
            jsApiCall();
        }
    }
</script>
<style>
    .z_zf01 {
        margin: 20px auto;
        width: 100px;
        display: block;
        border: none;
        margin: 0 auto;
        font-size: 16px;
        line-height: 30px;
        color: #fff;
        text-align: center;
        font-weight: bold;
        background-color: #62b900;
        border-radius: 5px;
    }
    dd {
        text-align: center;
        margin: 20px auto;
    }

</style>
<br/><br/><br/><br/><br/>
<input type="button" value="微信支付" onclick="callpay();" class="z_zf01"/>
</body>
</html>
