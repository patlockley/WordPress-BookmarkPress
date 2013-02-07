<?php

class BookmarkPostsFilter {
	
	public function __construct() {	
		
		add_filter( 'posts_where' , array($this, 'posts_where') );
		
	}

	function posts_where( $where ) {
			
		global $wpdb;	
		
		if(get_option("bookmark_press_include")){
			
			$where = str_replace("{$wpdb->posts}.post_type = 'post'", "({$wpdb->posts}.post_type = 'post' or {$wpdb->posts}.post_type = 'bookmarkpress')", $where);
		
		}
		
		return $where;
		
	}

} 

$bookmarkPostsFilter = new BookmarkPostsFilter();

?>