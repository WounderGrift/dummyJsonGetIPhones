<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StringToNumberController extends Controller
{
    const MAX = 100;
    const MIN = 1000;

    public function convert(Request $request)
    {
        $validated = $request->validate([
            'input_string' => 'required|string|max:32',
        ]);

        $hash = crc32($validated['input_string']);
        $title = self::MIN + ($hash % (self::MAX - self::MIN + 1));

        return view('mainPage::grid', compact('title',));
    }
}
