<?php

namespace Modules\ProfilePage\App\Http\Interfaces;

use Illuminate\Http\Request;

interface ProfilePageInterface
{
    public function index($cid = null);
    public function create(Request $request);
    public function login(Request $request);
    public function logout();
    public function banned(Request $request);
}
