import {alert} from "../../../../../public/js/helpers/alert.js";
import {ProfileDomain as Profile} from "../../../../../public/js/domains/profileDomain.js";

let ProfileChartModel = Backbone.Model.extend({
    url: '/profile/chart'
});

let ProfileChart = Backbone.View.extend({
    el: '#chartContainer',

    initialize: function() {
        let options = {
            animationEnabled: true,
            data: [
                {
                    type: "column",
                    dataPoints: [
                        {label: "Поддержка", y: 0},
                        {label: "Загрузок", y: 0},
                        {label: "Комменты", y: 0},
                        {label: "Лайки на игры", y: 0},
                        {label: "Лайки на комменты", y: 0},
                        {label: "Желаемые", y: 0},
                        {label: "Подписок", y: 0},
                    ]
                }
            ]
        };

        $('#chartContainer').CanvasJSChart(options);
        this.$('.canvasjs-chart-credit').hide();

        this.model = new ProfileChartModel();
        this.listenTo(this.model, 'showData', this.showData);
        this.listenTo(this.model, 'showError', this.showError);
        this.getData();
    },

    getData: function() {
        let id = this.$el.data('code')

        this.model.save({ code: id }, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },success: function(response) {
                this.model.trigger('showData', response.dataChart);
            }.bind(this),
            error: function(model, xhr) {
                this.model.trigger('showError', xhr.responseJSON.message);
            }.bind(this)
        });
    },

    showData: function(dataChart) {
        let options = {
            animationEnabled: true,
            data: [
                {
                    type: "column",
                    dataPoints: [
                        {label: "Поддержка", y: dataChart?.support ?? 0},
                        {label: "Загрузок", y: dataChart?.downloads ?? 0},
                        {label: "Комменты", y: dataChart?.comments ?? 0},
                        {label: "Лайки на игры", y: dataChart?.likesToGames ?? 0},
                        {label: "Лайки на комменты", y: dataChart?.likesToComments ?? 0},
                        {label: "Желаемые", y: dataChart?.wishlist ?? 0},
                        {label: "Подписок", y: dataChart?.newsletters ?? 0},
                    ]
                }
            ]
        };

        $('#chartContainer').CanvasJSChart(options);
        this.$('.canvasjs-chart-credit').hide();
    },

    showError: function(errorMessage) {
        if (errorMessage)
            new alert().errorWindowShow($('.error'), errorMessage);
        $('#main-loader').removeClass('show');
    }
});

let ProfileVerifyModel = Backbone.Model.extend({
    url: '/profile/send-email-verify',
});

let VerifyQuery = Backbone.View.extend({
    el: '#verify-email',

    events: {
        'submit': 'submitForm'
    },

    initialize: function () {
        this.model = new ProfileVerifyModel();
        this.submitButton = $('#verify-email button');
    },

    submitForm: function (event) {
        event.preventDefault();

        if (this.submitButton.prop('disabled')) {
            return;
        }

        this.submitButton.prop('disabled', true);

        let profile = new Profile({
            name: $('.info_title').data('name'),
            email: $('.news_content').data('email'),
        });

        this.model.save(profile, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: this.onSubmitSuccess.bind(this),
            error: this.onSubmitError.bind(this)
        });

        setTimeout(function () {
            this.button.prop('disabled', false);
        }, 60000);
    },

    onSubmitSuccess: function (model, response) {
        if (response.message) {
            new alert().startTimer(this.button);
        }
    },

    onSubmitError: function (model, error) {
        if (error && error.responseJSON && error.responseJSON.message)
            this.button.prop('disabled', false);
    }
});

let ProfileBanModel = Backbone.Model.extend({
    url: '/profile/banned',
});

let ProfileBanQuery = Backbone.View.extend({
    el: '#ban-button',

    events: {
        'click': 'banUser'
    },

    initialize: function() {
        this.model = new ProfileBanModel();
        this.listenTo(this.model, 'request', this.showLoader);
        this.listenTo(this.model, 'sync', this.redirectToProfile);
        this.listenTo(this.model, 'error', this.showError);
    },

    banUser: function(event) {
        event.preventDefault();
        let id = this.$el.data('code')

        this.model.save({ code: id }, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    showLoader: function() {
        $('#main-loader').addClass('show');
        this.$el.prop('disabled', true);
    },

    redirectToProfile: function(model, response) {
        if (response.redirect_url) {
            window.location.href = response.redirect_url;
        }
    },

    showError: function(model, error) {
        if (error && error.responseJSON && error.responseJSON.message) {
            new alert().errorWindowShow($('.error'), error.responseJSON.message);
            $('html, body').scrollTop(0);
            $('#main-loader').removeClass('show');
        }

        this.$el.prop('disabled', false);
    }
});

let templateChart   = new ProfileChart();
let verifyQuery     = new VerifyQuery();
let profileBanQuery = new ProfileBanQuery();

