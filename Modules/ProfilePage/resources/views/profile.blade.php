@extends('main::layouts.main')
@section('content')

    @if ($profile->is_banned)
        <div class="info-block">
            <div class="info_title"><b>Блокировка</b></div>
            <div class="news_content">Была выдана вам.</div>
        </div>
    @endif

    <div class="error">
        <h3></h3>
    </div>

    <div class="about">
        <div class="container">
            <h2>{{ $profile->name }}</h2>
            <div class="row about-info-grids">
                <div class="col-md-5 col-sm-12 abt-pic profile-center">
                    @if ($profile->is_banned)
                        <img src="{{ asset('images/banned.png') }}"
                             class="img-responsive profile-avatar"
                             alt="{{ $profile->avatar_name ?? 'images/banned.png' }}"/>
                    @else
                        <img src="{{ $profile->avatar_path ? Storage::url($profile->avatar_path) : asset('images/350.png') }}?timestamp={{ $profile->updated_at->timestamp }}"
                             class="img-responsive profile-avatar"
                             alt="{{ $profile->avatar_name ?? 'images/350.png' }}"/>
                    @endif
                    @if (Auth::check())
                        @if (($profile->id === Auth::user()->id && !$profile->is_banned) || ($profile->role !== $profile::ROLE_OWNER && Auth::user()->checkAdmin()) || Auth::user()->checkOwner())
                            <a href="{{ route('profile.edit', ['cid' => $profile->cid]) }}" class="btn btn-orange edit-button">
                                Редактировать
                            </a>
                        @endif
                        @if (($profile->id !== Auth::user()->id) && ($profile->role !== $profile::ROLE_OWNER && Auth::user()->checkAdmin() || Auth::user()->checkOwner()))
                            <button id="ban-button" class="btn btn-danger ban-button" data-code="{{ base64_encode($profile->id) }}">
                                {{ $profile->is_banned ? 'Разблокировать' : 'Заблокировать' }}
                            </button>
                        @endif
                    @endif
                </div>
                <div class="col-md-7 col-sm-12 abt-info-pic profile-center">
                    <h3>{{ $profile->status ?? 'Статус' }}</h3>
                    <p>{{ $profile->about_me ?? 'Расскажи немного о себе, пожалуйста' }}</p>

                    <ul>
                        <li>Дата регистрации: {{ \App\Http\Helpers\DateHelper::dateFormatterJFY($profile->created_at, $profile->timezone) }}</li>
                        @if ($profile->last_activity && $profile->timezone)
                            <li>Последний онлайн: {{ \App\Http\Helpers\DateHelper::getLastActivity($profile->last_activity, Auth::user()->timezone) }}</li>
                        @endif
                    </ul>
                    <div id="chartContainer" style="height: 300px; width: 100%;" data-code="{{ base64_encode($profile->id) }}"></div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
    <script type="module" src="{{asset('modules/profilepage/resources/assets/js/profiles.js')}}?version={{config('app.version')}}"></script>
@endsection
