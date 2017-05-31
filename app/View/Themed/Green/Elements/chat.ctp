<div id="chat-window" style="display:none;">
	<div id="chatters" style="height: 99%;">
	</div>
</div>
<?php if(isset($_SESSION['arr_user']['contact_id']) && SOCKET_URL): ?>
<script src="<?php echo URL.'/js/ion.sound.min.js'; ?>" type="text/javascript"></script>
<script src="<?php echo URL.'/js/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
	ion.sound({
	    sounds: [
	        {
	            name: "notification"
	        },
	    ],
	    volume: 1,
	    path: "<?php echo URL.'/sounds/'; ?>",
	    preload: true
	});
	var socket = io.connect('<?php echo SOCKET_URL ?>:8080');
	var arrOnline = {};
	var arrChatters = {};
	var arrNewMessage = {};
	var chatWindow = $("#chat-window");
	var chatActive = false;
	var fromId = "<?php echo $_SESSION['arr_user']['contact_id']; ?>";

	//this variable represents the total number of popups can be displayed according to the viewport width
	var totalPopups = 0;

	//arrays of popups ids
	var arrPopups = [];
	var arrPopupsOpened = [];
	socket.on('connect', function(){
	    socket.emit('addUser', "<?php echo $_SESSION['arr_user']['contact_id']; ?>");
	});
	socket.on('online', function (online) {
		if( $(".chatters", chatWindow).length ) {
			onlineChatters(online);
		} else {
			arrOnline = online;
		}
    });
    socket.on('updateChat', function (message) {
    	if( $("#chatter-"+ message[0].from).length ) {
    		chatterId = message[0].from;
    	} else {
    		chatterId = message[0].to;
    	}
    	if( !$(".chatters", chatWindow).length ) {
    		if( arrNewMessage[chatterId] == undefined ) {
    			arrNewMessage[chatterId] = 0;
    		} else {
    			arrNewMessage[chatterId]++;
    		}
    	}
    	var chatterPopup = $("[data-chatter-id="+ chatterId +"]");
    	if( !chatterPopup.length || chatterPopup.is(":hidden") ) {
    		var chatter = $("#chatter-"+ chatterId);
    		if( $("i", chatter).length ) {
    			var count = parseInt($("i", chatter).text());
    			$("i", chatter).text(++count);
    		} else {
    			chatter.append('<i>1<i>');
    		}
    		recountNewMessages(1, "plus");
    		if( !chatterPopup.is(":hidden") ) return false;
    	}
    	$(".popup-messages .mCSB_container", chatterPopup).append(getMessageContent(message));
    	$(".popup-messages", chatterPopup).mCustomScrollbar("update");
    	$(".popup-messages", chatterPopup).mCustomScrollbar("scrollTo", "bottom");
    });

	function tabAlert(msg){
        var oldTitle = document.title;
        var timeoutId;
        var blink = function() { document.title = document.title == msg ? ' ' : msg; };
        var clear = function() {
            clearInterval(timeoutId);
            document.title = oldTitle;
            window.onmousemove = null;
            timeoutId = null;
        };
        return function () {
            if (!timeoutId) {
                timeoutId = window.setInterval(blink, 1000);
                window.onmousemove = clear;
            }
        };
    }

    function onlineChatters(arrOnline) {
    	if( !Object.keys(arrOnline).length ) {
    		return false;
    	}
    	$(".chatters", chatWindow).removeClass("online");
    	var html = "";
		for(i in arrOnline) {
			if( i == fromId  ) continue;
			var count = $("i", "#chatter-"+ i).text();
			$("#chatter-"+ i).remove();
			html += '<li class="chatters online" onclick="registerPopup(\'' + i + '\',\''+ arrOnline[i] +'\')" id="chatter-'+ i +'">' +
						'<span>'+ arrOnline[i] + '</span>' +
						( count ? '<i>'+ count +'</i>' : '' )+
					'</li>';
		}
		$("#chatters .mCSB_container", chatWindow).prepend(html);
    }

	function getChatters() {
		$.ajax({
			url: "<?php echo URL.'/users/get_chatters' ?>",
			success: function(chatters) {
				chatters = $.parseJSON(chatters);
				var html = "";
				$("span#chat").html("Chat<i></i>");
				if( chatters["new_message"] ) {
					$("i","span#chat").html("("+chatters["new_message"] +")");
					$("span#chat").closest("li").css("background-color", "#325c99");
					$("span#chat").css("color", "#fff");
				}
				var chatters = chatters["chatters"];
				for(var i in chatters) {
					arrChatters[chatters[i]._id] = {};
					arrChatters[chatters[i]._id].name = chatters[i].short_name;
					arrChatters[chatters[i]._id].color = chatters[i].color;
					arrChatters[chatters[i]._id].count = chatters[i].count + (arrNewMessage[chatters[i]._id] ? arrNewMessage[chatters[i]._id] : 0);
					if( chatters[i]._id == fromId  ) continue;
					html += '<li class="chatters" onclick="registerPopup(\''+chatters[i]._id+'\',\''+chatters[i].name+'\')" id="chatter-'+chatters[i]._id+'">' +
								'<span>'+chatters[i].name+'</span>' +
								( chatters[i].count ? '<i>'+ chatters[i].count +'</i>' : '' )+
							'</li>';
				}
				$("#chatters", chatWindow).html(html)
										.mCustomScrollbar({
											scrollButtons:{
												enable:false
											},
											advanced:{
											        updateOnContentResize: true,
											    },
											autoHideScrollbar : true,
										});
				onlineChatters(arrOnline);
		        chatWindow.kendoWindow({
			    	iframe: false,
			        actions: ["Close"],
			        width: "210px",
			        height: "70%",
			        position: {
			        	bottom: 27,
			        },
					title: false,
					pinned: false,
					open: function(){
						chatWindow.parent().addClass("right_2");
						chatActive = true;
					},
					close: function(){
						chatActive = false;
					}
			    });
			}
		})
	}

	function recountNewMessages(count, opertator) {
		if( opertator == undefined ) {
			opertator = "minus";
		}
		count = parseInt(count);
		var currentCount = parseInt($("i","span#chat").text().replace("(","").replace(")","") || 0);
		if( opertator == "minus" ) {
			currentCount -= count;
		} else {
			currentCount += count;
		}
		if( !currentCount ) {
			$("span#chat").closest("li").animate({
	          backgroundColor: "#c9c9c9",
	        }, 1000 );

			$("span#chat").html("Chat<i></i>").css("color","#000");
		} else {
    		ion.sound.play("notification");
			tabAlert("New messages ( "+currentCount+" )");
			$("i","span#chat").text("("+ currentCount +")");
			$("span#chat").css("color", "#fff");
			$("span#chat").closest("li").css("background-color", "#325c99");
		}
	}

	function getMessageContent(content) {
		var html = "";
		for( var i in content ) {
			var out = false;
			if( content[i].from == fromId ) {
				out = true;
			}
			html += '<div class="message-box '+ (out ? 'right' : '') +'">' +
						'<div class="chatter-name" title="'+ content[i].time +'" style="background-color: '+ arrChatters[content[i].from].color +'">'+ arrChatters[content[i].from].name +'</div>' +
						'<div class="message">'+ content[i].message +'</div>' +
						( !out && !content[i].read ? "<i></i>" : '') +
					'</div>';
		}
		return html;
	}

	function getMessage(id) {
	    var chatterPopup = $("[data-chatter-id="+id+"]");
		$(".popup-messages", chatterPopup).html('<div style=" text-align: center; padding-top: 40%;"><img src="<?php echo URL.'/img/loading.gif' ?>" /></div>');
		$.ajax({
    		url : "<?php echo URL.'/users/get_chat_content' ?>",
    		type: "POST",
    		data: {from: fromId, to : id},
    		success: function(content){
	    		var content = $.parseJSON(content);
    			setTimeout(function(){
	    			$(".popup-messages", chatterPopup).html(getMessageContent(content));
	    			if(!$(".popup-messages", chatterPopup).hasClass("mCustomScrollbar")){
	    				$(".popup-messages", chatterPopup).mCustomScrollbar({
	    					scrollButtons:{
	    						enable:false
	    					},
	    					advanced:{
	    				        updateOnContentResize: true,
	    				    },
	    				    autoHideScrollbar : true,
	    				    callbacks:{
								onInit: function(object){
									var container = $(".mCSB_container", chatterPopup);
									var top = $(".popup-messages", chatterPopup).outerHeight() - container.outerHeight();
									container.css("top", (top > 0 ? 0 : top) + "px" );
								}
							}
	    				});
	    			}
	    			$(".popup-editor > textarea", chatterPopup).focusin(function(){
	    				if( $("i", chatterPopup).length ) {
		    				setTimeout(function(){
								$.ajax({
									url: "<?php echo URL.'/users/update_chat_read' ?>",
									type: "POST",
									data: {from: id, to: fromId},
									success: function(result){
										if( result == "ok" ) {
											recountNewMessages($("i", chatterPopup).text());
											$("i", chatterPopup).remove();
											$("i", "#chatter-"+ id).remove();
										}
									}
								})
		    				}, 1000);
	    				}
	    			});
	    			$(".popup-editor > textarea", chatterPopup).focus();
    			}, 500);
			}
		});
	}

	function getCaret(el){
        if (el.selectionStart) {
            return el.selectionStart;
        } else if (document.selection) {
            el.focus();
            var r = document.selection.createRange();
            if (r == null) {
                return 0;
            }
            var re = el.createTextRange(),
            rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);
            return rc.text.length;
        }
		return 0;
	}

	function sendMessage(event, id) {
		var textarea = $("textarea", "[data-chatter-id="+ id +"]");
	    var content = textarea.val();
	    if( $.trim(content).length === 0 ) {
	    	return false;
	    }
	    if ( event.keyCode == 13 && event.shiftKey ) {
	       var caret = getCaret(textarea[0]);
	       textarea.val(content.substring(0,caret)+content.substring(caret,content.length));
	       event.stopPropagation();
	    } else if( event.keyCode == 13 ) {
	    	event.preventDefault();
	    	socket.emit( 'sendChat', { message: content, send_to : id } );
	    	textarea.val("");
	    	return false;
	    }
	}

	$("a#chat").click(function(){
		if( !chatActive ) {
		    chatWindow.data("kendoWindow").open();
		} else {
		    chatWindow.data("kendoWindow").close();
		}
	});

	//this function can remove a array element.
	Array.remove = function(array, from, to) {
			var rest = array.slice((to || from) + 1 || array.length);
			array.length = from < 0 ? array.length + from : from;
			return array.push.apply(array, rest);
	};

	//this is used to close a popupclosePopup(id)
	function closePopup(id) {
		for(var i = 0; i < arrPopups.length; i++)
		{
			if(id == arrPopups[i])
			{
				// Array.remove(arrPopups, i);
				$("[data-chatter-id="+id+"]").hide();
				calculatePopups();
				return false;
			}
		}
	}

	//displays the popups. Displays based on the maximum number of popups that can be displayed on the current viewport width
	function displayPopups() {
		var right = 220;

		var i = 0;
		var count = 0;
		var focused = false;
		for(var i; i < arrPopups.length; i++) {
			if(arrPopups[i] != undefined)
			{
				var element = $("[data-chatter-id="+arrPopups[i]+"]");
				if( element.is(":hidden") ) continue;
				element.css("right", right + "px");
				right = right + 275;
				element.show();
				var container = $(".mCSB_container", element);
				container.css("top", ($(".popup-messages", element).outerHeight() - container.outerHeight()) + "px" );
				if( !focused ) {
	    			$(".popup-editor > textarea", element).focus();
	    			focused = true;
				}
				if(++count == totalPopups ) {break;}
			}
		}
	}

	//creates markup for a new popup. Adds the id to popups array.
	function registerPopup(id, name) {
		for(var i = 0; i < arrPopups.length; i++)
		{
			//already registered. Bring it to front.
			if(id == arrPopups[i])
			{
				Array.remove(arrPopups, i);
				arrPopups.unshift(id);
				$("[data-chatter-id="+ id +"]").show();
				calculatePopups();
				return;
			}
		}

		var element = '<div class="popup-box chat-popup" data-chatter-id="'+ id +'">' +
						'<div class="popup-head">' +
				 			'<div class="popup-head-left">'+ name +'</div>' +
				 			'<div class="popup-head-right">' +
				 				'<a href="javascript:void(0)" onclick="closePopup(\''+ id +'\');">&#10005;</a>' +
							'</div>' +
				 			'<div style="clear: both"></div>' +
				 		'</div>' +
			 			'<div class="popup-messages"></div>' +
			 			'<div class="popup-editor">' +
			 				'<textarea onkeydown="sendMessage(event, \''+ id +'\')" placeholder="Type message hit Enter"></textarea>' +
			 			'</div>' +
			 		'</div>';
		$("body").append(element);
		getMessage(id);
		$("[data-chatter-id="+ id +"]").click(function(){
			$("textarea", this).focus();
		});
		arrPopups.unshift(id);
		calculatePopups();

	}

	function calculatePopups() {
		var width = window.innerWidth;
		if(width < 540)
		{
			totalPopups = 0;
		}
		else
		{
			width = width - 200;
			//320 is width of a single popup box
			totalPopups = parseInt(width/265);
		}
		displayPopups();

	}

	//recalculate when window is loaded and also when window is resized.
	window.addEventListener("resize", calculatePopups);
	window.addEventListener("load", calculatePopups);
	getChatters();
</script>
<?php endif; ?>