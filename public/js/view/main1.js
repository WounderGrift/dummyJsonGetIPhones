import {alert} from "../helpers/alert.js";
import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs'

$(document).ready(function () {
    let swiperInterval = $('.swiper').data('interval');
    new Swiper('.banner-slider .swiper', {
        direction: 'horizontal',
        loop: false,
        autoplay: {
            delay: swiperInterval,
            disableOnInteraction: false,
        },
        scrollbar: {
            el: '.swiper-scrollbar',
        },
    });

    $(function () {
        $("#slider").responsiveSlides({
            auto: true,
            nav: false,
            speed: 500,
            namespace: "callbacks",
            pager: true,
        });
    });

    $(window).scroll(function () {
        if ($(window).scrollTop() > 400)
            $('#back2Top').fadeIn();
        else
            $('#back2Top').fadeOut();
    });

    $("span.menu").on('click', function () {
        $("ul.nav1").slideToggle(300, function () {});
    });

    $('.button-enter').on('click', function () {
        $('.popup-login').removeClass('disabled');
        $('.popup-content.auth').removeClass('disabled');

        $('html').addClass('hide-scroll');
        $('.container').addClass('mrg-container');
    });

    $('a.register-link').on('click', function () {
        $('.popup-content.auth').addClass('disabled');
        $('.popup-content.restore').addClass('disabled');
        $('.popup-content.register').removeClass('disabled');
    });

    $('a.login-link').on('click', function () {
        $('.popup-content.register').addClass('disabled');
        $('.popup-content.restore').addClass('disabled');
        $('.popup-content.auth').removeClass('disabled');
    });

    $('a.restore-link').on('click', function () {
        $('.popup-content.register').addClass('disabled');
        $('.popup-content.auth').addClass('disabled');
        $('.popup-content.restore').removeClass('disabled');
    });

    $(document).mousedown((e) => {
        let popup = $('.popup-content');
        if (!popup.is(e.target) && popup.has(e.target).length === 0) {
            $('html').removeClass('hide-scroll');
            $('.popup, .popup-content.auth, .popup-content.register, .popup-content.restore').addClass('disabled');
        }
    });

    $('.mask-content').on('click', function () {
        $('#nav-toggle').prop('checked', function (evt, checked) {
            $('html').removeClass('hide-scroll');
            return !checked;
        });
    });

    $('#nav-toggle').on('change', function () {
        if ($(this).prop('checked'))
            $('html').addClass('hide-scroll');
        else
            $('html').removeClass('hide-scroll');
    });

    $("#back2Top").on('click', function (event) {
        event.preventDefault();
        $("html, body").animate({scrollTop: 0}, "slow");
        return false;
    });

    let isFormSubmitting = false;
    $('#registration-form').submit(function () {
        if (isFormSubmitting)
            return;
        isFormSubmitting = true;

        $('.register .loader').addClass('show');

        let name = $('#registration-name').val();
        let email = $('#registration-email').val();
        let password = $('#registration-password').val();
        let remember = $('#registration-remember').is(":checked");
        let get_letter_release = $('#mailing').is(":checked");
        let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        $.ajax({
            url: '/profile/create',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: name,
                email: email,
                password: password,
                remember: remember,
                get_letter_release: get_letter_release,
                timezone: timezone
            },
            success: function (response) {
                if (response.reload)
                    location.reload();
                $('.register .loader').removeClass('show');
                isFormSubmitting = false;
            },
            error: function (error) {
                if (error && error.responseJSON && error.responseJSON.message)
                    new alert().errorWindowShow($('.popup-content.register h3'), error.responseJSON.message);
                $('.register .loader').removeClass('show');
                isFormSubmitting = false;
            }
        });
    });

    $('#login-form').submit(function () {
        if (isFormSubmitting)
            return;
        isFormSubmitting = true;

        $('.auth .loader').addClass('show');

        let email    = $('#login-email').val();
        let password = $('#login-password').val();
        let remember = $('#login-remember').is(":checked");
        let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        $.ajax({
            url: '/profile/login',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                email: email,
                password: password,
                remember: remember,
                timezone: timezone
            },
            success: function (response) {
                if (response.reload)
                    location.reload();
                $('.auth .loader').removeClass('show');
                isFormSubmitting = false;
            },
            error: function (error) {
                if (error && error.responseJSON && error.responseJSON.message)
                    new alert().errorWindowShow($('.popup-content.auth h3'), error.responseJSON.message);
                $('.auth .loader').removeClass('show');
                isFormSubmitting = false;
            }
        });
    });

    $('#restore-form').submit(function () {
        $('.restore .loader').addClass('show');

        let button = $('#restore-form button');
        button.prop('disabled', true);
        let name   = $('#restore-name').val();
        let email  = $('#restore-email').val();
        let window = $('.popup-content.restore h3');

        $.ajax({
            url: '/profile/restore',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: name,
                email: email
            },
            success: function (response) {
                if (response.message) {
                    new alert().startTimer(button);
                    window.text(response.message);
                    window.css('background', '#16ab46');
                    window.addClass('show');

                    $('.restore .loader').removeClass('show');
                    setTimeout(function () {
                        window.removeClass('show');
                        window.removeAttr('style');
                    }, 3000);
                }
            },
            error: function (error) {
                if (error && error.responseJSON && error.responseJSON.message)
                    new alert().errorWindowShow(window, error.responseJSON.message);
                button.prop('disabled', false);
                $('.restore .loader').removeClass('show');
            }
        });

        setTimeout(function() {
            button.prop('disabled', false);
        }, 60000);
    });

    let debounceWishlistTimeout;
    let wishQueue = [];
    $('.wishlist-action input').on('change', function () {
        let wishlist = $(this);
        let toggleWishlist = wishlist.is(":checked");
        let game_id  = wishlist.data('game-id');
        let count    = $('.wishlist.favorite-count');

        if (count.length > 0) {
            let currentValue = parseInt(count.text());

            if (!isNaN(currentValue)) {
                if (toggleWishlist) {
                    currentValue++;
                    count.text(currentValue);

                    let subscribeBtn = $('.user-subscribe');
                    subscribeBtn.removeClass('user-subscribe');
                    subscribeBtn.addClass('user-unsubscribe');
                    subscribeBtn.text('Отписаться от новостей');
                } else {
                    currentValue--;
                    count.text(currentValue);
                }
            }
        }

        wishQueue.push({
            toggleWishlist: toggleWishlist,
            game_id: game_id,
        });

        clearTimeout(debounceWishlistTimeout);
        debounceWishlistTimeout = setTimeout(() => {
            processWishlistQueue();
        }, 300);
    });

    let isBannerJumpSubmitting = false;
    $('.banner-jump').on('click', function(event) {
        event.preventDefault();

        if (isBannerJumpSubmitting)
            return;
        isBannerJumpSubmitting = true;

        let bannerId = $(this).data('code');

        $.ajax({
            url: '/banners/jump',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: bannerId
            },
            success: function (response) {
                if (response.redirect_url) {
                    window.open(response.redirect_url, '_blank');
                }
                isBannerJumpSubmitting = false;
            },
            error: function () {
                isBannerJumpSubmitting = false;
            }
        });
    });

    function processWishlistQueue()
    {
        if (wishQueue.length > 0) {
            let likeData = wishQueue.shift();
            let toggleWishlist  = likeData.toggleWishlist;
            let game_id     = likeData.game_id;

            $.ajax({
                url: '/wishlist/toggle-wishlist',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    toggleWishlist: toggleWishlist,
                    game_id: game_id
                },
                success: function () {
                    processWishlistQueue();
                },
                error: function () {
                    processWishlistQueue();
                }
            })
        }
    }
});
