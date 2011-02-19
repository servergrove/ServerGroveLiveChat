var Chat = function(){
    this.initialize();
}

Chat._instance = null;
Chat.create = function(){
    Chat._instance = new Chat();
}

/**
 * @return {Chat}
 */
Chat.get = function(){
    return Chat._instance;
}

Chat.prototype = {
    sendingAction: false,
    cmdClear: null,
    cmdSend: null,
    cmdAppendCanned: null,
    cboCanned: null,
    txtMessage: null,
    initialize: function(){
        this.cmdSend = document.getElementById("btnSend");
        this.cmdClear = document.getElementById("btnClear");
        this.cboCanned = document.getElementById("canned");
        this.cmdAppendCanned = document.getElementById("appendCanned");
        this.txtMessage = document.getElementById("msg");
        this.loadEvents();
    },
    loadEvents: function(){
        var cmdSend = this.cmdSend;
        var cboCanned = this.cboCanned;
        var cmdAppendCanned = this.cmdAppendCanned;
        var txtMessage = this.txtMessage;
        jQuery(this.txtMessage).keyup(function(event){
            cmdSend.disabled = jQuery(this).val().trim().length == 0;
            if (!cmdSend.disabled && event.keyCode == 13) {
                jQuery(cmdSend).click();
            } else {
                if (!Chat.get().sendingAction) {
                    Chat.get().sendingAction = true;
                    jQuery.ajax({
                        type: "GET",
                        url: "{{ path('sglc_chat_user_action', {'id': chat.getId(), 'action': 'typing'})}}",
                        cache: false,
                        success: function(){
                            Chat.get().sendingAction = false;
                        },
                        error: function(){
                            Chat.get().sendingAction = false;
                        }
                    });
                }
            }
        }).change(function(){
            cmdSend.disabled = jQuery(this).val().trim().length == 0;
        });
        
        jQuery(this.cmdClear).click(function(){
            cmdSend.disbled = true;
            jQuery(txtMessage).val('').focus();
        });
        
        jQuery(this.cmdSend).click(function(){
            this.disabled = true;
            Chat.get().sendMessage(jQuery(txtMessage).val());
            jQuery(txtMessage).val('').focus();
        });
        
        jQuery(this.cboCanned).change(function(){
            cmdAppendCanned.disabled = this.value == '---';
        });
        
        jQuery(this.cmdAppendCanned).click(function(){
            jQuery(txtMessage).val(jQuery(cboCanned).val()).focus();
            cmdSend.disabled = jQuery(txtMessage).val().trim().length == 0;
        });
    },
    sendMessage: function(text){
        jQuery.ajax({
            type: "POST",
            url: "{{ path('sglc_chat_send', {'id': chat.getId()})}}",
            data: {
                msg: escape(text.trim())
            },
            cache: false,
            success: function(msg){
            }
        });
    },
    loadTimer: function(){
        Chat.get()._updateInterval = window.setTimeout(function(){
            Chat.get().updateMessages();
            Chat.get().scrollMessagesContainer();
        }, 1000);
    },
    _updateInterval: null,
    updateMessages: function(){
        jQuery.ajax({
            type: "GET",
            url: "{{ path('sglc_chat_messages', {'_format': 'json', 'id': chat.getId()})}}",
            dataType: 'json',
            cache: false,
            success: function(json){
                try {
                    if (typeof(json.action) != 'undefined') {
                        Chat.get().displayAction(json.action);
                    } else {
                        Chat.get().removeAction();
                    }
					Chat.get().scrollMessagesContainer();
                    if (typeof(json.messages) != 'undefined') {
                        jQuery.each(json.messages, function(i, item){
                            Chat.get().appendMessage(item);
							Chat.get().scrollMessagesContainer();
                        });
                    }
                } catch (e) {
                }
                Chat.get().loadTimer();
            },
            error: function(XMLHttpRequest){
                if (XMLHttpRequest.status == 404) {
                    Chat.get().appendMessage({
                        content: 'No chat session found. <a href="{{path("sglc_chat_homepage")}}">Please start a new chat</a>',
                        name: 'Admin',
                        idOperator: true
                    });
                } else {
                    Chat.get().loadTimer();
                }
            }
        });
    },
    displayAction: function(item){
        jQuery('#messages #action').html(item);
    },
    removeAction: function(){
        jQuery('#messages #action').html('&nbsp;');
    },
    appendMessage: function(item){
        var _table = document.createElement('table');
        var _row = document.createElement('tr');
        var _cell1 = document.createElement('td');
        var _cell2 = document.createElement('td');
        
        jQuery(_table).attr({
            cellpadding: 3,
            cellspacing: 0,
            border: 0,
            width: "100%"
        });
        jQuery(_row).attr({
            valign: 'top'
        });
        if (item.isOperator) {
            jQuery(_row).css({
                'background-color': '#F8F8F8'
            });
        }
        
        jQuery(document.createElement('div')).addClass('name').addClass(item.isOperator ? 'operator' : 'client').html(item.name + ': ').appendTo(_cell1).parent().attr('width', '70px').appendTo(_row);
        
        jQuery(_cell2).html(item.content).appendTo(_row).parent().appendTo(_table);
        
        jQuery(_table).appendTo('#messages #user-messages');
    },
    scrollMessagesContainer: function(){
        jQuery('#messages').scrollTop(jQuery('#messages').outerHeight());
    },
    _checkStatusEnabled: true,
    checkStatus: function(){
        jQuery.ajax({
            type: "GET",
            url: "{{ path('sglc_chat_status', {'id': chat.getId()})}}",
            dataType: 'script',
            cache: false,
            success: function(){
                if (Chat.get()._checkStatusEnabled) {
                    window.setTimeout(function(){
                        Chat.get().checkStatus();
                    }, 1000);
                }
            }
        });
    },
    start: function(){
        jQuery('#connecting').hide();
        jQuery('#chat').show();
        Chat.get()._checkStatusEnabled = false;
        Chat.get().updateMessages();
    }
};


jQuery(document).ready(function(){
    Chat.create();
    Chat.get().checkStatus();
});
