<?php

namespace App\Http\Controllers\Api\v1_0;

use App\Http\Controllers\Api\BaseController;
use App\Models\SearchHistory;
use JWTAuth;

class SearchController extends BaseController
{
    /**
     * 获取列表
     * @author: simayubo
     */
    public function index()
    {
        $user = JWTAuth::toUser();
        $list = SearchHistory::where('uid', $user->id)
            ->take(20)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return $this->returnMsg(true, 0, 'success', $list);
    }

    /**
     * 删除
     * @author: simayubo
     */
    public function destroy()
    {
        $user = JWTAuth::toUser();
        $rows = \DB::table('search_history')->where('uid', $user->id)->delete();
        if ($rows >= 0){
            return $this->returnMsg(true, 0, '删除成功');
        }else{
            return $this->returnMsg(false, 101, '删除失败');
        }
    }
}
