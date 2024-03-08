<?php

namespace Modules\MainPage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Game;
use Modules\MainPage\App\Http\Interfaces\MainPageInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AllGamesController extends Controller implements MainPageInterface
{
    public function index($category = null)
    {
        $title  = 'ВСЕ ИГРЫ';
        $route  = 'all.index.category';
        $jsFile = 'modules/mainpage/resources/assets/js/page/all.js';

        $games = Game::query()->select('game.*')
            ->where('game.is_soft', 0)
            ->where('game.is_waiting', 0)
            ->where('game.status', Game::STATUS_PUBLISHED);

        $gamesCopy  = clone $games;
        $categories = $gamesCopy->select('categories.label', 'categories.url')
            ->leftJoin('games_categories_link', 'games_categories_link.game_id', '=', 'game.id')
            ->leftJoin('categories', 'categories.id', '=', 'games_categories_link.category_id')
            ->orderBy('categories.label', 'DESC')
            ->groupBy('categories.label', 'categories.url')
            ->distinct()
            ->pluck('categories.label', 'categories.url')
            ->filter(function ($item) {
                return !empty($item);
            });

        if (isset($category)) {
            $games->leftJoin('games_categories_link', 'games_categories_link.game_id', '=', 'game.id')
                ->leftJoin('categories', 'categories.id', '=', 'games_categories_link.category_id')
                ->where('categories.url', $category);

            $label = Categories::where('url', $category)->value('label');
            if ($label)
                $title .= ', ' . mb_strtoupper($label);
        }

        $games = $games->orderBy('game.is_sponsor', 'DESC')
            ->orderByRaw('STR_TO_DATE(game.date_release, "%d %M %Y") DESC')->paginate(28);

        if ($games->isEmpty())
            throw new NotFoundHttpException();

        return view('mainpage::grid', compact('title', 'route',
            'jsFile', 'games', 'categories'));
    }
}
