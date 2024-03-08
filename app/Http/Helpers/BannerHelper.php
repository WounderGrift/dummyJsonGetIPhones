<?php

namespace App\Http\Helpers;

use App\Models\Banners;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerHelper
{
    public static function createBanner($addBanner, string $type): void
    {
        foreach ($addBanner as $id => $banner)
        {
            if ($banner['type'] == 'video') {
                $bannerName = Str::random(12) . '.webm';
                $bannerPath = "banners/" . $bannerName;
            } elseif ($banner['type'] == 'image') {
                $bannerName = Str::random(12) . '.png';
                $bannerPath = "banners/" . $bannerName;
            }

            if (!isset($bannerName) || !isset($bannerPath))
                return;

            $base64ImageWithoutPrefix = substr($banner['result'], strpos($banner['result'], ',') + 1);

            if (Storage::disk('public')->put($bannerPath, base64_decode($base64ImageWithoutPrefix)))
            {
                $recordedBanner = Banners::find($id);

                if (isset($recordedBanner) && $recordedBanner->banner_path
                    && Storage::disk('public')->exists($recordedBanner->banner_path)) {
                    Storage::disk('public')->delete($recordedBanner->banner_path);
                    $recordedBanner->update([
                        'banner_path' => $bannerPath,
                        'banner_name' => $bannerName,
                        'type' => $type,
                        'media_type' => $banner['type'],
                        'active' => false
                    ]);

                    continue;
                }

                $lastBanner = Banners::where('type', $type)
                    ->latest('created_at')->first();

                Banners::create([
                    'banner_path' => $bannerPath,
                    'banner_name' => $bannerName,
                    'type' => $type,
                    'media_type' => $banner['type'],
                    'position' => $lastBanner ? $lastBanner->position + 1 : 1,
                    'active' => false
                ]);
            }
        }
    }

    public static function removeBannerSoft($removeBannerId): bool
    {
        $isDeleted = false;
        $current   = Banners::withTrashed()->find($removeBannerId);

        if (!$current->trashed()) {
            $current->update(['active' => false]);
            $isDeleted = $current->delete();
        } else
            $current->restore();

        return $isDeleted;
    }

    public static function setOptionBanner($optionBanners): void
    {
        foreach ($optionBanners as $id => $option)
        {
            $banner = Banners::withTrashed()->find($id);

            if ($banner) {
                $banner->update([
                    'banner_name' => $option['name'],
                    'position' => $option['position'],
                    'href' => $option['href'],
                ]);
            }
        }
    }

    public static function getBasementBanners($onlyActive = false)
    {
        $basementBanners = Banners::where('type', 'basement_banner');
        if ($onlyActive)
            $basementBanners->where('active', 1);
        return $basementBanners->orderBy('banners.position', 'ASC')->get();
    }

    public static function getBigBannerMenu($onlyActive = false)
    {
        $bigBannerBanners = Banners::where('type', 'big_banner_menu');
        if ($onlyActive)
            $bigBannerBanners->where('active', 1);
        return $bigBannerBanners->orderBy('banners.position', 'ASC')->get();
    }

    public static function getLittleBannerMenu()
    {
        return Banners::where('type', 'little_banner_menu')
            ->orderBy('banners.position', 'ASC')->get();
    }

    public static function getDetailBannerMenu($onlyActive = false)
    {
        $detailBanner = Banners::where('type', 'detail_banner');
        if ($onlyActive)
            $detailBanner->where('active', 1);
        return $detailBanner->orderBy('banners.position', 'ASC')->get();
    }

    public static function getExtraBanners(): array
    {
        $banners = Banners::get();
        $bannersFolder = 'banners';

        $needFilesName = [];
        foreach ($banners as $banner) {
            $arrayPath = explode('/', $banner->banner_path);
            $needFilesName[] = $arrayPath[count($arrayPath) - 1];
        }

        $pathFiles = [];
        foreach (Storage::disk('public')->files($bannersFolder) as $file) {
            $fileName = pathinfo($file, PATHINFO_BASENAME);

            if (!in_array($fileName, $needFilesName))
                $pathFiles[] = Storage::url("$bannersFolder/$fileName");
        }

        return $pathFiles;
    }
}
