import {alert} from "../../../../../public/js/helpers/alert.js";

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
                        {label: "Лайки", y: 0},
                        {label: "Лайки на комментарии", y: 0},
                        {label: "Желаемые", y: 0},
                        {label: "Подписок", y: 0},
                    ]
                }
            ]
        };

        $('#chartContainer').CanvasJSChart(options);
        this.$('.canvasjs-chart-credit').hide();

        this.listenTo(this.model, 'showData', this.showData);
        this.listenTo(this.model, 'showError', this.showError);
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
                        {label: "Лайки", y: dataChart?.likesToGames ?? 0},
                        {label: "Лайки на комментарии", y: dataChart?.likesToComments ?? 0},
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
let profileBanQuery = new ProfileBanQuery();
