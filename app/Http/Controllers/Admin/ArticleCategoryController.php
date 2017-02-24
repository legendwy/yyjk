<?php

namespace App\Http\Controllers\admin;

use App\Repositories\Eloquent\ArticleCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleCategoryController extends Controller
{

    protected $articleCategory;
    public function __construct(ArticleCategoryRepository $articleCategory)
    {
        $this->middleware('check.permission:articleCategory');
        $this->articleCategory = $articleCategory;
    }

    /**
     * 文章分类列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author wangyan
     */
    public function index()
    {
        $list = $this->articleCategory->getCategoryList();
        return view('admin.article.category',['list' => $list]);
    }

    /**
     * 添加文章分类
     * @Author wangyan
     */
    public function create()
    {
        return view('admin.article.category_add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required'
        ],[
           'name.required' => '分类名称不能为空'
        ]);
        $this->articleCategory->addCategory($request);
        return redirect('admin/article_category');
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
        $category = $this->articleCategory->getCategoryInfo($id);
        return view('admin.article.category_edit',['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {		$this->validate($request,[            'name' => 'required'        ],[            'name.required' => '分类名称不能为空'        ]);		
        $this->articleCategory->updateCategory($request, $id);
        return redirect('admin/article_category');
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
