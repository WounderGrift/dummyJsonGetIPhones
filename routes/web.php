<?php

use App\Http\Controllers\SearchGamesController;
use Illuminate\Support\Facades\Route;

use Modules\MainPage\App\Http\Controllers\AllGamesController;
use Modules\MainPage\App\Http\Controllers\WishlistGameController;
use Modules\ProfilePage\App\Http\Controllers\EditProfileController;
use Modules\ProfilePage\App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::middleware(['auth', 'isNotVerify'])->group(function () {
    Route::prefix('/profile')->group(function () {
        Route::post('/send-email-verify', [ProfileController::class, 'sendEmailVerify'])
            ->name('profile.send-email-verify');
        Route::get('/verify/{token}', [ProfileController::class, 'verify'])
            ->name('profile.verify');
    });
});

Route::middleware(['auth', 'isVerify'])->group(function () {
    Route::prefix('/wishlist')->group(function () {
        Route::get('/', [WishlistGameController::class, 'index'])->name('wishlist.index');
        Route::get('/{category}', [WishlistGameController::class, 'index'])->name('wishlist.index.category');
        Route::put('/toggle-wishlist', [WishlistGameController::class, 'toggleWishlist'])
            ->name('wishlist.toggle');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/logout', [ProfileController::class, 'logout'])->name('profile.logout');
        Route::get('/edit/{cid}', [EditProfileController::class, 'index'])->name('profile.edit');
        Route::put('/update', [EditProfileController::class, 'update'])->name('profile.update');
        Route::get('/{cid}', [ProfileController::class, 'index'])->name('profile.index.cid');
        Route::post('/chart', [ProfileController::class, 'profileChart'])->name('profile.chart');
    });
});

Route::middleware('owner')->group(function () {
    Route::prefix('banners')->group(function () {
        Route::get('/big-banners', [BannerController::class, 'indexBigBanner'])
            ->name('big-banner.index');
        Route::get('/little-banners', [BannerController::class, 'indexLittleBanner'])
            ->name('little-banner.index');
        Route::get('/detail-banners', [BannerController::class, 'indexDetailBanner'])
            ->name('detail-banner.index');
        Route::get('/basement-banners', [BannerController::class, 'indexBasementBanner'])
            ->name('basement-banner.index');
        Route::post('/jump', [BannerController::class, 'bannerJump'])->name('jump-banner');
        Route::post('/banners-save', [BannerController::class, 'bannersSave'])->name('banners.save');
        Route::delete('/banner-remove-softly', [BannerController::class, 'bannerRemoveSoftly'])
            ->name('banner.remove-softly');
        Route::delete('/banner-remove-forced', [BannerController::class, 'bannerRemoveForced'])
            ->name('banner.remove-forced');
        Route::post('/activate-banner', [BannerController::class, 'bannerActivate'])->name('banner.activate');
    });
});

