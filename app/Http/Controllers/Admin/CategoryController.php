<?php



namespace App\Http\Controllers\Admin;



use App\Repositories\Eloquent\CategoryRepository;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



class CategoryController extends Controller

{

    protected $category;



    public function __construct(CategoryRepository $category)

    {

        $this->middleware('check.permission:category');

        $this->category = $category;

    }



    /**

     * 分类列表

     * @return $this

     * @author: simayubo

     */

    public function index()

    {

        $list = $this->category->getCategoryList();

        $category = $this->category->getCategories();

        return view('admin.category.list')->with(compact('list', 'category'));

    }



    /**

     * 根据分类id获取子分类列表

     * @param $id

     * @return mixed

     * @author: simayubo

     */

    public function getCategoryListById($id){

        $list = $this->category->getCategoryList($id);

        if (empty($list)){

            return ['status' => 'no list'];

        }

        return ['status' => 'success', 'list' => $list];

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {



    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $rules = [
            'category_name'  =>  'required|unique:category,category_name',
            'parent_id' =>  'required',
            'info' =>  'required',
            'mobile_name' =>  'required|unique:category,mobile_name',
        ];
        $message = [
            'category_name.required'    =>  '分类名称不能为空',
            'category_name.unique'      =>  '分类名已存在',
            'info.required'    =>  '分类介绍不能为空',
            'mobile_name.required'    =>  '移动端名称不能为空',
            'mobile_name.unique'    =>  '移动端名称已存在',
            'parent_id.required'        =>  '上级分类必填'
        ];
        if ($request->input('parent_id') != 0){
            $rules['icon'] = 'required';
            $message['icon.required'] = '图标不能为空';
        }
        $this->validate($request, $rules, $message);
        $this->category->addCategory($request);
        return redirect('admin/category');

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

        $info = $this->category->find($id)->toArray();

        $category = $this->category->getCategories();

//        dd($category);



        return view('admin.category.edit')->with(compact('info', 'category'));



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

            'category_name'  =>  'required|unique:category,category_name,'.$id,

            'parent_id' =>  'required',

            'info' =>  'required',

            'mobile_name' =>  'required|unique:category,mobile_name,'.$id,

        ];

        $message = [

            'category_name.required'    =>  '分类名称不能为空',

            'info.required'    =>  '分类介绍不能为空',

            'mobile_name.required'    =>  '移动端名称不能为空',

            'mobile_name.unique'    =>  '移动端名称已存在',

            'category_name.unique'      =>  '分类名已存在',

            'parent_id.required'        =>  '上级分类必填'

        ];

        $this->validate($request, $rules, $message);

        $this->category->updateCategory($request);

        return redirect('admin/category');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $resault = $this->category->destoryCategory($id);

        if ($resault == 1){

            return ['status' => 'success'];

        }elseif($resault == -2){

            return ['status' => 'fail', 'msg' => '存在下级分类，清先处理下级分类'];

        }else{

            return ['status' => 'fail', 'msg' => '删除失败  '];

        }

    }

}

