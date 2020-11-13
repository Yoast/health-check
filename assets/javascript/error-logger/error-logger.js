( function() {
	function postError( error ) {
		const http = new XMLHttpRequest();
		const params = 'action=health-check-log-errors' +
			'&_ajax_nonce=' + window.SiteHealthErrorLogger.nonce +
			'&error=' + encodeURIComponent( JSON.stringify( error ) );
		http.open( 'POST', window.ajaxurl, true );
		http.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded;' );
		http.onreadystatechange = function() {
			console.warn( 'Error successfully logged to', window.SiteHealthErrorLogger.logURL, error );
		};
		http.send( params );
	}

	let errorMessage = '';

	window.onerror = function( msg, url, lineNo, columnNo, err ) {
		let error = {};
		const string = msg.toLowerCase();
		const substring = 'script error';
		if ( string.indexOf( substring ) > -1 ) {
			console.log( msg, url, lineNo, columnNo, err );
			error = {
				message: 'External Script error',
				url: 'external',
				line: '',
				column: '',
				screen: window.SiteHealthErrorLogger.screen,
			};
		} else {
			error = {
				message: msg,
				url: url,
				line: lineNo,
				column: columnNo,
				screen: window.SiteHealthErrorLogger.screen,
			};
		}

		// Prevent the same error message being logged twice.
		if ( errorMessage !== msg ) {
			errorMessage = msg;
			postError( error );
		}

		return false;
	};
}() );
