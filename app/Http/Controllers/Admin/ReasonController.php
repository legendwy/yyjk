<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReasonRequest;
use App\Repositories\Eloquent\ReasonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReasonController extends Controller
{
    protected $reason;

    public function __construct(ReasonRepository $reason)
    {
        $this->middleware('check.permission:reason');
        $this->reason = $reason;
    }

    /**
     * 获取列表
     * @param Request $request
     * @return $this
     * @author: simayubo
     */
    public function index(Request $request)
    {
        $list = $this->reason->getList($request);

        return view('admin.reason.list')->with(compact('list'));
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: simayubo
     */
    public function create()
    {
        return view('admin.reason.create');
    }

    /**
     * 添加验证
     * @param ReasonRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author: simayubo
     */
    public function store(ReasonRequest $request)
    {
        if ($this->reason->create($request->all())){
            flash('添加成功', 'success');
        }else{
            flash('添加失败', 'error');
        }
        return redirect('admin/reason');
    }

    /**
     * 编辑
     * @param $id
     * @return $this
     * @author: simayubo
     */
    public function edit($id)
    {
        $info = $this->reason->find($id);

        return view('admin.reason.edit')->with(compact('info'));
    }

    /**
     * 更新
     * @param ReasonRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author: simayubo
     */
    public function update(ReasonRequest $request, $id)
    {
        $this->reason->updateReason($request, $id);
        return redirect('admin/reason');
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author: simayubo
     */
    public function destroy($id)
    {
        $this->reason->destroy($id);
        return redirect('admin/reason');
    }
}
