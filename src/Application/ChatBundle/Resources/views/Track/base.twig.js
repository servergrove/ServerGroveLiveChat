
if (typeof(SGChatTracker) == 'undefined') {

    jQuery.timer = function(interval, callback){
        /**
         *
         * timer() provides a cleaner way to handle intervals
         *
         *     @usage
         * $.timer(interval, callback);
         *
         *
         * @example
         * $.timer(1000, function (timer) {
         *     alert("hello");
         *     timer.stop();
         * });
         * @desc Show an alert box after 1 second and stop
         *
         * @example
         * var second = false;
         *     $.timer(1000, function (timer) {
         *             if (!second) {
         *                     alert('First time!');
         *                     second = true;
         *                     timer.reset(3000);
         *             }
         *             else {
         *                     alert('Second time');
         *                     timer.stop();
         *             }
         *     });
         * @desc Show an alert box after 1 second and show another after 3 seconds
         *
         *
         */
        var interval = interval || 100;
        
        if (!callback) return false;
        
        _timer = function(interval, callback){
            this.stop = function(){
                clearInterval(self.id);
            };
            
            this.internalCallback = function(){
                callback(self);
            };
            
            this.reset = function(val){
                if (self.id) clearInterval(self.id);
                
                var val = val || 100;
                this.id = setInterval(this.internalCallback, val);
            };
            
            this.interval = interval;
            this.id = setInterval(this.internalCallback, this.interval);
            
            var self = this;
        };
        return new _timer(interval, callback);
    }
    
    
    
    var SGChatTracker = {
        _getTimeZone: function(){
            var dt = new Date();
            return (dt.getTimezoneOffset() / 60) * (-1);
        },
        drawStatusLink: function(online){
            if (typeof(online)=='undefined') {
                online = SGChatTrackerStatus;
            }
            if (online) {
                document.write("<a href=\"javascript:void(0)\" onclick=\"window.open('{{url('sglc_chat_homepage')}}', 'newchat' , 'width=680,height=520,toolbar=no,location=no,resizable=1');\"><img border=0 src=\"https://secure.servergrove.com/sglivechat/images/livechat.png\" alt=\"Livechat is Online\" /></a>");
            } else {
                document.write('<img src="https://secure.servergrove.com/sglivechat/images/livechat_off.png" alt="Livechat is Offline" />');
            }
        },
        callUpdater: function(first){
            var _data = null;
            var _type = 'GET';
  
            if (typeof(first) != 'undefined' && first != null && first) {
                _data = {
                    lt: escape(new Date().getTime()),
                    tz: SGChatTracker._getTimeZone(),
                    r: encodeURIComponent(document.referrer)
                };
                _type = 'POST';
            } else {
                _data = {
                    lt: escape(new Date().getTime())
                };
            }
            jQuery.ajax({
                type: _type,
                url: "{{ path('sglc_track_updater')}}",
                data: _data,
                cache: false,
                success: function(){
                    SGChatTracker.loadUpdater();
                },
                error: function(){
                    SGChatTracker.loadUpdater();
                }
            });
        },
        loadUpdater: function(){
            window.setTimeout(function(){
                SGChatTracker.callUpdater(false);
            }, 5000);
        },
        Chat: {
            accept: function(id){
                SGChatTracker.Chat.removeInvite();
                window.open('{{path("sglc_chat_accept", {"id": "__idchat__"})}}'.replace("__idchat__", id), 'chat_' + id, 'width=680,height=520,location=0,toolbar=0,resizable=1');
            },
            reject: function(id){
                jQuery('#chatDiv').fadeOut("slow", function(){
                    SGChatTracker.Chat.removeInvite()
                });
                jQuery.get('{{path("sglc_chat_reject", {"id": "__idchat__"})}}'.replace("__idchat__", id));
            },
            removeInvite: function(){
                jQuery('#chatDiv').remove();
            },
            scrollInvite: function(){
                jQuery("#chatDiv").css("position", "fixed");
                jQuery("#chatDiv").css("top", "200px");
                jQuery("#chatDiv").css("border", "3px solid #FDC463");
            },
            openInvite: function(){
                jQuery('#chatDiv').show();
                SGChatTracker.Chat.scrollInvite();
            }
        }
    };

    var SGChatTrackerStatus = false;
}

/*
if (typeof(skipChat) == "undefined") {
    document.write('<div id="chatDiv" style="position: absolute; left: 100px; border: 2px solid #666; border-top: 0; background: url(/sglivechat/images/header_bgr.jpg) repeat-x top;background-color: #fff; width: 330px;"><img src="/sglivechat/images/sg_logo.jpg" width=182 height=37 class="logo" /><div style="clear: both; padding: 10px; ">Chat Invitiation: may I help you with anything? <a href="javascript:void(0);" onclick="acceptChat(410)">Yes</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="rejectChat(410)">No</a></div></div>');
    
    $(document).ready(function(){
        $('#chatDiv').hide();
        $(window).scroll(function(){
            scrollInvite();
        });
    });
}

function getTimeZone(){
    var dt = new Date();
    return (dt.getTimezoneOffset() / 60) * (-1);
}

function processResponse(ajax){
    json = ajax.responseJSON;
}

var sglcu = $.timer(5000, function(timer){
    $.getScript('/sglivechat/f.php/tracker/update?v=2&lt=' + escape(new Date()), processResponse);
});

var sglcs = $.timer(1000 * 3600, function(timer){
    sglcu.stop();
    sglcs.stop();
});*/
