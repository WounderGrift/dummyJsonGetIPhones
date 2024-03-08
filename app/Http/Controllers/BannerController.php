<?php

namespace App\Http\Controllers;

use App\Http\Helpers\FileHelper;
use App\Models\Banners;
use App\Models\BannerStatistics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function indexBigBanner()
    {
        $title = 'Настройка Большого Баннер Меню';
        $inBigBannerPage = true;

        $banners = Banners::withTrashed()->where('type', 'big_banner_menu')
            ->orderBy('banners.position', 'ASC')->get();

        $mimeTypeBanner = implode(', ', FileHelper::ACCESS_BANNER_MIME_TYPE);

        return view('owner.banner', compact('title', 'inBigBannerPage',
            'banners', 'mimeTypeBanner'));
    }

    public function indexLittleBanner()
    {
        $title = 'Настройка Маленького Баннер Меню';
        $inOwnerPanel = true;
        $inLittleBannerPage = true;

        $banners = Banners::withTrashed()->where('type', 'little_banner_menu')
            ->orderBy('banners.position', 'ASC')->get();

        $mimeTypeBanner = implode(', ', FileHelper::ACCESS_BANNER_MIME_TYPE);

        return view('owner.banner', compact('title', 'inOwnerPanel',
            'inLittleBannerPage', 'banners', 'mimeTypeBanner'));
    }

    public function indexDetailBanner()
    {
        $title = 'Настройка Баннер Детали';
        $inOwnerPanel = true;
        $inDetailBannerPage = true;

        $banners = Banners::withTrashed()->where('type', 'detail_banner')
            ->orderBy('banners.position', 'ASC')->get();

        $mimeTypeBanner = implode(', ', FileHelper::ACCESS_BANNER_MIME_TYPE);

        return view('owner.banner', compact('title', 'inOwnerPanel',
            'inDetailBannerPage', 'banners', 'mimeTypeBanner'));
    }

    public function indexBasementBanner()
    {
        $title = 'Настройка Баннер Подвала';
        $inOwnerPanel = true;
        $inBasementBannerPage = true;

        $banners = Banners::withTrashed()->where('type', 'basement_banner')
            ->orderBy('banners.position', 'ASC')->get();

        $mimeTypeBanner = implode(', ', FileHelper::ACCESS_BANNER_MIME_TYPE);

        return view('owner.banner', compact('title', 'inOwnerPanel',
            'inBasementBannerPage', 'banners', 'mimeTypeBanner'));
    }

    public function bannerJump(Request $request): bool|JsonResponse
    {
        $code = $request->input('id');
        $bannerId = base64_decode($code);
        $banner   = Banners::find($bannerId);

        if (empty($banner->href))
            return false;

        if ($banner) {
            if ($banner->active) {
                BannerStatistics::create([
                    'banner_id' => $bannerId,
                    'user_id' => $request->user()->id ?? null,
                ]);
            }

            return response()->json(['redirect_url' => $banner->href]);
        }

        return false;
    }

    public function bannersSave(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $addBanner = $request->input('bannerNew');
            $removeBanner = $request->input('bannerOldRemove');
            $additionalBanner = $request->input('bannerAdditional');
            $typeBanner = $request->input('typeBanner');

            if ($addBanner)
                BannerHelper::createBanner($addBanner, $typeBanner);

            if ($removeBanner)
                BannerHelper::removeBannerSoft($removeBanner);

            if ($additionalBanner)
                BannerHelper::setOptionBanner($additionalBanner);

            $route = [
                'big_banner_menu'    => route('big-banner.index'),
                'little_banner_menu' => route('little-banner.index'),
                'detail_banner'      => route('detail-banner.index'),
                'basement_banner'    => route('basement-banner.index')
            ];

            DB::commit();
            return response()->json(['redirect_url' => $route[$typeBanner]]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Произошла ошибка при добавлении баннера: '
                . $e->getMessage()], 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function bannerRemoveSoftly(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $removeBigBannerMenu = $request->input('bannerId');
            $result = false;

            if ($removeBigBannerMenu)
                $result = BannerHelper::removeBannerSoft($removeBigBannerMenu);

            DB::commit();
            return response()->json(['success' => true, 'isDeleted' => $result]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Произошла ошибка при мягком удалении баннера: '
                . $e->getMessage()], 400);
        }
    }

    public function bannerRemoveForced(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $bannerId = $request->input('bannerId');

            if ($bannerId) {
                $banner = Banners::withTrashed()->find($bannerId);
                if ($banner->banner_path && Storage::disk('public')->exists($banner->banner_path))
                    Storage::disk('public')->delete($banner->banner_path);

                if ($banner->bannersStatistics) {
                    foreach ($banner->bannersStatistics as $statistic)
                        $statistic->forceDelete();
                }
                $banner->forceDelete();
            }

            $bannerUrl = $request->input('bannerUrl');
            $bannerUrl = str_replace("/storage", "", $bannerUrl);

            if ($bannerUrl) {
                Storage::disk('public')->delete($bannerUrl);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Произошла ошибка при удалении баннера: '
                . $e->getMessage()], 400);
        }
    }

    public function bannerActivate(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $bannerId = $request->input('bannerId');
            $banner   = Banners::find($bannerId);

            $banner->update(['active' => !$banner->active]);

            DB::commit();
            return response()->json(['success' => true, 'active' => $banner->active]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Произошла ошибка при активации баннера: '
                . $e->getMessage()], 400);
        }
    }
}
