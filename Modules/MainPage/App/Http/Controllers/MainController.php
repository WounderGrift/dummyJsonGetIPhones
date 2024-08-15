<?php

namespace Modules\MainPage\App\Http\Controllers;

use App\Models\Product;
use Modules\MainPage\App\Http\AbstractClasses\MainPageAbstract;
use Modules\MainPage\App\Http\Interfaces\MainPageInterface;

class MainController extends MainPageAbstract implements MainPageInterface
{
    public function index($category = null)
    {
        $title  = 'ПРЕДПОЛОЖИМ КАТАЛОГ';
        $route  = 'all.index.category';
        $jsFile = 'modules/mainpage/resources/assets/js/page/main.js';

        if (!Product::query()->exists()) {
            parent::fetchIphonesAndSave();
        }

        $data = Product::all();
        return view('mainPage::grid', compact('title', 'data', 'route', 'jsFile'));
    }
}
