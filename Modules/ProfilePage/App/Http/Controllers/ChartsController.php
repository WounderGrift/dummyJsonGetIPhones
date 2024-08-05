<?php

namespace Modules\ProfilePage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartsController extends Controller
{
    public function profilesTable($search = null)
    {
        $inOwnerPanel = true;
        $title = $search ? "Статистика - Профили, $search" : "Статистика - Профили";

        $profiles = Users::select('name', 'cid', 'avatar_path', 'avatar_name', 'is_banned',
            'last_activity', 'created_at', 'updated_at');

        if ($search) {
            $profiles = $profiles->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('cid', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        $profiles = $profiles->orderBy('id', 'DESC')->paginate(28);
        return view('profilePage::profiles', compact('inOwnerPanel', 'title',
            'profiles'));
    }

    public function profilesChart(Request $request): JsonResponse
    {
        $startDateType = $request->input('startDate');
        $endDate = now()->endOfDay();

        $groupedProfiles = Users::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as x'),
            DB::raw('COUNT(*) as y')
        );

        $startDate = match ($startDateType) {
            '7Д'    => now()->startOfDay()->subDays(7),
            '1МЕС'  => now()->startOfDay()->subMonth(),
            '1ГОД'  => now()->startOfDay()->subYear(),
            '5ЛЕТ'  => now()->startOfDay()->subYears(5),
            default => null,
        };

        $resultsArray = [];
        if (isset($startDate))
            $groupedProfiles->whereBetween('created_at', [$startDate, $endDate]);

        $groupedProfiles->orderBy('created_at')->groupBy('x')
            ->chunk(100, function ($results) use (&$resultsArray) {
            foreach ($results as $result) {
                $resultsArray[] = $result->toArray();
            }
        });

        return response()->json(['data' => $resultsArray]);
    }
}
