import {ProfileDomain as Profile} from "../../../../../public/js/domains/profileDomain.js";
import {alert} from "../../../../../public/js/helpers/alert.js";

let ProfileModel = Backbone.Model.extend({
    url: '/profile/update',
    constructor: function(attributes, options) {
        Backbone.Model.apply(this, arguments);
        this.profile = new Profile({
            profileId: $('#profile-update').data('profile-id'),
            name: $('#profile-name').val(),
            status: $('#status').val(),
            about_me: $('#about').val(),
            cid: $('#cid').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            role: null,
            get_letter_release: $('#mailing').is(":checked"),
            avatar_name: $('#avatar-name').text(),
            avatar: ''
        });
    },
});

let HandleFileChangeView = Backbone.View.extend({
    el: '#fileInput',

    events: {
        'change': 'handleFileChange'
    },

    initialize: function(options) {
        this.model = options.model;
        $('#avatar-remove').on('click', this.handleFileRemove.bind(this));
    },

    handleFileChange: function() {
        let fileInput = this.el;
        let file      = fileInput.files[0];

        if (file instanceof Blob) {
            let reader = new FileReader();

            let ava  = $("#avatar");
            let self = this;

            reader.onload = function (event) {
                self.model.profile.avatar_name = file.name;
                ava.attr("src", event.target.result);
                self.model.profile.avatar = event.target.result;
            };

            reader.readAsDataURL(file);
        }
    },

    handleFileRemove: function (event) {
        event.preventDefault();
        $("#avatar").attr('src', '../../images/350.png');
        $("#file").val("");
        this.model.profile.avatar_name = "Аватар не выбран";
        this.model.profile.avatar = "";
    }
});

let RoleChangeView = Backbone.View.extend({
    el: '.preview-detail-files',

    events: {
        'click': 'onClick'
    },

    initialize: function(options) {
        this.model = options.model;
    },

    onClick: function(e) {
        e.preventDefault();
        let $clickedElement = $(e.currentTarget);
        this.model.profile.role = $clickedElement.data('role');

        $('.preview-detail-files').each(function() {
            $(this).css("color", "black");
        });

        $clickedElement.css("color", "var(--pink)");
    }
});

let InputChangeView = Backbone.View.extend({
    el: '#profile-name, #status, #about, #cid, #email, #password, #mailing',

    events: {
        'change': 'handleInputChange'
    },

    initialize: function() {
        this.listenTo(this.model, 'change', this.render);
    },

    handleInputChange: function(event) {
        let $input = $(event.target);
        let attributeName = $input.attr('name');

        let attributeValue;
        if ($input.attr('type') === 'checkbox') {
            attributeValue = $input.prop('checked');
        } else {
            attributeValue = $input.val();
        }

        this.model.profile[attributeName] = attributeValue;
    }
});

let ProfileUpdateQuery = Backbone.View.extend({
    el: '#profile-update',

    events: {
        'submit': 'submitForm'
    },

    initialize: function (options) {
        this.model  = options.model;
        this.loader = $('#main-loader');
        this.window = $('.error');
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

        this.model.save(this.model.profile, {
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    onSubmitSuccess: function (model, response) {
        if (response.redirect_url) {
            window.location.href = response.redirect_url;
        }
    },

    onSubmitError: function (model, error) {
        if (error && error.responseJSON && error.responseJSON.message)
            new alert().errorWindowShow(this.window, error.responseJSON.message);
        $('html, body').scrollTop(0);
        $('#main-loader').removeClass('show');
        this.submitButton.prop('disabled', false);
    }
});

let profile = new ProfileModel();
let handleFileChangeView = new HandleFileChangeView({ model: profile });
let roleChangeView     = new RoleChangeView({ model: profile });
let inputChangeView    = new InputChangeView({ model: profile });
let profileUpdateQuery = new ProfileUpdateQuery({ model: profile })
