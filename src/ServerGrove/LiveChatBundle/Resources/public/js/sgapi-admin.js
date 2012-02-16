(function(window, $, Backbone, _) {

    var sg = window.ServerGrove = {

        /**
         * Model namespace
         */
        Model: {
            Chat: {
                Request: Backbone.Model.extend({
                    parse: function(response) {
                        return response;
                    }
                })
            }
        },

        Collection: {
            Chat: {
                Request: Backbone.Collection.extend({
                    url: '/sglivechat/admin/api/active-sessions.json',

                    initialize: function() {
                        this.model = sg.Model.Chat.Request;
                    },

                    parse: function(response) {
                        if (!response.result) {
                            return;
                        }

                        return response.rsp;
                    }
                })
            }
        },

        /**
         * View namespace
         */
        View: {
            /**
             * Chat namespace
             */
            Chat: {

                /**
                 * Chat request view
                 */
                Request: Backbone.View.extend({
                    tagName: "tr",

                    events: {
                        "click .btn-group .btn-primary": "openAcceptPopup",
                        "click .btn-group .btn-info": "openChatPopup",
                        "click .btn-group .btn-danger": "closeChatPopup"
                    },

                    openAcceptPopup: function() {
                        this.openPopup(this.model.get("acceptUrl"));
                    },

                    openChatPopup: function() {
                        this.openPopup(this.model.get("loadUrl"));
                    },

                    openPopup: function(url) {
                        window.open(url, "livechat" + this.model.get("id"), "width=700,height=575,toolbar=no,location=no");
                    },

                    closeChatPopup: function() {
                        $.ajax({url: this.model.get('closeUrl')});
                    },

                    render: function() {
                        this.$el.html(_.template($("#chat-request-item").html(), {model: this.model.toJSON()}));

                        if (this.model.get("closed")) {
                            var self = this;
                            _(this.model.get("rating").grade).times(function() {
                                self.$('.btn-group').append(document.createTextNode("*"));
                            });
                            this.$('.btn-group').append(document.createTextNode(" - "));
                            this.$('.btn-group').append(document.createTextNode(this.model.get("rating").comments));
                        } else if (this.model.get("acceptable")) {
                            this.$('.btn-group').append(_.template($("#chat-accept-button").html(), {model: this.model.toJSON()}));
                        } else if (this.model.get("inProgress")) {
                            this.$('.btn-group').append(_.template($("#chat-close-button").html(), {model: this.model.toJSON()}));
                        } else {
                            this.$('.btn-group').append(_.template($("#chat-reload-button").html(), {model: this.model.toJSON()}));
                        }

                        return this.el;
                    }
                })
            }
        },

        Console: function(tbody) {
            this._tbody = tbody;
            this._requests = new sg.Collection.Chat.Request();
        }
    };

    _.extend(sg.Console.prototype, Backbone.Events, {
        _requests: null,
        _tbody: null,

        start: function() {
            var fetch = function(requests, tbody) {
                requests.fetch({
                    success: function(collection) {
                        $(tbody).empty();

                        collection.each(function(request) {
                            $(tbody).append((new sg.View.Chat.Request({model: request})).render())
                        });

                        window.setTimeout(function() {
                            fetch.call(this, requests, tbody);
                        }, 5000);
                    },
                    error: function() {
                        window.setTimeout(function() {
                            fetch.call(this, requests, tbody);
                        }, 5000);
                    }
                });
            };

            fetch.call(this, this._requests, this._tbody);
        }
    });

})(window, jQuery, Backbone, _.noConflict());