<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Eloquent\MenuRepository;

class MenuComposer
{
    /**
     * The user repository implementation.
     *
     * @var MenuRepository
     */
    protected $menus;

    /**
     * Create a new profile composer.
     *
     * @param  MenuRepository  $users
     * @return void
     */
    public function __construct(MenuRepository $menu)
    {
        // Dependencies automatically resolved by service container...
        $this->menus = $menu;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $menu = $this->menus->getMenuList();

        foreach ($menu as $key => $item) {
            $value = $item['child'];
            foreach ($value as $_key => $_item) {
                if (!request()->user('admin')->can($_item['slug'])){
                    unset($value[$_key]);
                }
            }
            if (empty($value)){
                $menu[$key]['child'] = [];
            }else{
                $menu[$key]['child'] = $value;
            }
        }
//        dd($menu);
        $view->with('sidebarMenus', $menu);
    }
}