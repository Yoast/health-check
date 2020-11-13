window.onerror = function( msg, url, lineNo, columnNo, err ) {
	let error = {};
	const string = msg.toLowerCase();
	const substring = 'script error';
	if ( string.indexOf( substring ) > -1 ) {
		error = {
			message: 'External Script error',
			url: 'external',
			line: '',
			column: '',
			screen: window.SiteHealthErrorLogger.screen,
		};
		alert( 'Script Error: See Browser Console for Detail' );
	} else {
		error = {
			message: msg,
			url: url,
			line: lineNo,
			column: columnNo,
			screen: window.SiteHealthErrorLogger.screen,
		};
	}

	console.log( error );
	return false;
};
