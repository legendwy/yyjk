<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\AdRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GuangController extends Controller
{
    protected $ad;
    public function __construct(AdRepository $ad)
    {
        $this->middleware('check.permission:ad');
        $this->ad = $ad;
    }

    /**
     * 广告位置列表
     * @Author wangyan
     */
    public function index()
    {
        $position = \DB::table('ad_position')->get();
        return view('admin.ad.list')->with(compact('position'));
    }



    /**
     * 图片列表
     * @param $id
     * @Author wangyan
     */
    public function guang($id){
        $list = $this->ad->getAdByPositionId($id);
        return view('admin.ad.ad')->with(compact('list','id'));
    }

    /**
     * 图片添加页面
     * @param Request $request
     * @Author wangyan
     */
    public function create(Request $request)
    {
        $position_id=  $request->get('position_id');
        return view('admin.ad.create')->with(compact('position_id'));
    }

    /**
     * 添加
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author wangyan
     */
    public function store(Request $request)
    {
        $position_id = $request->get('position_id');
        $this->validate($request,[
            'sort' => 'required'
        ],[
            'sort.required' => '排序不能为空'
        ]);
        $this->ad->addAd($request);
        return redirect('admin/guang/guang/'.$position_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * 修改图片
     * @param $id
     * @Author wangyan
     */
    public function edit($id)
    {
        $ad = $this->ad->getAdById($id);
        return view('admin.ad.ad_edit')->with(compact('ad'));
    }

    /**修改
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author wangyan
     */
    public function update(Request $request, $id)
    {
        $position_id = $this->ad->getAdById($id);
        $this->validate($request,[
            'sort' => 'required'
        ],[
            'sort.required' => '排序不能为空'
        ]);
        $this->ad->updateAdById($request, $id);
        return redirect('admin/guang/guang/'.$position_id['position_id']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $position_id = $this->ad->getAdById($id);
        $this->ad->destroyAdById($id);
        return redirect('admin/guang/guang/'.$position_id['position_id']);
    }
}
