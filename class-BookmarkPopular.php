<?php

class BookmarkPopular {
	
	public function __construct() {	
		add_filter( 'the_content', array($this, "the_content")  );
		add_action( 'wp_enqueue_scripts', array($this,'display_javascript') );
	}
	
	function display_javascript() {
	
		wp_register_style( 'bookmarkpress_library_css', plugins_url('/css/bookmarkpress_library.css', __FILE__) );
		wp_enqueue_style( 'bookmarkpress_library_css' );
		
	}
	
	function the_content($content) {
	
		global $post;
	
		if($post->ID==get_option("bookmarkpress_page_popular")){
		
			echo $content;

			global $wpdb;
		
			$table_nameposts = $wpdb->prefix . "posts";
			$table_namemeta = $wpdb->prefix . "postmeta";
		
			$results = $wpdb->query( 
					"select id, post_title, post_date, post_author guid FROM " . $table_nameposts . "," . $table_namemeta . "
					where post_type = 'bookmarkpress' and meta_key = '_bookmark_likes' and post_id = ID
					order by meta_value DESC"
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

$bookmarkPopular = new BookmarkPopular();

?>