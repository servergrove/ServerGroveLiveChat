if (typeof(ServerGrove) == 'undefined') {
    var ServerGrove = new Object();
}

if (typeof(ServerGrove.Sound) == 'undefined') {
    ServerGrove.Sound = {
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
            if (ServerGrove.Sound._mute == -1) {
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
            ServerGrove.Sound._mute *= -1;
        }
    };
}