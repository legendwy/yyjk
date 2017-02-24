<?php



namespace App\Http\Controllers\Web;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



class HomeController extends Controller

{

    public function index()

    {

        return view('web.index.index');

    }



    /**

     * 发送手机验证码

     * User: lf

     * @param $phone

     * @param $name

     * @param $code

     * @return bool

     */

    public function getPhone($phone, $name, $code)

    {
        error_reporting(0);
        $url = 'http://www.ztsms.cn/sendSms.do?username=15303819966&password=' . 'Yyjk112233' . '&mobile=' . $phone . '&content=你的验证码为'.$code.'，请勿告诉他人！【有益健康】&productid=676767&xh=';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close($ch);
        $resault_array = explode(',', $output);
        if($resault_array[0] == '1'){
            return true;
        }else{
            return false;
        }

    }

}

