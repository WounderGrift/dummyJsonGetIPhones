<?php

namespace Modules\ProfilePage\App\Http\Interfaces;

use Illuminate\Http\Request;

interface EditProfilePageInterface
{
    public function index($cid);
    public function update(Request $request);
}
