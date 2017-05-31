var express = require('express')
  , app = express()
  , http = require('http')
  , server = http.createServer(app)
  , io = require('socket.io').listen(server)
  , MongoClient = require('mongodb').MongoClient
  , ObjectId = require('mongodb').ObjectID
  , format = require('util').format;
server.listen(8080);

var chatters = {};
var arrOnline = {};

io.sockets.on('connection', function (socket) {
	socket.on('addUser', function(userID){
		mongoQuery(function(db){
		    db.collection("tb_contact").findOne({ _id : ObjectId(userID), is_employee : 1 }, { is_employee : true, first_name: true, last_name : true }, function(err, result){
				socket.userID  = userID;
		    	var name = (result.first_name != undefined ? result.first_name : "") + " " + (result.last_name != undefined ? result.last_name : "");
		    	chatters[userID] = {};
    	    	chatters[userID].name = name;
				arrOnline[userID] = name;
				socket.join('to-'+userID);
				io.sockets.emit('online', arrOnline, chatters);
				db.close();
		    });
		});
	});

	socket.on('sendChat', function (data) {
		sendProcess(data);
	});

	var sendProcess = function(data){
		var message = [];
		var date = currentDate();
		data.message = htmlentities(data.message);
		data.message = data.message.replace(/\n/g, "<br />");
		mongoQuery(function(db){
		    db.collection("tb_chat").insert({
		    	deleted : false,
		    	from : ObjectId(socket.userID),
		    	to : ObjectId(data.send_to),
		    	message: data.message,
		    	read : false,
		    	modified_by : ObjectId(socket.userID),
   				date_modified : new Date(),
	    	}, { w : 1 }, function(err, result){
	    		db.close();
		    });
		});
		message[0] = {
			from : socket.userID,
			to : data.send_to,
			message : data.message,
			time : date,
			read: 0
		};
		socket.join('to-' + data.send_to);
		io.sockets.in('to-' + data.send_to).emit('updateChat', message);
		socket.leave('to-' + data.send_to);
		socket.join('to-'+socket.userID);
	};

	socket.on('sendMessage', function(data){
		socket.broadcast.emit('receiveMessage', data);
		console.log('Sent Message');
	});

	socket.on('disconnect', function(){
		delete arrOnline[socket.userID];
		io.sockets.emit('online', arrOnline, chatters);
	});

	var mongoQuery = function(callBack){
		MongoClient.connect('mongodb://localhost/jobtraq', function(err, db) {
		    if(err) throw err;
		    if( typeof callBack == "function" ) {
		    	callBack(db);
		    }
		})
	};

	var currentDate = function(){
		var currentDate = new Date()
		var day = currentDate.getDate();
		if(day < 10)
			day = "0" + day;
		var month = currentDate.getMonth();
		var year = currentDate.getFullYear();
		var arrMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		month = arrMonth[month];
		var hours = currentDate.getHours();
		var minutes = currentDate.getMinutes();
		if (minutes < 10)
			minutes = "0" + minutes;
		var secs = currentDate.getSeconds();
		if (secs < 10)
			secs = "0" + secs;
		return hours + ":" + minutes + ":" + secs + " " + month + " " + day + ", " + year;
	};

	var htmlentities = function(string, quote_style, charset, double_encode) {
		var hash_map = get_html_translation_table('HTML_ENTITIES', quote_style),
		symbol = '';
		string = string == null ? '' : string + '';

		if (!hash_map) {
			return false;
		}

		if (quote_style && quote_style === 'ENT_QUOTES') {
			hash_map["'"] = '&#039;';
		}

		if ( !! double_encode || double_encode == null) {
			for (symbol in hash_map) {
				if (hash_map.hasOwnProperty(symbol)) {
					string = string.split(symbol)
			  			.join(hash_map[symbol]);
				}
			}
		} else {
			string = string.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g, function(ignore, text, entity) {
				for (symbol in hash_map) {
					if (hash_map.hasOwnProperty(symbol)) {
						text = text.split(symbol)
							.join(hash_map[symbol]);
					}
				}
				return text + entity;
			});
		}
		return string;
	};

	var get_html_translation_table = function(table, quote_style) {
		var entities = {},
		hash_map = {},
		decimal;
		var constMappingTable = {},
		constMappingQuoteStyle = {};
		var useTable = {},
		useQuoteStyle = {};

		// Translate arguments
		constMappingTable[0] = 'HTML_SPECIALCHARS';
		constMappingTable[1] = 'HTML_ENTITIES';
		constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
		constMappingQuoteStyle[2] = 'ENT_COMPAT';
		constMappingQuoteStyle[3] = 'ENT_QUOTES';

		useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
		useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() :
		'ENT_COMPAT';

		if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
			throw new Error('Table: ' + useTable + ' not supported');
		// return false;
		}
		entities['38'] = '&amp;';
		if (useTable === 'HTML_ENTITIES') {
			entities['160'] = '&nbsp;';
			entities['161'] = '&iexcl;';
			entities['162'] = '&cent;';
			entities['163'] = '&pound;';
			entities['164'] = '&curren;';
			entities['165'] = '&yen;';
			entities['166'] = '&brvbar;';
			entities['167'] = '&sect;';
			entities['168'] = '&uml;';
			entities['169'] = '&copy;';
			entities['170'] = '&ordf;';
			entities['171'] = '&laquo;';
			entities['172'] = '&not;';
			entities['173'] = '&shy;';
			entities['174'] = '&reg;';
			entities['175'] = '&macr;';
			entities['176'] = '&deg;';
			entities['177'] = '&plusmn;';
			entities['178'] = '&sup2;';
			entities['179'] = '&sup3;';
			entities['180'] = '&acute;';
			entities['181'] = '&micro;';
			entities['182'] = '&para;';
			entities['183'] = '&middot;';
			entities['184'] = '&cedil;';
			entities['185'] = '&sup1;';
			entities['186'] = '&ordm;';
			entities['187'] = '&raquo;';
			entities['188'] = '&frac14;';
			entities['189'] = '&frac12;';
			entities['190'] = '&frac34;';
			entities['191'] = '&iquest;';
			entities['192'] = '&Agrave;';
			entities['193'] = '&Aacute;';
			entities['194'] = '&Acirc;';
			entities['195'] = '&Atilde;';
			entities['196'] = '&Auml;';
			entities['197'] = '&Aring;';
			entities['198'] = '&AElig;';
			entities['199'] = '&Ccedil;';
			entities['200'] = '&Egrave;';
			entities['201'] = '&Eacute;';
			entities['202'] = '&Ecirc;';
			entities['203'] = '&Euml;';
			entities['204'] = '&Igrave;';
			entities['205'] = '&Iacute;';
			entities['206'] = '&Icirc;';
			entities['207'] = '&Iuml;';
			entities['208'] = '&ETH;';
			entities['209'] = '&Ntilde;';
			entities['210'] = '&Ograve;';
			entities['211'] = '&Oacute;';
			entities['212'] = '&Ocirc;';
			entities['213'] = '&Otilde;';
			entities['214'] = '&Ouml;';
			entities['215'] = '&times;';
			entities['216'] = '&Oslash;';
			entities['217'] = '&Ugrave;';
			entities['218'] = '&Uacute;';
			entities['219'] = '&Ucirc;';
			entities['220'] = '&Uuml;';
			entities['221'] = '&Yacute;';
			entities['222'] = '&THORN;';
			entities['223'] = '&szlig;';
			entities['224'] = '&agrave;';
			entities['225'] = '&aacute;';
			entities['226'] = '&acirc;';
			entities['227'] = '&atilde;';
			entities['228'] = '&auml;';
			entities['229'] = '&aring;';
			entities['230'] = '&aelig;';
			entities['231'] = '&ccedil;';
			entities['232'] = '&egrave;';
			entities['233'] = '&eacute;';
			entities['234'] = '&ecirc;';
			entities['235'] = '&euml;';
			entities['236'] = '&igrave;';
			entities['237'] = '&iacute;';
			entities['238'] = '&icirc;';
			entities['239'] = '&iuml;';
			entities['240'] = '&eth;';
			entities['241'] = '&ntilde;';
			entities['242'] = '&ograve;';
			entities['243'] = '&oacute;';
			entities['244'] = '&ocirc;';
			entities['245'] = '&otilde;';
			entities['246'] = '&ouml;';
			entities['247'] = '&divide;';
			entities['248'] = '&oslash;';
			entities['249'] = '&ugrave;';
			entities['250'] = '&uacute;';
			entities['251'] = '&ucirc;';
			entities['252'] = '&uuml;';
			entities['253'] = '&yacute;';
			entities['254'] = '&thorn;';
			entities['255'] = '&yuml;';
		}

		if (useQuoteStyle !== 'ENT_NOQUOTES') {
			entities['34'] = '&quot;';
		}
		if (useQuoteStyle === 'ENT_QUOTES') {
			entities['39'] = '&#39;';
		}
		entities['60'] = '&lt;';
		entities['62'] = '&gt;';
		// ascii decimals to real symbols
		for (decimal in entities) {
			if (entities.hasOwnProperty(decimal)) {
				hash_map[String.fromCharCode(decimal)] = entities[decimal];
			}
		}

		return hash_map;
	}
});
