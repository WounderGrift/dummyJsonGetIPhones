<?php

namespace Modules\ProfilePage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Harvester;

class OwnerController extends Controller
{
    public function index($hideBroken = false, $sourced = 'null')
    {
        $inOwnerPanel = true;
        $query = Harvester::query();

        if ($hideBroken) {
            $query->where('status', '!=', 'broken');
        }

        if ($sourced !== 'null') {
            $query->where('source', $sourced);
        }

        $harvesting = $query->orderBy('created_at', 'DESC')->paginate(28);

        return view('profilePage::profiles', compact('inOwnerPanel', 'harvesting',
            'hideBroken', 'sourced'));
    }
}
