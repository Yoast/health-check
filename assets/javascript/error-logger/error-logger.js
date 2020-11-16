/* global console XMLHttpRequest */
/**
 * Subscribes to all JS errors and logs them to the server when they occur.
 *
 * @return {void}
 */
( function() {
	/**
	 * Posts a JS error via WP Ajax to Site Health.
	 *
	 * @param {Object} error The error to log.
	 *
	 * @return {void}
	 */
	function postError( error ) {
		const http = new XMLHttpRequest();
		const params = 'action=health-check-log-errors' +
			'&_ajax_nonce=' + window.SiteHealthErrorLogger.nonce +
			'&error=' + encodeURIComponent( JSON.stringify( error ) );
		http.open( 'POST', window.ajaxurl, true );
		http.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded;' );
		http.onreadystatechange = function() {
			if ( http.readyState === XMLHttpRequest.DONE ) {
				// eslint-disable-next-line no-console
				console.warn( 'Error successfully logged to', window.SiteHealthErrorLogger.logURL, error );
			}
		};
		http.send( params );
	}

	let errorMessage = '';

	/**
	 * Callback function for the onerror event. Logs errors to the server.
	 *
	 * @param {string} msg The error message.
	 * @param {string} url The url of the file where the error occurred.
	 * @param {number} lineNo The line number where the error occurred.
	 * @param {number} columnNo The column number where the error occurred.
	 * @param {ErrorEvent} err The error event.
	 * @return {boolean} False.
	 */
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
