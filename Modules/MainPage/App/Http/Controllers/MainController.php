<?php

namespace Modules\MainPage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MainPage\App\Http\Interfaces\MainPageInterface;

class MainController extends Controller implements MainPageInterface
{
    public function index($category = null)
    {
        $title  = 'ПРЕДПОЛОЖИМ КАТАЛОГ';
        $route  = 'all.index.category';
        $jsFile = 'modules/mainpage/resources/assets/js/page/main.js';

        return view('mainPage::grid', compact('title', 'route', 'jsFile'));
    }
}
