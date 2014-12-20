(function( $ ) {
	'use strict';

	/**
	 * All of the code for your Dashboard-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	 
	$('#wordpress-indesign-exchange-management').ready(function() {
		var downloadurl = '',
			IdExportOptions = {
				filename: 'export.xml',
				rootElement: 'indesign-export'
			};

		var setWordpressIndesignExchangeDownloadUrl = function() {
			downloadurl = exporturl + '?indesign_download=1';
			Object.keys(IdExportOptions).forEach(function(k) {
				downloadurl += '&' + k + '=' + IdExportOptions[k];
			});
			$('#download-indesign-exchange-xml').attr('href', downloadurl);
		};

		$('#download-indesign-exchange-filename').on('keyup', function() {
			IdExportOptions.filename = $(this).val();
			setWordpressIndesignExchangeDownloadUrl();
		});
		$('#download-indesign-exchange-root-element').on('keyup', function() {
			IdExportOptions.rootElement = $(this).val();
			setWordpressIndesignExchangeDownloadUrl();
		});
	});

})( jQuery );