Route::middleware(['ownerOrAdmin'])->group(function () {
    Route::prefix('/owner')->group(function () {
        Route::get('/{hideBroken?}/{source?}', [OwnerController::class, 'index'])->name('owner.index');
    });

    Route::prefix('/dynamic-menu')->group(function () {
        Route::get('/', [DynamicMenuController::class, 'index'])->name('dynamic-menu.index');
        Route::post('/save', [DynamicMenuController::class, 'save'])->name('dynamic-menu.save');
    });

    Route::prefix('/detail')->group(function () {
        Route::get('/edit/{uri}', [EditDetailsController::class, 'index'])->name('detail.edit.index');
        Route::delete('/remove-game', [EditDetailsController::class, 'removeGame'])
            ->name('detail-game.remove');
        Route::delete('/remove-torrent-softly', [EditDetailsController::class, 'removeTorrentSoftly'])
            ->name('file-softly.remove');
        Route::delete('/remove-torrent-forced', [EditDetailsController::class, 'removeTorrentForced'])
            ->name('file-forced.remove');
        Route::delete('/remove-screen-softly', [EditDetailsController::class, 'removeScreenSoftly'])
            ->name('screen-softly.remove');
        Route::delete('/remove-screen-forced', [EditDetailsController::class, 'removeScreenForced'])
            ->name('screen-forced.remove');
        Route::post('/preview-grid-set-existed', [EditDetailsController::class, 'setPreviewGridFromExists'])
            ->name('preview-grid.setExisted');
        Route::delete('/preview-grid-remove-existed', [EditDetailsController::class, 'setPreviewGridRemoveExists'])
            ->name('preview-grid.removeExisted');
        Route::post('/release', [EditDetailsController::class, 'release'])->name('detail.release');
        Route::get('/new', [NewDetailController::class, 'index'])->name('detail.new.index');
        Route::post('/create', [NewDetailController::class, 'create'])->name('detail.create');
    });

    Route::prefix('chart')->group(function () {
        Route::get('/profiles', [ChartsController::class, 'profilesTable'])->name('profiles.chart.table');
        Route::post('/profiles/range', [ChartsController::class, 'profilesChart'])
            ->name('profiles.chart.range');
        Route::get('/profiles/{search}', [ChartsController::class, 'profilesTable'])
            ->name('profiles.chart.search');

        Route::get('/activity', [ChartsController::class, 'commentariesTable'])->name('activity.chart.table');
        Route::post('/activity/range', [ChartsController::class, 'activityChart'])
            ->name('activity.chart.range');
        Route::get('/commentaries/{search}', [ChartsController::class, 'commentariesTable'])
            ->name('commentaries.chart.search');

        Route::get('/banners', [ChartsController::class, 'bannersTable'])->name('banners.chart.table');
        Route::post('/banners/range', [ChartsController::class, 'bannersChart'])->name('banners.chart.range');
    });

    Route::prefix('unpublished')->group(function () {
        Route::get('/', [UnpublishedGameController::class, 'index'])->name('unpublished.index');
        Route::get('/{uri}', [UnpublishedGameController::class, 'detail'])->name('unpublished.detail');
    });

    Route::prefix('expiration')->group(function () {
        Route::get('/', [ExpirationGameController::class, 'index'])->name('expiration.index');
        Route::get('/{uri}', [ExpirationGameController::class, 'detail'])->name('expiration.detail');
    });

    Route::post('/publishing', [PublishGameController::class, 'publish'])->name('publishing');
    Route::get('/search-for-unpublished', [SearchGamesController::class, 'searchForUnpublished'])
        ->name('search.unpublished');
    Route::get('/search-for-series', [SearchGamesController::class, 'searchForSeries'])
        ->name('search.series');

    Route::prefix('/series')->group(function() {
        Route::get('/all', [EditSeriesController::class, 'index'])->name('series.list');
        Route::get('/all/{uri}', [EditSeriesController::class, 'indexView'])->name('series.list.view');
        Route::get('/new', [NewSeriesController::class, 'index'])->name('series.new');
        Route::post('/create', [NewSeriesController::class, 'create'])->name('series.create');
        Route::get('/edit/{uri}', [EditSeriesController::class, 'indexSeriesDetail'])->name('series.edit');
        Route::post('/update', [EditSeriesController::class, 'update'])->name('series.update');

        Route::post('/preview-set-existed', [EditSeriesController::class, 'setPreviewFromExists'])
            ->name('series.setPreviewExisted');
        Route::delete('/preview-remove-existed', [EditSeriesController::class, 'setPreviewRemoveExists'])
            ->name('series.removeExisted');
    });

    Route::prefix('publish')->group(function () {
        Route::get('/{uri}', [PublishGameController::class, 'indexPreview'])->name('publish.uri');
        Route::get('/detail/{uri}', [PublishGameController::class, 'indexDetail'])
            ->name('publish.detail.uri');
        Route::delete('/remove', [PublishGameController::class, 'removeGame'])->name('publishing.remove');
    });

    Route::prefix('trashed')->group(function () {
        Route::get('/trashed-games', [RecycleBinController::class, 'trashedGameIndex'])
            ->name('trashed.games');
        Route::post('/remove-games', [RecycleBinController::class, 'removeGame'])->name('trashed.removeGame');
        Route::post('/restore-games', [RecycleBinController::class, 'restoreGame'])
            ->name('trashed.restoreGame');
        Route::delete('/cleaning-games', [RecycleBinController::class, 'emptyTrashGame'])
            ->name('trashed.cleaningGame');

        Route::get('/trashed-screen', [RecycleBinController::class, 'trashedScreenIndex'])
            ->name('trashed.screen');
        Route::delete('/cleaning-screen', [RecycleBinController::class, 'emptyTrashScreenshots'])
            ->name('trashed.cleaningScreen');

        Route::get('/trashed-files', [RecycleBinController::class, 'trashedTorrentIndex'])
            ->name('trashed.files');
        Route::delete('/cleaning-files', [RecycleBinController::class, 'emptyTrashTorrents'])
            ->name('trashed.cleaningFiles');
    });

    Route::post('/banned', [ProfileController::class, 'banned'])->prefix('profile')
        ->name('profile.banned');
});

