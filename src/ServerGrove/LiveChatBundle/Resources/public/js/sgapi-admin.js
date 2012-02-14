(function(window, $, Backbone, _) {

    var sg = window.ServerGrove = {

        /**
         * Model namespace
         */
        Model: {
            Chat: {
                Request: Backbone.Model.extend({

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
                        "click .btn-group .btn-danger": "confirmClosure"
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

                    confirmClosure: function() {
                        return confirm("Are you sure?");
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
        }
    };

})(window, jQuery, Backbone, _.noConflict());