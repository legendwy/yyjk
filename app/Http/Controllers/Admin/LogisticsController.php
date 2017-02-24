<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LogisticsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\LogisticsRepository;

class LogisticsController extends Controller
{
    protected $logistics;

    public function __construct(LogisticsRepository $logistics)
    {
        $this->middleware('check.permission:logistics');
        $this->logistics = $logistics;
    }

    /**
     * 获取物流公司列表
     * @param Request $request
     * @return $this
     * @author: simayubo
     */
    public function index(Request $request)
    {
        $list = $this->logistics->getList($request);
        return view('admin.logistics.list')->with(compact('list'));
    }

    /**
     * 添加物流公司
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: simayubo
     */
    public function create()
    {
        return view('admin.logistics.create');
    }

    /**
     * 验证添加
     * @param LogisticsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author: simayubo
     */
    public function store(LogisticsRequest $request)
    {
        if ($this->logistics->create($request->all())){
            flash('物流公司添加成功', 'success');
        }else{
            flash('物流公司添加失败', 'error');
        }
        return redirect('admin/logistics');
    }

    /**
     * 编辑
     * @param $id
     * @return $this
     * @author: simayubo
     */
    public function edit($id)
    {
        $info = $this->logistics->find($id);

        return view('admin.logistics.edit')->with(compact('info'));
    }

    /**
     * 编辑
     * @param LogisticsRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author: simayubo
     */
    public function update(LogisticsRequest $request, $id)
    {
        $this->logistics->updateLogistics($request, $id);
        return redirect('admin/logistics');
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author: simayubo
     */
    public function destroy($id)
    {
        $this->logistics->destroy($id);
        return redirect('admin/logistics');
    }
}