Route::group(['namespace' => 'App\Http\Controllers'], function() {
    Route::get('/', [RecommendedGameController::class, 'index'])->name('main.index');
    Route::redirect('/recommended', '/');
    Route::get('/recommended/{ids}', [RecommendedGameController::class, 'recommendedDetailIndex'])
        ->name('recommended.index');
    Route::get('/all', [AllGamesController::class, 'index'])->name('all.index');
    Route::get('/series', [SeriesGameController::class, 'index'])->name('series.index');
    Route::get('/new', [NewGamesController::class, 'index'])->name('new.index');
    Route::get('/waiting', [WaitingGamesController::class, 'index'])->name('waiting.index');
    Route::get('/russian', [RussianGamesController::class, 'index'])->name('russian.index');
    Route::get('/weak', [WeakGamesController::class, 'index'])->name('weak.index');
    Route::get('/repacks', [RepackGamesController::class, 'index'])->name('repacks.index');
    Route::get('/soft', [AllSoftController::class, 'index'])->name('soft.index');

    Route::get('/search', [SearchGamesController::class, 'index'])->name('search.index');

    Route::prefix('/profile')->group(function () {
        Route::post('/create', [ProfileController::class, 'create'])->name('profile.create');
        Route::post('/login', [ProfileController::class, 'login'])->name('profile.login');
        Route::post('/restore', [ProfileController::class, 'restore'])->name('profile.restore');
    });

    Route::prefix('/detail')->group(function () {
        Route::redirect('/', '/');
        Route::post('/subscribe', [DetailsGameController::class, 'subscribe'])->name('detail.subscribe');
        Route::post('/unsubscribe', [DetailsGameController::class, 'unsubscribe'])
            ->name('detail.unsubscribe');
        Route::post('/download', [DetailsGameController::class, 'download'])->name('detail.download');
        Route::post('/toggle-like', [DetailsGameController::class, 'toggleLike'])->name('detail.toggleLike');
        Route::post('/send-comment', [DetailsGameController::class, 'sendComment'])
            ->name('detail.sendComment');
        Route::delete('/remove-comment', [DetailsGameController::class, 'removeComment'])
            ->name('detail.removeComment');
        Route::post('/reset-comment', [DetailsGameController::class, 'resetComment'])
            ->name('detail.resetComment');
        Route::post('/send-report-error', [DetailsGameController::class, 'sendReportError'])
            ->name('detail.sendReportError');
        Route::redirect('/edit', '/');
        Route::get('/{uri}', [DetailsGameController::class, 'index'])->name('detail.index.uri');
    });

    Route::prefix('/mail')->group(function () {
        Route::get('/unsubscribe-from-email-about-public-game/{code}',
            [SubscribeFromMailController::class, 'unsubscribeFromEmailAboutPublicGame'])
            ->name('unsubscribeFromEmailAboutPublicGame');
        Route::get('/subscribe-from-unsubscribe-about-public-game/{code}',
            [SubscribeFromMailController::class, 'subscribeFromUnsubscribeToPublicGame'])
            ->name('subscribeFromUnsubscribeToPublicGame');
        Route::get('/unsubscribe-from-email-about-update-game/{code}/{id}',
            [SubscribeFromMailController::class, 'unsubscribeFromEmailAboutUpdateGame'])
            ->name('unsubscribeFromEmailAboutUpdateGame');
        Route::get('/subscribe-from-unsubscribe-about-update-game/{code}/{id}',
            [SubscribeFromMailController::class, 'subscribeFromUnsubscribeAboutUpdateGame'])
            ->name('subscribeFromUnsubscribeAboutUpdateGame');
        Route::get('/unsubscribe-from-email-about-update-games/{code}',
            [SubscribeFromMailController::class, 'unsubscribeFromEmailAboutUpdateGames'])
            ->name('unsubscribeFromEmailAboutUpdateGames');
        Route::get('/unsubscribe-from-all-newsletter/{code}',
            [SubscribeFromMailController::class, 'unsubscribeFromAllNewsletter'])
            ->name('unsubscribeFromAllNewsletter');
    });

    Route::get('/faq', function () {return view('faq');})->name('faq');
    Route::middleware('resetLockIpFeedback')->get('/feedback', [FeedbackController::class, 'index'])
        ->name('feedback.index');
    Route::post('/feedback/send-feedback', [FeedbackController::class, 'sendFeedback'])
        ->name('feedback.send');

    Route::get('/{category}', [AllGamesController::class, 'index'])->name('all.index.category');
    Route::get('/series/{uri}', [SeriesGameController::class, 'indexSeries'])->name('series.indexSeries');
    Route::get('/all/{category}', [AllGamesController::class, 'index'])->name('all.index.category');
    Route::get('/new/{category}', [NewGamesController::class, 'index'])->name('new.index.category');
    Route::get('/waiting/{category}', [WaitingGamesController::class, 'index'])
        ->name('waiting.index.category');
    Route::get('/russian/{category}', [RussianGamesController::class, 'index'])
        ->name('russian.index.category');
    Route::get('/weak/{category}', [WeakGamesController::class, 'index'])->name('weak.index.category');
    Route::get('/repacks/{category}', [RepackGamesController::class, 'index'])
        ->name('repacks.index.category');
    Route::get('/year/{category}', [YearGamesController::class, 'index'])->name('year.index.category');
    Route::get('/soft/{category}', [AllSoftController::class, 'index'])->name('soft.index.category');
});
