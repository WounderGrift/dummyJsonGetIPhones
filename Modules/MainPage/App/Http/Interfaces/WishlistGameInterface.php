<?php

namespace Modules\MainPage\App\Http\Interfaces;

use Illuminate\Http\Request;

interface WishlistGameInterface
{
    public function index($category = null);
    public function toggleWishlist (Request $request);
}
