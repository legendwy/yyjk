<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\SysRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SysController extends Controller
{
    protected $sys;
    public function __construct(SysRepository $sys)
    {
        $this->middleware('check.permission:sys');
        $this->sys = $sys;
    }


    /**
     * 配置列表
     * @Author wangyan
     */
    public function index()
    {
        $config = $this->sys->getSysList();
//        return $config;
        return view('admin.sys.list')->with(compact('config'));
    }

    public function updateSys(Request $request){
        $this->sys->updateSys($request);
        return redirect('admin/sys');

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
