<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Series;
use Illuminate\Http\Request;

class SearchGamesController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $title = "Поиск $query";
        $justSearch = true;

        $games = Game::where('game.name', 'like',  "%{$query}%")
            ->where('status', Game::STATUS_PUBLISHED)
            ->orderByRaw('STR_TO_DATE(game.date_release, "%d %M %Y") DESC')
            ->paginate(28);

        $categories = $route = $jsFile = null;
        return view('grid', compact('title', 'route', 'jsFile',
            'games', 'categories', 'justSearch'));
    }

    public function searchForUnpublished(Request $request)
    {
        $query = $request->input('query');
        $title = "Поиск $query по неопубликованным";
        $inOwnerPanel = true;
        $isUnpublished = true;
        $isUnpublishedSearch = true;

        $games = Game::where('game.name', 'like', "%{$query}%")
            ->where('status', Game::STATUS_UNPUBLISHED)
            ->orderByRaw('STR_TO_DATE(game.date_release, "%d %M %Y") DESC')
            ->paginate(28);

        $categories = $route = $jsFile = null;
        return view('owner.grid', compact('title', 'route', 'jsFile',
            'isUnpublishedSearch', 'isUnpublished', 'inOwnerPanel', 'games', 'categories'));
    }

    public function searchForSeries(Request $request)
    {
        $query = $request->input('query');
        $title = "Поиск $query по сериям";
        $isSeries = true;
        $isSeriesSearch = true;

        $series = Series::where('uri', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")->paginate(28);

        $categories = $route = $jsFile = null;
        return view('series', compact('title', 'route', 'jsFile',
            'isSeriesSearch', 'isSeries', 'series', 'categories'));
    }
}
