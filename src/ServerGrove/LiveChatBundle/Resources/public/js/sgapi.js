(function($) {
    var sg = window.ServerGrove = {
        Cookie: {
            create: function(name, value, days) {
                var expiration, date = new Date();
                if (days) {
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expiration = '; expires=' + date.toGMTString();
                } else {
                    expiration = "";
                }

                document.cookie = name + "=" + value + expiration + "; path=/";
            },

            read: function(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');

                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];

                    while (c.charAt(0) == ' ') {
                        c = c.substring(1, c.length);
                    }

                    if (c.indexOf(nameEQ) == 0) {
                        return c.substring(nameEQ.length, c.length);
                    }
                }

                return null;
            },

            erase: function(name) {
                sg.Cookie.create(name, "", -1);
            }
        },

        Sound: {
            /**
             * Mute sound when = 1
             */
            _mute: -1,

            /**
             * Call flashsound swf to play desired sound
             *
             * @param el
             * @param sound
             */
            play: function(el, sound) {
                if (sg.Sound._mute == -1) {
                    if (el) {
                        if (typeof el.TGotoLabel != "undefined") {
                            el.TGotoLabel("/", sound);
                            el.TPlay("/");
                        }
                    }
                }
            },

            /**
             * Toggle sound mute state
             */
            mute: function() {
                sg.Sound._mute *= -1;
            }
        }
    };

})(jQuery);