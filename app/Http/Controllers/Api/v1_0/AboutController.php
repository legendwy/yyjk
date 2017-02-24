<?php

namespace App\Http\Controllers\Api\v1_0;

use App\Http\Controllers\Api\BaseController;
use App\Models\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AboutController extends BaseController
{
    /**
     * 个人中心->联系我们
     * @return mixed
     * @author fangweibo
     */
    public function about()
    {
        $intro = \DB::table('config')->whereIn('id',[10,11,12,13,14,20])->select('remark','value')->get();

        if($intro){
            return $this->returnMsg(true,0,'查询成功',$intro);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

    /**
     * 设置中心->客服热线
     * @return mixed
     * @author fangweibo
     */
    public function hotTel()
    {
        $tel = \DB::table('config')->where('remark','客服热线')->select('remark','value')->get();

        if($tel){
            return $this->returnMsg(true,0,'查询成功',$tel);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

    /**
     * 帮助中心
     * @return mixed
     * @author fangweibo
     */
    public function helpList()
    {
        $data = \DB::table('article')->where('type_id',2)
            ->select('id','title','desc','created_at')
            ->orderBy('sort')->paginate(15);

        if($data){
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

    /**
     * 系统公告
     * @return mixed
     * @author fangweibo
     */
    public function notice()
    {
        $data = \DB::table('article')->where('type_id',1)
            ->orderBy('created_at','desc')
            ->select('id','title','desc','created_at')->paginate(15);

        if($data){
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

    /**
     * 首页公告(轮播)
     * @return mixed
     * @author fangweibo
     */
    public function indexNotice()
    {
        $data = \DB::table('article')->where('type_id',1)
            ->orderBy('created_at','desc')
            ->select('id','title')->limit(3)->get();

        if($data){
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

    /**
     * 公告详情
     * @param $id
     * @return mixed
     * @author fangweibo
     */
    public function noticeDetail(Request $request)
    {
        $data = \DB::table('article')->where('id',$request->input('id'))
            ->select('id','title','content','created_at')->first();
//        return gettype($data);
        if($data){
            $data->created_at = date('Y-m-d',strtotime($data->created_at));
//            $host = $_SERVER['APP_URL'];
            $host = "http://".$_SERVER['HTTP_HOST'];
            $content = $data->content;
            $data->content = preg_replace('#src="/#is', 'src="'.$host.'/', $content);
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

    /**
     * 提现说明
     * @return mixed
     * @author fangweibo
     */
    public function withdraw()
    {
        $data = \DB::table('article')->where('id',5)
            ->select('title','content')
            ->get();

        if($data){
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

    /**
     * 获取省市区信息
     * @param Request $request
     * @return mixed
     * @author fangweibo
     */
    public function regionList(Request $request)
    {
        $pid = $request->input('pid');
        if(!$pid){
            $data = \DB::table('region')->where('parent_id',1)->get();
        }else{
            $data = \DB::table('region')->where('parent_id',$pid)->get();
        }

        if($data->toArray()){
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,422,'暂无数据',[],422);
        }
    }


    /**
     * 奖励规则
     * @return mixed
     * @author fangweibo
     */
    public function bonus()
    {
        $data = \DB::table('article')->where('id',6)
            ->select('title','content')
            ->get();

        if($data){
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,422,'查询失败',[],422);
        }
    }

}
