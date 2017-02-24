<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\FeedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    protected $feedback;

    public function __construct(FeedbackRepository $feedback){
        $this->feedback = $feedback;
    }

    /**
     * Display a listing of the resource.
     * 反馈信息列表
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = \DB::table('feedback')->get();
        return view('admin.feedback.list')->with(compact('data'));
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
     * 删除反馈信息
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->feedback->delete($id);
        if($result){
            return ['status'=>'success'];
        }else{
            return ['status'=>'false','msg'=>'删除失败'];
        }
    }

    /**
     * 处理反馈信息
     * @param Request $request
     * @return array
     * @author fangweibo
     */
    public function deal(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');

        $result = $this->feedback->changeStatus($id,$status);
        if($result){
            return ['status'=>'success'];
        }else{
            return ['status'=>'false','msg'=>'操作失败'];
        }
    }
}
