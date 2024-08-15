<!DOCTYPE html>
<html lang="ru-RU" class="h-100">
<head>
    <title>dummyJson</title>
    <link href="{{asset('css/bootstrap.css')}}" rel='stylesheet' type='text/css'/>
    <link href="{{asset('css/style.css')}}" rel='stylesheet' type='text/css'/>
    <link href="{{asset('css/itc-slider.css')}}" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <meta name="title" content="dummyJson">
    <meta name="description" content="dummyJson">
    <link rel="alternate" type="application/rss+xml" title="dummyJson" href="{{ asset('rss.xml') }}">

    <meta property="twitter:card" content="summary">
    <meta property="twitter:title" content="dummyJson">
    <meta property="twitter:description" content="dummyJson">

    <meta property="og:type" content="article">
    <meta property="og:site_name" content="dummyJson">
    <meta property="og:title" content="dummyJson">
    <meta property="og:description" content="dummyJson">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.min.js"></script>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">

    <script src="{{ asset('lib/underscore/underscore.js') }}"></script>
    <script src="{{ asset('lib/backbone/backbone.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script type="module" src="{{ asset('js/view/main.js') }}?version={{ config('app.version') }}"></script>
</head>
<body>
@csrf

@if (!Auth::check())
    <div class="popup-login popup disabled">
        <div class="popup-content auth disabled">
            <div class="loader"></div>
            <h2><u>Авторизация</u></h2>
            <h3 class="error"></h3>
            <form id="login-form" method="POST" onsubmit="return false;">
                <input id="login-email" class="regist-login" type="email" name="email" placeholder="Ваш eMail">
                <input id="login-password" class="regist-login" type="password" name="password"
                       placeholder="Ваш Пароль">
                <label class="checkbox-container" for="login-remember">
                    Запомнить меня
                    <input type="checkbox" id="login-remember">
                    <span class="checkmark"></span>
                </label>
                <button type="submit">ЗАЙТИ</button>
            </form>
            <hr>
            <p>У вас нет аккаунта? <a class="register-link">Зарегистрируйтесь</a></p>
        </div>
        <div class="popup-content register disabled">
            <div class="loader"></div>
            <h2><u>Зарегистрироваться</u></h2>
            <h3 class="error"></h3>
            <form id="registration-form" method="POST" onsubmit="return false;">
                <input id="registration-name" class="regist-login" type="text" name="name" placeholder="Ваше Имя">
                <input id="registration-email" class="regist-login" type="email" name="email" placeholder="Ваш eMail">
                <input id="registration-password" class="regist-login" type="password" name="password"
                       placeholder="Ваш Пароль">
                <label class="checkbox-container" for="registration-remember">
                    Запомнить меня
                    <input type="checkbox" id="registration-remember">
                    <span class="checkmark"></span>
                </label>
                <button type="submit">ЗАЙТИ</button>
            </form>
            <p>У вас есть аккаунт? <a class="login-link">Вернуться назад</a></p>
        </div>
        <div class="popup-content restore disabled">
            <div class="loader"></div>
            <h2><u>Восстановить доступ</u></h2>
            <h3 class="error"></h3>
            <form id="restore-form" method="POST" onsubmit="return false;">
                <input id="restore-name" class="regist-login" type="text" name="name" placeholder="Ваше Имя">
                <input id="restore-email" class="regist-login" type="email" name="email" placeholder="Ваше Мыло">
                <button type="submit">ПРИСЛАТЬ ПИСЬМО</button>
            </form>
            <p>Вспомнили пароль? <a class="login-link">Вернуться</a></p>
        </div>
    </div>
@endif

<div class="top-banner">
    <div class="header">
        <div class="container">
            <div class="header-left">
                <div class="search">
                    <form action="" method="GET">
                        <input type="submit" value="">
                        <input type="text" name="query" autocomplete="off" placeholder="Поиска по сайту нет...">
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="headr-right">
                <div class="details">
                    @if (!Auth::check())
                        <a class="button-enter button">
                            <i class="fa fa-sign-in" aria-hidden="true"></i>ЗАГЛЯНУТЬ
                        </a>
                    @else
                        @if (Auth::user()->checkOwner())
                            <a href="{{ route('profiles.chart.table') }}" class="btn btn-success">
                                <i class="fas fa-user-shield"></i>
                            </a>
                        @elseif (Auth::user()->checkAdmin())
                            <a href="{{ route('profiles.chart.table') }}" class="btn btn-success">
                                <i class="fas fa-user-shield"></i>
                            </a>
                        @endif

                        <a href="{{ route('profile.index.cid', ['cid' => Auth::user()->cid]) }}" class="btn btn-orange">
                            <i class="fas fa-user"></i>
                        </a>

                        <a href="{{ route('profile.logout') }}" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div id="main-loader" class="loader"></div>
    </div>
    <!--banner-info-->
    <div class="banner-info" style="
        background: rgba(0, 0, 0, 0.5);
        position: fixed;
        width: 100% !important;
        left: 0 !important;
        height: 52px;
        top: 55px;">
        <div class="container" style="display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;">
            <div class="logo">
                <h1><a href="{{ route('main.index') }}">ГЛАВНАЯ СТРАНИЦЫ</a></h1>
            </div>
            <div class="top-menu">
                <span class="menu"></span>
                <ul class="nav1">
                    <li><a href="#">ПУНКТ 1</a></li>
                    <li><a href="#">ПУНКТ 2</a></li>
                    <li><a href="#">ПУНКТ 3</a></li>
                    <li><a href="#">ПУНКТ 4</a></li>
                    <li><a href="#">ПУНКТ 5</a></li>
                    <li><a href="#">ПУНКТ 6</a></li>
                    <li><a href="#">ПУНКТ 7</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<!-- banner -->
<!-- Slider-starts-Here -->
<script src="{{ asset('js/responsiveslides.min.js') }}"></script>

<div class="banner">
    <div class="bnr2"
         style="background: url({{ Storage::url('banners/default/bnr3.jpg') }}) no-repeat 0 0; background-size: cover;">
    </div>
</div>

<main>

@yield('content')

<a id="back2Top" title="Наверх" href="#">&#10148;</a>
</main>

<footer style="flex-shrink: 0;">
    <div class="copywrite">
        <div class="basement container">
            <a class="telegram" href="#" target="_blank">
                <p><i class="fab fa-telegram"></i> Канал</p>
            </a>
            <a class="feedback-link" href="#">
                <p><i class="fas fa-envelope"></i> Связь</p>
            </a>
            <a class="copyright" href="#">
                <p><i class="fas fa-copyright"></i> {{ date('Y') }} dummyJson</p>
            </a>
            <a class="thanks" href="#" target="_blank">
                <p><i class="fas fa-heart"></i> Спасибо</p>
            </a>
        </div>
    </div>
</footer>

</body>
</html>
