<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\ImageRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    protected $image;
    public function __construct(ImageRepository $image)
    {
        $this->middleware('check.permission:image');
        $this->image = $image;
    }

    /**
     * 图片列表
     * @Author wangyan
     */
    public function index()
    {
        $list = $this->image->getImageList();
        return view('admin.ad.image')->with(compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $image = $this->image->getImageById($id);
        return view('admin.ad.image_edit')->with(compact('image'));
    }

    /**
     * 修改图片
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author wangyan
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'size' => 'required'
        ],[
            'size.required' => '尺寸不能为空'
        ]);
        $this->image->updateImageById($request, $id);
        return redirect('admin/image');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
