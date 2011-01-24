var mute = -1;						// Mute sound when = 1

// Call flashsound swf to play desired sound
function playSound(el, sound){
        if( mute == -1 ){
                if( el ){
                        if( typeof el.TGotoLabel != "undefined" ){
                                el.TGotoLabel("/", sound);
                                el.TPlay("/");
                        }
                }
        }
}

// Toggle sound mute state
function muteSound(){
//        var e = document.getElementById("sound");
//       e.innerHTML = ( mute == 1 ) ? "Sound On" : "Sound Off";
//        e.className = ( mute == 1 ) ? "soundOn" : "soundOff";
        mute *= -1;
//        inputFocus();
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}



