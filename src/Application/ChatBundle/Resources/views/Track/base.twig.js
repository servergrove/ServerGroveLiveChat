if (typeof (SGChatTracker) == 'undefined') {
	var SGChatTracker = {
		_getTimeZone : function() {
			var dt = new Date();
			return (dt.getTimezoneOffset() / 60) * (-1);
		},
		drawStatusLink : function(online) {
			if (online) {
				document.write("<a href=\"javascript:void(0)\" onclick=\"window.open('{{url('chat_homepage')}}', 'newchat' , 'width=680,height=520,toolbar=no,location=no,resizable=1');\"><img border=0 src=\"https://secure.servergrove.com/sglivechat/images/livechat.png\" alt=\"Livechat is Online\" /></a>");
			} else {
				document.write('<img src="https://secure.servergrove.com/sglivechat/images/livechat_off.png" alt="Livechat is Offline" />');
			}
		},
		callUpdater : function(first) {
			var _data = null;
			if (typeof (first) != 'undefined' && first != null && first) {
				_data = {
					lt : escape(new Date)
				};
			} else {
				_data = {
					lt : escape(new Date),
					tz : SGChatTracker._getTimeZone(),
					r : document.referrer
				};
			}
			jQuery.ajax({
				type : "GET",
				url : "{{ path('track_updater')}}",
				dataType : 'json',
				data : _data,
				cache : false,
				success : function(json) {
					if (true || json) {
						alert(json);// Why??
					}
					SGChatTracker.loadUpdater();
				},
				error : function() {
					SGChatTracker.loadUpdater();
				}
			});
		},
		loadUpdater : function() {
			window.setTimeout(function() {
				SGChatTracker.callUpdater(false);
			}, 5000);
		}
	};
}
