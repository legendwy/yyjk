<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\ArticleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{

    protected $article;
    public function __construct(ArticleRepository $article)
    {
        $this->middleware('check.permission:article');
        $this->article = $article;
    }

    /**
     * 文章列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author wangyan
     */
    public function index()
    {
        $list = $this->article->getArticleList();
        return view('admin.article.article',['article' => $list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = \DB::table('article_category')->whereBetween('id',['1',2])->get();
        return view('admin.article.article_add', ['category' => $category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleRequest $request)
    {
        $this->article->addArticle($request);
        return redirect('admin/article');

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
     */
    public function edit($id)
    {
        $article = $this->article->getArticleInfo($id);
		$article->type_name = \DB::table('article_category')->where(['id' => $article->type_id])->value('name');
        $category = \DB::table('article_category')->get()->toArray();

        return view('admin.article.article_edit')->with(compact('category', 'article'));
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
	$rules = [            
		'title'   => 'required',               
		'sort'    => 'required',            
		'desc'    => 'required',            
		'content' => 'required',        
	];       
	$message = [            
		'title.required'    =>  '文章标题不能为空',                      
		'sort.required'     =>  '排序不能为空',            
		'desc.required'     =>  '文章摘要不能为空',            
		'content.required'  =>  '文章内容不能为空',        
	];        
	$this->validate($request, $rules, $message);
	$this->article->updateArticle($request, $id);
	return redirect('admin/article');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->article->destroyArticle($id);
        return redirect('admin/article');
    }
}
