function bookmarkpress_library_search(extra){ 

	jQuery(document).ready(function($) {
	
		term = document.getElementById("bookmarkpress_search").value;
		
		if(term!=""){
	
			var data = {
				action: "bookmarkpress_library_search",
				term:term,
				extra:extra,
				nonce: bookmarkpress_library.answerNonce
			};
			
			jQuery.post(bookmarkpress.ajaxurl, data, function(response) {
				document.getElementById("bookmarkpress_results").innerHTML=response;
			});
			
		}else{
		
			document.getElementById("bookmarkpress_results").innerHTML = "<p>Please enter a search term</p>";
		
		}
	});
	
}