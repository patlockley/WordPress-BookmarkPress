function bookmarkpress_visit(post_id){ 

	jQuery(document).ready(function($) {
	
		var data = {
			action: "bookmarkpress_visit",
			post:post_id,
			nonce: bookmarkpress.answerNonce
		};
		
		jQuery.post(bookmarkpress.ajaxurl, data, function(response) {
			document.getElementById('bookmarkpress_visits').innerHTML = response;
		});
	});
	
}

function bookmarkpress_like(post_id){ 

	jQuery(document).ready(function($) {
	
		var data = {
			action: "bookmarkpress_like",
			post:post_id,
			nonce: bookmarkpress.answerNonce
		};
		
		jQuery.post(bookmarkpress.ajaxurl, data, function(response) {
			document.getElementById('bookmarkpress_like').innerHTML = response;
			document.getElementById('bookmarkpress_like_button').innerHTML = "";
		});
	});
	
}