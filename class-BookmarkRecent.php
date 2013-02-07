<?php

class BookmarkRecent {
	
	public function __construct() {	
		add_filter( 'the_content', array($this, "the_content")  );
		add_action( 'wp_enqueue_scripts', array($this,'display_javascript') );
	}
	
	function display_javascript($hook) {
	
		wp_register_style( 'bookmarkpress_library_css', plugins_url('/css/bookmarkpress_library.css', __FILE__) );
		wp_enqueue_style( 'bookmarkpress_library_css' );
		
	}
	
	function the_content($content) {
	
		global $post;
	
		if($post->ID==get_option("bookmarkpress_page_recent")){
		
			echo $content;

			global $wpdb;
		
			$table_nameposts = $wpdb->prefix . "posts";
		
			$results = $wpdb->query( 
					"select id, post_title, post_date, guid, post_author FROM " . $table_nameposts . "
					where post_type = 'bookmarkpress' 
					order by post_date DESC"
			);
			
			$output = array();
			
			$details = new BookmarkDetails();
			
			foreach($wpdb->last_result as $post){
				
					array_push($output, $details->make_post_html($post));

			}

			echo implode("\n", $output);
		
		}else{
		
			return $content;
		
		}
		
	}

} 

$bookmarkRecent = new BookmarkRecent();

?>