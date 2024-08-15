import {alert} from "../helpers/alert.js";
import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs'
import {ProfileDomain as Profile} from "/public/js/domains/profileDomain.js";

let BannerSliderView = Backbone.View.extend({
    el: '.banner-slider',

    initialize: function () {
        this.initSwiper();
        this.initSlides();
    },

    initSwiper: function () {
        let $swiperContainer = this.$('.swiper');

        if ($swiperContainer.children().length > 0) {
            let swiperInterval = $swiperContainer.data('interval');

            new Swiper($swiperContainer, {
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
        }
    },

    initSlides: function() {
        $("#slider").responsiveSlides({
            auto: true,
            nav: false,
            speed: 500,
            namespace: "callbacks",
            pager: true,
        });
    }
});

let EnterButtonView = Backbone.View.extend({
    el: '.button-enter',

    events: {
        'click': 'openPopup'
    },

    openPopup: function () {
        $('.popup-login').removeClass('disabled');
        $('.popup-content.auth').removeClass('disabled');
        $('html').addClass('hide-scroll');
        $('.container').addClass('mrg-container');
    }
});

let LoginPopupLinksView = Backbone.View.extend({
    el: '.popup',

    events: {
        'click a': 'handleTabClick',
        'mousedown': 'handleMouseDown'
    },

    handleTabClick: function (event) {
        event.preventDefault();
        const clickedClass = $(event.currentTarget).attr('class');

        if (clickedClass === 'register-link') {
            this.showRegisterTab();
        } else if (clickedClass === 'login-link') {
            this.showLoginTab();
        } else if (clickedClass === 'restore-link') {
            this.showRestoreTab();
        }
    },

    showRegisterTab: function () {
        $('.popup-content.auth, .popup-content.restore').addClass('disabled');
        $('.popup-content.register').removeClass('disabled');
    },

    showLoginTab: function () {
        $('.popup-content.register, .popup-content.restore').addClass('disabled');
        $('.popup-content.auth').removeClass('disabled');
    },

    showRestoreTab: function () {
        $('.popup-content.register, .popup-content.auth').addClass('disabled');
        $('.popup-content.restore').removeClass('disabled');
    },

    handleMouseDown: function (event) {
        let popup = $('.popup-content');
        if (!popup.is(event.target) && popup.has(event.target).length === 0) {
            $('html').removeClass('hide-scroll');
            $('.popup, .popup-content.auth, .popup-content.register, .popup-content.restore').addClass('disabled');
        }
    }
});

let MobileMenu = Backbone.View.extend({
    el: 'span.menu',

    events: {
        'click': 'openMenu'
    },

    openMenu: function () {
        $("ul.nav1").slideToggle(300, function () {});
    }
});

let Back2Top = Backbone.View.extend({
    el: '#back2Top',

    events: {
        'click': 'handleTabClick',
    },

    initialize: function () {
        $(window).on('scroll', this.handleScroll.bind(this));
    },

    handleScroll: function () {
        if ($(window).scrollTop() > 400)
            $('#back2Top').fadeIn();
        else
            $('#back2Top').fadeOut();
    },

    handleTabClick: function (event) {
        event.preventDefault();
        $("html, body").animate({scrollTop: 0}, "slow");
        return false;
    }
});

let MaskContent = Backbone.View.extend({
    el: '.mask-content',

    events: {
        'click': 'maskToggle',
    },

    maskToggle: function () {
        $('#nav-toggle').prop('checked', function (evt, checked) {
            $('html').removeClass('hide-scroll');
            return !checked;
        });
    }
});

let RegistrationModel = Backbone.Model.extend({
    url: '/profile/create',
});

let RegistrationQuery = Backbone.View.extend({
    el: '#registration-form',

    events: {
        'submit': 'submitForm'
    },

    initialize: function () {
        this.model  = new RegistrationModel();
        this.loader = $('.popup-login .popup-content.register .loader');
        this.submitButton = this.$('[type="submit"]');

        this.listenTo(this.model, 'sync', this.onSubmitSuccess);
        this.listenTo(this.model, 'error', this.onSubmitError);
    },

    submitForm: function (event) {
        event.preventDefault();

        if (this.submitButton.prop('disabled')) {
            return;
        }

        this.submitButton.prop('disabled', true);
        this.loader.addClass('show');

        let profile = new Profile({
            name: $('#registration-name').val(),
            email: $('#registration-email').val(),
            password: $('#registration-password').val(),
            remember: $('#registration-remember').is(":checked"),
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        });

        this.model.save(profile, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    onSubmitSuccess: function (model, response) {
        if (response.reload)
            location.reload();
        this.loader.removeClass('show');
        this.submitButton.prop('disabled', false);
    },

    onSubmitError: function (model, error) {
        if (error && error.responseJSON && error.responseJSON.message)
            new alert().errorWindowShow($('.popup-content.register h3'), error.responseJSON.message);
        this.loader.removeClass('show');
        this.submitButton.prop('disabled', false);
    }
});

let AuthModel = Backbone.Model.extend({
    url: '/profile/login',
});

let AuthQuery = Backbone.View.extend({
    el: '#login-form',

    events: {
        'submit': 'submitForm'
    },

    initialize: function () {
        this.model  = new AuthModel();
        this.loader = $('.popup-login .popup-content.auth .loader');
        this.submitButton = this.$('[type="submit"]');

        this.listenTo(this.model, 'sync', this.onSubmitSuccess);
        this.listenTo(this.model, 'error', this.onSubmitError);
    },

    submitForm: function (event) {
        event.preventDefault();

        if (this.submitButton.prop('disabled')) {
            return;
        }

        this.submitButton.prop('disabled', true);
        this.loader.addClass('show');

        let profile = new Profile({
            email: $('#login-email').val(),
            password: $('#login-password').val(),
            remember: $('#login-remember').is(":checked"),
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        });

        this.model.save(profile, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    onSubmitSuccess: function (model, response) {
        if (response.reload)
            location.reload();
        this.loader.removeClass('show');
        this.submitButton.prop('disabled', false);
    },

    onSubmitError: function (model, error) {
        if (error && error.responseJSON && error.responseJSON.message)
            new alert().errorWindowShow($('.popup-content.auth h3'), error.responseJSON.message);
        this.loader.removeClass('show');
        this.submitButton.prop('disabled', false);
    }
});

let RestoreModel = Backbone.Model.extend({
    url: '/profile/restore',
});

let RestoreQuery = Backbone.View.extend({
    el: '#restore-form',

    events: {
        'submit': 'submitForm'
    },

    initialize: function () {
        this.model  = new RestoreModel();
        this.loader = $('.popup-login .popup-content.restore .loader');
        this.window = $('.popup-content.restore h3');
        this.submitButton = this.$('[type="submit"]');

        this.listenTo(this.model, 'sync', this.onSubmitSuccess);
        this.listenTo(this.model, 'error', this.onSubmitError);
    },

    submitForm: function (event) {
        event.preventDefault();

        if (this.submitButton.prop('disabled')) {
            return;
        }

        this.submitButton.prop('disabled', true);
        this.loader.addClass('show');

        let profile = new Profile({
            name: $('#restore-name').val(),
            email: $('#restore-email').val(),
        });

        this.model.save(profile, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    onSubmitSuccess: function (model, response) {
        if (response.message) {
            new alert().startTimer(this.submitButton);
            this.window.text(response.message);
            this.window.css('background', '#16ab46');
            this.window.addClass('show');

            this.loader.removeClass('show');
            setTimeout(function (a = this) {
                a.window.removeClass('show');
                a.window.removeAttr('style');
            }, 3000);
        }
    },

    onSubmitError: function (model, error) {
        if (error && error.responseJSON && error.responseJSON.message)
            new alert().errorWindowShow($('.popup-content.auth h3'), error.responseJSON.message);
        this.loader.removeClass('show');
        this.submitButton.prop('disabled', false);
    }
});

let WishlistItem = Backbone.Model.extend({
    url: '/wishlist/toggle-wishlist'
});

let WishlistCollection = Backbone.Collection.extend({
    model: WishlistItem
});

let WishlistActionView = Backbone.View.extend({
    el: '.wishlist-action input',

    events: {
        'change': 'toggleWishlist'
    },

    initialize: function() {
        this.wishQueue = new WishlistCollection();
        this.debounceWishlistTimeout;
    },

    toggleWishlist: function(event) {
        let wishlist = $(event.currentTarget);
        let toggleWishlist = wishlist.is(":checked");
        let game_id = $('main .container').data('game-id') ?? wishlist.data('game-id');
        let count   = $('.wishlist.favorite-count');

        let existingItem = this.wishQueue.findWhere({ game_id: game_id });
        if (existingItem) {
            existingItem.set('toggleWishlist', toggleWishlist);
        } else {
            this.wishQueue.add({
                toggleWishlist: toggleWishlist,
                game_id: game_id
            });
        }

        if (count.length > 0) {
            let currentValue = parseInt(count.text());

            if (!isNaN(currentValue)) {
                if (toggleWishlist) {
                    currentValue++;
                    count.text(currentValue);

                    let subscribeBtn = $('.user-subscribe');
                    if (subscribeBtn.text().trim() === 'Подписаться на обновления') {
                        subscribeBtn.removeClass('user-subscribe');
                        subscribeBtn.addClass('user-unsubscribe');
                        subscribeBtn.text('Отписаться от новостей');

                        let newsletterCount = $('.newsletter_count');
                        let currentValue = parseInt(newsletterCount.text()) + 1;
                        newsletterCount.text(currentValue++);
                    }
                } else {
                    currentValue--;
                    count.text(currentValue);
                }
            }
        }

        clearTimeout(this.debounceWishlistTimeout);
        this.debounceWishlistTimeout = setTimeout(() => {
            this.processWishlistQueue();
        }, 300);
    },

    processWishlistQueue: async function() {
        for (let item of this.wishQueue.models) {
            await this.processItem(item);
            await this.delay(300);
        }
    },

    processItem: function(item) {
        let self = this;
        return new Promise(function(resolve, reject) {
            let toggleWishlist = item.get('toggleWishlist');
            let game_id = item.get('game_id');

            let wishlistItem = new WishlistItem();
            wishlistItem.save({
                toggleWishlist: toggleWishlist,
                game_id: game_id
            }, {
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    self.wishQueue.remove(item);
                    resolve();
                },
                error: function() {
                    self.wishQueue.remove(item);
                    reject();
                }
            });
        });
    },

    delay: function(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
});

let BannerJumpModel = Backbone.Model.extend({
    url: '/banners/jump'
});

let BannerJumpQuery = Backbone.View.extend({
    el: '.banner-jump',

    events: {
        'click': 'submitForm'
    },

    initialize: function () {
        this.model = new BannerJumpModel();
        this.listenTo(this.model, 'sync', this.onSubmitSuccess);
    },

    submitForm: function (event) {
        event.preventDefault();

        let bannerId = this.$el.data('code');
        this.model.save({ id: bannerId }, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    onSubmitSuccess: function (model, response) {
        if (response.redirect_url)
            window.open(response.redirect_url, '_blank');
    }
});

let SkeletonLoader = Backbone.View.extend({
    el: window,

    initialize: function() {
        $(window).on('load', this.onLoad, () => this.onLoad());
    },

    onLoad: function() {
        $('.games-skeleton-list').hide();
        $('.games-list').show();
    }
});

let enterButtonView     = new EnterButtonView();
let loginPopupLinksView = new LoginPopupLinksView();
let registrationQuery   = new RegistrationQuery();
let authQuery           = new AuthQuery();
let restoreQuery        = new RestoreQuery();

let bannerSliderView  = new BannerSliderView();
let bannerJumpQuery   = new BannerJumpQuery();
let back2Top          = new Back2Top();
let mobileMenu        = new MobileMenu();
let maskContent       = new MaskContent();

let wishlistActionView   = new WishlistActionView();
let skeletonLoaderBanner = new SkeletonLoader();
