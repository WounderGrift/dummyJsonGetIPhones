<?php

namespace Modules\MainPage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\TelegramLogHelper;
use App\Models\Categories;
use App\Models\Game;

use App\Models\Newsletter;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\MainPage\App\Http\Interfaces\WishlistGameInterface;

class WishlistGameController extends Controller implements WishlistGameInterface
{
    public function index($category = null)
    {
        $title = 'ЖЕЛАЕМЫЕ';
        $inWishlistPage = true;
        $route = 'wishlist.index.category';

        $games = Game::query()->select('game.*')
            ->rightJoin('wishlist', function ($join) {
                $join->on('wishlist.game_id', '=', 'game.id')
                    ->where('wishlist.user_id', '=', Auth::user()->id);
            })->where(function ($query) {
                $query->where('status', Game::STATUS_PUBLISHED);
                if (Auth::check() && Auth::user()->checkOwnerOrAdmin()) {
                    $query->orWhere('status', Game::STATUS_UNPUBLISHED);
                }
            });

        $gamesCopy = clone $games;
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

        $games  = $games->paginate(28);
        $jsFile = null;

        return view('mainpage::grid', compact('title', 'inWishlistPage',
            'route', 'jsFile', 'games', 'categories'));
    }

    public function toggleWishlist (Request $request): JsonResponse
    {
        if (!$request->user())
            return response()->json(['message' => 'Forbidden'], 403);

        $data = $request->validate([
            'game_id' => ['string'],
            'toggleWishlist'  => ['boolean'],
        ]);

        $data['game_id'] = base64_decode($data['game_id']);
        if (!Game::query()->where('id', $data['game_id'])->exists())
            return response()->json(['message' => 'Недопустимый ID игры'], 403);

        $game = Game::findOrFail($data['game_id']);

        if ($data['toggleWishlist']) {
            $add = [
                'game_id' => $data['game_id'],
                'user_id' => $request->user()->id,
            ];

            $wishitem = Wishlist::firstOrcreate($add, $add);

            $newsletterData = [
                'user_id' => $request->user()->id,
                'game_id' => $data['game_id'],
                'email'   => $request->user()->email
            ];

            Newsletter::firstOrCreate($newsletterData, $newsletterData);

            TelegramLogHelper::reportToggleWishlist($request->user(), $game, true, !$wishitem);
            return response()->json(['bool' => true]);
        } else {
            $wishitem = Wishlist::where('game_id', $data['game_id'])->where('user_id', $request->user()->id);
            TelegramLogHelper::reportToggleWishlist($request->user(), $game, false, !$wishitem->delete());
            return response()->json(['bool' => false]);
        }
    }
}
