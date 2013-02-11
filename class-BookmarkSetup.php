<?php

class BookmarkSetup {
	
	public function __construct() {	
		
		if ( is_admin() ) {
			add_action('admin_notices', array($this, 'bookmark_setup_check'));
		}
		
	}
		
	function bookmark_setup_check() {
	
		$url = site_url() . "/wp-admin/options-general.php?page=bookmarkpress";
	
		$string = "";
	
		if(trim(get_option("bookmark_press_name"))===""){
		
			$string .= "<p>You need to set the <strong>Bookmark Name</strong></p>";
			
		}
		
		if(trim(get_option("bookmark_press_description"))===""){
	
			$string .= "<p>You need to set the <strong>Bookmark Description</strong></p>";
			
		}
		
		if(trim(get_option("bookmarkpress_page_library"))===""){
	
			$string .= "<p>You need to set the page for the <strong>Bookmark Library</strong></p>";
			
		}
		
		if(trim(get_option("bookmarkpress_page_recent"))===""){
	
			$string .= "<p>You need to set the page for the <strong>Recent Bookmarks</strong></p>";
			
		}
		
		if(trim(get_option("bookmarkpress_page_popular"))===""){
	
			$string .= "<p>You need to set the page for the <strong>Popular Bookmarks</strong></p>";
			
		}
		
		if($string!=""){
		
			echo "<div class='update-nag' style='margin-top:10px'>";
			
			echo "<h2>BookmarkPress Notice</h2>";

			echo $string;
			
			echo "<p>Please visit the <a href='" . $url . "'>BookmarkPress Management page</a></p>";

			echo "</div>";
			
		}
	
	}

} 

$bookmarkSetup = new BookmarkSetup();

?>