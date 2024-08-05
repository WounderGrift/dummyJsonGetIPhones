<?php

namespace Modules\ProfilePage\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\DateHelper;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\ProfilePage\App\Http\Interfaces\ProfilePageInterface;

class ProfileController extends Controller implements ProfilePageInterface
{
    public function index($cid = null)
    {
        $inProfilePage = true;

        if (!$cid)
            $profile = $this->profile;
        else {
            $profile = Users::query()->where('cid', $cid)->first();
            if (!$profile)
                $profile = $this->profile;
        }

        return view('profilePage::profile', compact('inProfilePage', 'profile'));
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'regex:/^[\w\.~-]+@([a-zA-Z-]+\.)+[a-zA-Z-]{2,4}$/i',
                Rule::unique('users'), 'string', 'max:255'],
            'password' => ['required', 'min:6', 'max:255'],
            'remember' => ['boolean'],
            'timezone' => ['string']
        ]);

        if (!Users::latest()->exists())
            $data['role'] = Users::ROLE_OWNER;
        else
            $data['role'] = Users::ROLE_FREQUENTER;
        $data['cid']  = null;

        if (DateHelper::isValidTimeZone($data['timezone']))
            $timezone = $data['timezone'];
        else {
            $timezone = config('app.timezone');
        }

        try {
            DB::beginTransaction();

            $data['timezone']  = $timezone;
            $data['is_verify'] = 1;
            $user = Users::create(Arr::only($data, ['cid', 'name', 'email', 'role', 'password', 'is_verify',
                'timezone']));

            if ($user) {
                Auth::loginUsingId($user->id, $data['remember']);

                DB::commit();
                return response()->json(['reload' => true]);
            }

            throw new \Exception('Не получается создать ваш профиль', 400);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email', 'regex:/^[\w\.~-]+@([a-zA-Z-]+\.)+[a-zA-Z-]{2,4}$/i', 'string'],
            'password' => ['required', 'min:6'],
            'remember' => ['boolean'],
        ]);

        $user = Users::where('email', $data['email'])->first();

        try {
            if ($user) {
                if (DateHelper::isValidTimeZone($request->input('timezone')))
                    $timezone = $request->input('timezone');
                else {
                    $timezone = config('app.timezone');
                }

                $user->update(['timezone' => $timezone]);
                if (Hash::check($data['password'], $user->password)) {
                    if ($user->oneTimeToken)
                        $user->oneTimeToken->delete();

                    Auth::loginUsingId($user->id, $data['remember']);
                    return response()->json(['reload' => true]);
                }

                if ($user->oneTimeToken) {
                    $expired = Carbon::now()->gt($user->oneTimeToken->updated_at->addMinute(30));

                    if (!$expired && Hash::check($data['password'], $user->oneTimeToken->token)) {
                        $user->oneTimeToken->delete();
                        Auth::loginUsingId($user->id, $data['remember']);
                        return response()->json(['reload' => true]);
                    }
                }
            }

            return response()->json(['message' => 'Неверные мыло и пароль'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Произошла ошибка при входе в профиль'], 400);
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('main.index');
    }

    public function banned(Request $request): JsonResponse
    {
        $code = $request->input('code');
        $userId = base64_decode($code);

        $user = Users::find($userId);
        $isBanned = !$user->is_banned;
        $user->update([
            'is_banned' => $isBanned
        ]);

        return response()->json(['redirect_url' => route('profile.index.cid', ['cid' => $user->cid])]);
    }
}
